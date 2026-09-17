<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\StudentTransportFee;
use App\Models\Transport\TransportAssignment;
use App\Models\Transport\TransportRoute;
use App\Models\Transport\TransportRouteStop;
use App\Models\Transport\TransportVehicle;
use App\Models\Transport\TransportDriver;
use App\Models\Student;
use App\Models\StudentFeeDue;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TransportFeeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SCHOOL ID
    |--------------------------------------------------------------------------
    */

    protected function schoolId()
    {
        return auth()->user()->school_id;
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = StudentTransportFee::query()
            ->where('school_id', $this->schoolId())
            ->with([
                'assignment.route',
                'assignment.stop',
                'assignment.vehicle',
                'assignment.driver',
                'route',
                'stop',
            ]);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->whereHas(
                    'route',
                    function ($route) use ($search) {

                        $route->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )->orWhere(
                            'code',
                            'like',
                            "%{$search}%"
                        );
                    }
                );

                $q->orWhereHas(
                    'stop',
                    function ($stop) use ($search) {

                        $stop->where(
                            'stop_name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );

                $q->orWhereHas(
                    'assignment',
                    function ($assignment) use ($search) {

                        $assignment->whereHas(
                            'vehicle',
                            function ($vehicle) use ($search) {

                                $vehicle->where(
                                    'vehicle_no',
                                    'like',
                                    "%{$search}%"
                                )->orWhere(
                                    'registration_no',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        );

                        $assignment->orWhereHas(
                            'driver',
                            function ($driver) use ($search) {

                                $driver->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        );
                    }
                );

                $q->orWhereHas(
                    'student',
                    function ($student) use ($search) {

                        $student->where(
                            'first_name',
                            'like',
                            "%{$search}%"
                        )->orWhere(
                            'last_name',
                            'like',
                            "%{$search}%"
                        )->orWhere(
                            'admission_no',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
            });
        }


        if ($request->filled('student_id')) {

            $query->where(
                'student_id',
                $request->student_id
            );
        }


        if ($request->filled('route_id')) {

            $query->where(
                'transport_route_id',
                $request->route_id
            );
        }


        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        $fees = $query
            ->latest('id')
            ->paginate(
                $request->integer('per_page', 20)
            );


        /*
        |--------------------------------------------------------------------------
        | Attach student manually if relationship isn't defined
        |--------------------------------------------------------------------------
        */

        $studentIds = collect($fees->items())
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->values();


        $students = Student::where(
            'school_id',
            $this->schoolId()
        )
        ->whereIn(
            'id',
            $studentIds
        )
        ->get([
            'id',
            'admission_no',
            'first_name',
            'last_name',
            'class_id',
            'section_id',
        ])
        ->keyBy('id');


        foreach ($fees->items() as $fee) {

            $fee->student_data =
                $students->get(
                    $fee->student_id
                );
        }


        return response()->json([
            'success' => true,
            'data' => $fees,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    */

    public function options()
    {
        $schoolId = $this->schoolId();


        $students = Student::where(
            'school_id',
            $schoolId
        )
        ->where(
            'status',
            1
        )
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get([
            'id',
            'admission_no',
            'first_name',
            'last_name',
            'academic_session_id',
            'class_id',
            'section_id',
        ]);


        $assignments =
            TransportAssignment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                'Active'
            )
            ->with([
                'route',
                'stop',
                'vehicle',
                'driver',
            ])
            ->orderBy('id', 'desc')
            ->get();


        $routes = TransportRoute::where(
            'school_id',
            $schoolId
        )
        ->where(
            'status',
            'Active'
        )
        ->orderBy('name')
        ->get([
            'id',
            'name',
            'code',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Transport Fee Type
        |--------------------------------------------------------------------------
        */

        $feeType = FeeType::where(
            'school_id',
            $schoolId
        )
        ->where(
            'code',
            'TRANSPORT'
        )
        ->where(
            'status',
            1
        )
        ->first([
            'id',
            'name',
            'code',
            'frequency',
        ]);


        return response()->json([
            'success' => true,
            'data' => [

                'students' => $students,

                'assignments' => $assignments,

                'routes' => $routes,

                'fee_type' => $feeType,

            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT ASSIGNMENTS
    |--------------------------------------------------------------------------
    */

    public function studentAssignments(
        $studentId
    ) {
        $schoolId = $this->schoolId();


        $student = Student::where(
            'school_id',
            $schoolId
        )
        ->where(
            'id',
            $studentId
        )
        ->first();


        if (!$student) {

            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }


        $assignments =
            TransportAssignment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                'Active'
            )
            ->with([
                'route',
                'stop',
                'vehicle',
                'driver',
            ])
            ->get();


        return response()->json([
            'success' => true,
            'data' => $assignments,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $fee = StudentTransportFee::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->with([
            'assignment.route',
            'assignment.stop',
            'assignment.vehicle',
            'assignment.driver',
            'route',
            'stop',
        ])
        ->first();


        if (!$fee) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport fee configuration not found.',
            ], 404);
        }


        $student = Student::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $fee->student_id
        )
        ->first([
            'id',
            'admission_no',
            'first_name',
            'last_name',
            'class_id',
            'section_id',
            'academic_session_id',
        ]);


        $fee->student_data = $student;


        return response()->json([
            'success' => true,
            'data' => $fee,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();


        $validator = Validator::make(
            $request->all(),
            [

                'student_id' => [
                    'required',
                    'integer',
                ],

                'transport_assignment_id' => [
                    'required',
                    'integer',
                ],

                'amount' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'discount' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'start_date' => [
                    'required',
                    'date',
                ],

                'end_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:start_date',
                ],

                'billing_cycle' => [
                    'required',
                    'in:One Time,Monthly,Quarterly,Half Yearly,Yearly',
                ],

                'status' => [
                    'required',
                    'in:Active,Inactive',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

            ]
        );


        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | STUDENT
        |--------------------------------------------------------------------------
        */

        $student = Student::where(
            'school_id',
            $schoolId
        )
        ->where(
            'id',
            $request->student_id
        )
        ->where(
            'status',
            1
        )
        ->first();


        if (!$student) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid student.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | ASSIGNMENT
        |--------------------------------------------------------------------------
        */

        $assignment =
            TransportAssignment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->transport_assignment_id
            )
            ->where(
                'status',
                'Active'
            )
            ->first();


        if (!$assignment) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid or inactive transport assignment.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | PREVENT DUPLICATE ACTIVE CONFIGURATION
        |--------------------------------------------------------------------------
        */

        $existing =
            StudentTransportFee::where(
                'school_id',
                $schoolId
            )
            ->where(
                'student_id',
                $student->id
            )
            ->where(
                'status',
                'Active'
            )
            ->first();


        if ($existing) {

            return response()->json([
                'success' => false,
                'message' =>
                    'This student already has an active transport fee configuration.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        $fee =
            StudentTransportFee::create([

                'school_id' =>
                    $schoolId,

                'student_id' =>
                    $student->id,

                'transport_assignment_id' =>
                    $assignment->id,

                'transport_route_id' =>
                    $assignment->transport_route_id,

                'transport_route_stop_id' =>
                    $assignment->transport_route_stop_id,

                'amount' =>
                    $request->amount,

                'discount' =>
                    $request->discount ?? 0,

                'start_date' =>
                    $request->start_date,

                'end_date' =>
                    $request->end_date,

                'billing_cycle' =>
                    $request->billing_cycle,

                'status' =>
                    $request->status,

                'remarks' =>
                    $request->remarks,

                'created_by' =>
                    auth()->id(),

            ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Student transport fee configuration created successfully.',
            'data' => $fee,
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {
        $schoolId = $this->schoolId();


        $fee =
            StudentTransportFee::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $id
            )
            ->first();


        if (!$fee) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport fee configuration not found.',
            ], 404);
        }


        $validator = Validator::make(
            $request->all(),
            [

                'student_id' => [
                    'required',
                    'integer',
                ],

                'transport_assignment_id' => [
                    'required',
                    'integer',
                ],

                'amount' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'discount' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'start_date' => [
                    'required',
                    'date',
                ],

                'end_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:start_date',
                ],

                'billing_cycle' => [
                    'required',
                    'in:One Time,Monthly,Quarterly,Half Yearly,Yearly',
                ],

                'status' => [
                    'required',
                    'in:Active,Inactive',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

            ]
        );


        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }


        $student = Student::where(
            'school_id',
            $schoolId
        )
        ->where(
            'id',
            $request->student_id
        )
        ->where(
            'status',
            1
        )
        ->first();


        if (!$student) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid student.',
            ], 422);
        }


        $assignment =
            TransportAssignment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->transport_assignment_id
            )
            ->where(
                'status',
                'Active'
            )
            ->first();


        if (!$assignment) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid or inactive transport assignment.',
            ], 422);
        }


        $duplicate =
            StudentTransportFee::where(
                'school_id',
                $schoolId
            )
            ->where(
                'student_id',
                $student->id
            )
            ->where(
                'status',
                'Active'
            )
            ->where(
                'id',
                '!=',
                $fee->id
            )
            ->exists();


        if (
            $duplicate &&
            $request->status === 'Active'
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'This student already has another active transport fee configuration.',
            ], 422);
        }


        $fee->update([

            'student_id' =>
                $student->id,

            'transport_assignment_id' =>
                $assignment->id,

            'transport_route_id' =>
                $assignment->transport_route_id,

            'transport_route_stop_id' =>
                $assignment->transport_route_stop_id,

            'amount' =>
                $request->amount,

            'discount' =>
                $request->discount ?? 0,

            'start_date' =>
                $request->start_date,

            'end_date' =>
                $request->end_date,

            'billing_cycle' =>
                $request->billing_cycle,

            'status' =>
                $request->status,

            'remarks' =>
                $request->remarks,

            'updated_by' =>
                auth()->id(),

        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Student transport fee configuration updated successfully.',
            'data' => $fee,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $fee =
            StudentTransportFee::where(
                'school_id',
                $this->schoolId()
            )
            ->where(
                'id',
                $id
            )
            ->first();


        if (!$fee) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport fee configuration not found.',
            ], 404);
        }


        $fee->delete();


        return response()->json([
            'success' => true,
            'message' =>
                'Transport fee configuration deleted successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE FEES
    |--------------------------------------------------------------------------
    */

    public function generate(
        Request $request
    ) {
        $validator = Validator::make(
            $request->all(),
            [

                'month' => [
                    'required',
                    'date_format:Y-m',
                ],

                'due_day' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'max:28',
                ],

            ]
        );


        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }


        $schoolId = $this->schoolId();

        $month = Carbon::createFromFormat(
            'Y-m',
            $request->month
        );


        /*
        |--------------------------------------------------------------------------
        | TRANSPORT FEE TYPE
        |--------------------------------------------------------------------------
        */

        $feeType = FeeType::where(
            'school_id',
            $schoolId
        )
        ->where(
            'code',
            'TRANSPORT'
        )
        ->where(
            'status',
            1
        )
        ->first();


        if (!$feeType) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport Fee type not found. Create fee type with code TRANSPORT first.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIVE TRANSPORT CONFIGURATIONS
        |--------------------------------------------------------------------------
        */

        $configs =
            StudentTransportFee::where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                'Active'
            )
            ->whereDate(
                'start_date',
                '<=',
                $month->copy()->endOfMonth()
            )
            ->where(function ($query) use ($month) {

                $query->whereNull(
                    'end_date'
                )->orWhereDate(
                    'end_date',
                    '>=',
                    $month->copy()->startOfMonth()
                );

            })
            ->get();


        $created = 0;

        $skipped = 0;

        $errors = [];


        DB::beginTransaction();


        try {

            foreach ($configs as $config) {

                $student =
                    Student::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $config->student_id
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->first();


                if (!$student) {

                    $skipped++;

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | DUPLICATE CHECK
                |--------------------------------------------------------------------------
                */

                $period =
                    $month->format('F Y');


                $alreadyExists =
                    StudentFeeDue::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'student_id',
                        $student->id
                    )
                    ->where(
                        'fee_type_id',
                        $feeType->id
                    )
                    ->where(
                        'period',
                        $period
                    )
                    ->whereNull(
                        'deleted_at'
                    )
                    ->exists();


                if ($alreadyExists) {

                    $skipped++;

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | AMOUNTS
                |--------------------------------------------------------------------------
                */

                $amount =
                    (float) $config->amount;

                $discount =
                    (float) $config->discount;

                $lateFee = 0;


                $netAmount =
                    max(
                        0,
                        $amount -
                        $discount +
                        $lateFee
                    );


                /*
                |--------------------------------------------------------------------------
                | DUE DATE
                |--------------------------------------------------------------------------
                */

                $dueDay =
                    $request->due_day
                    ?: $month->daysInMonth;


                $dueDay = min(
                    (int) $dueDay,
                    $month->daysInMonth
                );


                $dueDate =
                    $month->copy()
                        ->day($dueDay);


                /*
                |--------------------------------------------------------------------------
                | CREATE STUDENT FEE DUE
                |--------------------------------------------------------------------------
                */

                StudentFeeDue::create([

                    'school_id' =>
                        $schoolId,

                    'academic_session_id' =>
                        $student->academic_session_id,

                    'student_id' =>
                        $student->id,

                    'class_id' =>
                        $student->class_id,

                    'section_id' =>
                        $student->section_id,

                    'fee_type_id' =>
                        $feeType->id,

                    'fee_structure_id' =>
                        null,

                    'fee_date' =>
                        $month->copy()
                            ->startOfMonth()
                            ->format('Y-m-d'),

                    'due_date' =>
                        $dueDate->format('Y-m-d'),

                    'period' =>
                        $period,

                    'amount' =>
                        $amount,

                    'discount' =>
                        $discount,

                    'late_fee' =>
                        $lateFee,

                    'net_amount' =>
                        $netAmount,

                    'paid_amount' =>
                        0,

                    'balance_amount' =>
                        $netAmount,

                    'status' =>
                        'Pending',

                    'remarks' =>
                        'Transport Fee - ' .
                        $period,

                    'created_by' =>
                        auth()->id(),

                ]);


                $created++;
            }


            DB::commit();


        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to generate transport fees.',
                'error' =>
                    $e->getMessage(),
            ], 500);
        }


        return response()->json([
            'success' => true,
            'message' =>
                "Transport fees generated successfully. Created: {$created}, Skipped: {$skipped}.",
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }
}