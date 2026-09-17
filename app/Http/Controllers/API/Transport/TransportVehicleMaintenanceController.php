<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\TransportVehicleMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransportVehicleMaintenanceController extends Controller
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
        $schoolId = $this->schoolId();

        $query = DB::table('transport_vehicle_maintenances as m')
            ->leftJoin(
                'transport_vehicles as v',
                'v.id',
                '=',
                'm.transport_vehicle_id'
            )
            ->where('m.school_id', $schoolId)
            ->whereNull('m.deleted_at')
            ->select([
                'm.*',

                'v.vehicle_no',
                'v.registration_no',
                'v.vehicle_type',
                'v.model',
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
                    'm.maintenance_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'm.maintenance_type',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'm.service_provider',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'm.description',
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
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | VEHICLE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('transport_vehicle_id')) {

            $query->where(
                'm.transport_vehicle_id',
                $request->transport_vehicle_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TYPE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('maintenance_type')) {

            $query->where(
                'm.maintenance_type',
                $request->maintenance_type
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'm.status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'm.maintenance_date',
                '>=',
                $request->date_from
            );
        }


        if ($request->filled('date_to')) {

            $query->whereDate(
                'm.maintenance_date',
                '<=',
                $request->date_to
            );
        }


        $maintenance = $query
            ->orderByDesc('m.maintenance_date')
            ->orderByDesc('m.id')
            ->paginate(
                $request->integer('per_page', 20)
            );


        return response()->json([
            'success' => true,
            'data' => $maintenance,
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
            ->where('school_id', $schoolId)
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


        return response()->json([
            'success' => true,

            'data' => [

                'vehicles' => $vehicles,

                'maintenance_types' => [

                    'Regular Service',
                    'Oil Change',
                    'Tyre',
                    'Battery',
                    'Brake',
                    'Engine',
                    'AC',
                    'Electrical',
                    'Repair',
                    'Insurance',
                    'Fitness',
                    'Pollution',
                    'Other',

                ],

                'statuses' => [

                    'Scheduled',
                    'In Progress',
                    'Completed',
                    'Cancelled',

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


        $maintenance = DB::table(
            'transport_vehicle_maintenances as m'
        )
        ->leftJoin(
            'transport_vehicles as v',
            'v.id',
            '=',
            'm.transport_vehicle_id'
        )
        ->where(
            'm.school_id',
            $schoolId
        )
        ->where(
            'm.id',
            $id
        )
        ->whereNull(
            'm.deleted_at'
        )
        ->select([
            'm.*',

            'v.vehicle_no',
            'v.registration_no',
            'v.vehicle_type',
            'v.model',
            'v.capacity',
        ])
        ->first();


        if (!$maintenance) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Maintenance record not found.',
            ], 404);
        }


        return response()->json([
            'success' => true,
            'data' => $maintenance,
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
                    'required',
                    'integer',
                ],

                'maintenance_date' => [
                    'required',
                    'date',
                ],

                'maintenance_type' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'odometer' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'service_provider' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],

                'cost' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'next_service_date' => [
                    'nullable',
                    'date',
                ],

                'next_service_odometer' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'status' => [
                    'required',
                    'in:Scheduled,In Progress,Completed,Cancelled',
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
        | VEHICLE
        |--------------------------------------------------------------------------
        */

        $vehicle = DB::table('transport_vehicles')
            ->where('school_id', $schoolId)
            ->where('id', $request->transport_vehicle_id)
            ->whereNull('deleted_at')
            ->first();


        if (!$vehicle) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid vehicle.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | MAINTENANCE NUMBER
        |--------------------------------------------------------------------------
        */

        $maintenanceNo =
            $this->generateMaintenanceNo(
                $schoolId
            );


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        $maintenance =
            TransportVehicleMaintenance::create([

                'school_id' =>
                    $schoolId,

                'transport_vehicle_id' =>
                    $request->transport_vehicle_id,

                'maintenance_no' =>
                    $maintenanceNo,

                'maintenance_date' =>
                    $request->maintenance_date,

                'maintenance_type' =>
                    $request->maintenance_type,

                'odometer' =>
                    $request->odometer,

                'service_provider' =>
                    $request->service_provider,

                'description' =>
                    $request->description,

                'cost' =>
                    $request->cost ?? 0,

                'next_service_date' =>
                    $request->next_service_date,

                'next_service_odometer' =>
                    $request->next_service_odometer,

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
                'Vehicle maintenance record created successfully.',
            'data' => $maintenance,
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


        $maintenance =
            TransportVehicleMaintenance::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $id
            )
            ->first();


        if (!$maintenance) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Maintenance record not found.',
            ], 404);
        }


        $validator = Validator::make(
            $request->all(),
            [

                'transport_vehicle_id' => [
                    'required',
                    'integer',
                ],

                'maintenance_date' => [
                    'required',
                    'date',
                ],

                'maintenance_type' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'odometer' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'service_provider' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable','string',
                ],

                'cost' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'next_service_date' => [
                    'nullable',
                    'date',
                ],

                'next_service_odometer' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'status' => [
                    'required',
                    'in:Scheduled,In Progress,Completed,Cancelled',
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


        $vehicle = DB::table('transport_vehicles')
            ->where('school_id', $schoolId)
            ->where('id', $request->transport_vehicle_id)
            ->whereNull('deleted_at')
            ->first();


        if (!$vehicle) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid vehicle.',
            ], 422);
        }


        $maintenance->update([

            'transport_vehicle_id' =>
                $request->transport_vehicle_id,

            'maintenance_date' =>
                $request->maintenance_date,

            'maintenance_type' =>
                $request->maintenance_type,

            'odometer' =>
                $request->odometer,

            'service_provider' =>
                $request->service_provider,

            'description' =>
                $request->description,

            'cost' =>
                $request->cost ?? 0,

            'next_service_date' =>
                $request->next_service_date,

            'next_service_odometer' =>
                $request->next_service_odometer,

            'status' =>
                $request->status,

            'remarks' =>
                $request->remarks,

        ]);


        return response()->json([
            'success' => true,
            'message' =>
                'Vehicle maintenance record updated successfully.',
            'data' => $maintenance,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $maintenance =
            TransportVehicleMaintenance::where(
                'school_id',
                $this->schoolId()
            )
            ->where(
                'id',
                $id
            )
            ->first();


        if (!$maintenance) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Maintenance record not found.',
            ], 404);
        }


        $maintenance->delete();


        return response()->json([
            'success' => true,
            'message' =>
                'Vehicle maintenance record deleted successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE MAINTENANCE NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateMaintenanceNo(
        $schoolId
    ) {
        $prefix = 'VM-' . date('Ym') . '-';


        $last = DB::table(
            'transport_vehicle_maintenances'
        )
        ->where(
            'school_id',
            $schoolId
        )
        ->where(
            'maintenance_no',
            'like',
            $prefix . '%'
        )
        ->orderByDesc('id')
        ->value('maintenance_no');


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