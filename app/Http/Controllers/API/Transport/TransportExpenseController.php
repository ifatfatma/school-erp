<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\TransportExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransportExpenseController extends Controller
{
    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = DB::table('transport_expenses as e')
            ->leftJoin(
                'transport_vehicles as v',
                'v.id',
                '=',
                'e.transport_vehicle_id'
            )
            ->leftJoin(
                'transport_drivers as d',
                'd.id',
                '=',
                'e.transport_driver_id'
            )
            ->where('e.school_id', $schoolId)
            ->whereNull('e.deleted_at')
            ->select([
                'e.*',

                'v.vehicle_no',
                'v.registration_no',

                'd.name as driver_name',
            ]);


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'e.expense_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'e.expense_type',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'e.vendor',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'e.reference_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'v.vehicle_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'v.registration_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'd.name',
                    'like',
                    "%{$search}%"
                );
            });
        }


        if ($request->filled('transport_vehicle_id')) {

            $query->where(
                'e.transport_vehicle_id',
                $request->transport_vehicle_id
            );
        }


        if ($request->filled('transport_driver_id')) {

            $query->where(
                'e.transport_driver_id',
                $request->transport_driver_id
            );
        }


        if ($request->filled('expense_type')) {

            $query->where(
                'e.expense_type',
                $request->expense_type
            );
        }


        if ($request->filled('date_from')) {

            $query->whereDate(
                'e.expense_date',
                '>=',
                $request->date_from
            );
        }


        if ($request->filled('date_to')) {

            $query->whereDate(
                'e.expense_date',
                '<=',
                $request->date_to
            );
        }


        $expenses = $query
            ->orderByDesc('e.expense_date')
            ->orderByDesc('e.id')
            ->paginate(
                $request->integer('per_page', 20)
            );


        return response()->json([
            'success' => true,
            'data' => $expenses,
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


        $vehicles = DB::table('transport_vehicles')
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->whereNull('deleted_at')
            ->orderBy('vehicle_no')
            ->get([
                'id',
                'vehicle_no',
                'registration_no',
                'vehicle_type',
                'model',
                'status',
            ]);


        $drivers = DB::table('transport_drivers')
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
                'phone',
                'status',
            ]);


        return response()->json([
            'success' => true,

            'data' => [

                'vehicles' => $vehicles,

                'drivers' => $drivers,

                'expense_types' => [

                    'Fuel',
                    'Diesel',
                    'Petrol',
                    'CNG',
                    'Engine Oil',
                    'Toll',
                    'Parking',
                    'Repair',
                    'Spare Parts',
                    'Driver Expense',
                    'Cleaning',
                    'Permit',
                    'Insurance',
                    'Tax',
                    'Other',

                ],

                'payment_methods' => [

                    'Cash',
                    'Bank',
                    'UPI',
                    'Card',
                    'Cheque',
                    'Other',

                ],

            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $schoolId = $this->schoolId();


        $expense = DB::table('transport_expenses as e')
            ->leftJoin(
                'transport_vehicles as v',
                'v.id',
                '=',
                'e.transport_vehicle_id'
            )
            ->leftJoin(
                'transport_drivers as d',
                'd.id',
                '=',
                'e.transport_driver_id'
            )
            ->where('e.school_id', $schoolId)
            ->where('e.id', $id)
            ->whereNull('e.deleted_at')
            ->select([
                'e.*',

                'v.vehicle_no',
                'v.registration_no',
                'v.vehicle_type',

                'd.name as driver_name',
                'd.phone as driver_phone',
            ])
            ->first();


        if (!$expense) {

            return response()->json([
                'success' => false,
                'message' => 'Expense record not found.',
            ], 404);
        }


        return response()->json([
            'success' => true,
            'data' => $expense,
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

                'transport_vehicle_id' => [
                    'nullable',
                    'integer',
                ],

                'transport_driver_id' => [
                    'nullable',
                    'integer',
                ],

                'expense_date' => [
                    'required',
                    'date',
                ],

                'expense_type' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'quantity' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'unit' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'rate' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'amount' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'vendor' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'payment_method' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'reference_no' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
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
        | VERIFY VEHICLE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('transport_vehicle_id')) {

            $vehicleExists = DB::table('transport_vehicles')
                ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
                ->where('id', $request->transport_vehicle_id)
                ->whereNull('deleted_at')
                ->exists();


            if (!$vehicleExists) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid vehicle.',
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY DRIVER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('transport_driver_id')) {

            $driverExists = DB::table('transport_drivers')
                ->when($schoolId, fn($q, $id) => $q->where('school_id', $id))
                ->where('id', $request->transport_driver_id)
                ->whereNull('deleted_at')
                ->exists();


            if (!$driverExists) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid driver.',
                ], 422);
            }
        }


        $expense = TransportExpense::create([

            'school_id' =>
                $schoolId,

            'transport_vehicle_id' =>
                $request->transport_vehicle_id,

            'transport_driver_id' =>
                $request->transport_driver_id,

            'expense_no' =>
                $this->generateExpenseNo(
                    $schoolId
                ),

            'expense_date' =>
                $request->expense_date,

            'expense_type' =>
                $request->expense_type,

            'quantity' =>
                $request->quantity,

            'unit' =>
                $request->unit,

            'rate' =>
                $request->rate ?? 0,

            'amount' =>
                $request->amount ?? 0,

            'vendor' =>
                $request->vendor,

            'payment_method' =>
                $request->payment_method,

            'reference_no' =>
                $request->reference_no,

            'description' =>
                $request->description,

            'remarks' =>
                $request->remarks,

            'created_by' =>
                auth()->id(),

        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Transport expense created successfully.',
            'data' => $expense,
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


        $expense = TransportExpense::where(
            'school_id',
            $schoolId
        )
        ->where(
            'id',
            $id
        )
        ->first();


        if (!$expense) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Expense record not found.',
            ], 404);
        }


        $validator = Validator::make(
            $request->all(),
            [

                'transport_vehicle_id' => [
                    'nullable',
                    'integer',
                ],

                'transport_driver_id' => [
                    'nullable',
                    'integer',
                ],

                'expense_date' => [
                    'required',
                    'date',
                ],

                'expense_type' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'quantity' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'unit' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'rate' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'amount' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'vendor' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'payment_method' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'reference_no' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
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


        $expense->update([

            'transport_vehicle_id' =>
                $request->transport_vehicle_id,

            'transport_driver_id' =>
                $request->transport_driver_id,

            'expense_date' =>
                $request->expense_date,

            'expense_type' =>
                $request->expense_type,

            'quantity' =>
                $request->quantity,

            'unit' =>
                $request->unit,

            'rate' =>
                $request->rate ?? 0,

            'amount' =>
                $request->amount ?? 0,

            'vendor' =>
                $request->vendor,

            'payment_method' =>
                $request->payment_method,

            'reference_no' =>
                $request->reference_no,

            'description' =>
                $request->description,

            'remarks' =>
                $request->remarks,

        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Transport expense updated successfully.',
            'data' => $expense,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $expense = TransportExpense::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();


        if (!$expense) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Expense record not found.',
            ], 404);
        }


        $expense->delete();


        return response()->json([
            'success' => true,
            'message' =>
                'Transport expense deleted successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateExpenseNo(
        $schoolId
    ) {
        $prefix =
            'EXP-' . date('Ym') . '-';


        $last = DB::table(
            'transport_expenses'
        )
        ->where(
            'school_id',
            $schoolId
        )
        ->where(
            'expense_no',
            'like',
            $prefix . '%'
        )
        ->orderByDesc('id')
        ->value('expense_no');


        if ($last) {

            $number =
                (int) substr(
                    $last,
                    strlen($prefix)
                );

            $number++;

        } else {

            $number = 1;

        }


        return $prefix .
            str_pad(
                $number,
                5,
                '0',
                STR_PAD_LEFT
            );
    }
}