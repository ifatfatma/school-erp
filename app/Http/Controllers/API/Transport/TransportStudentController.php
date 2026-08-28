<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\TransportStudent;
use App\Models\Transport\TransportRoute;
use App\Models\Transport\TransportRouteStop;
use App\Models\Transport\TransportVehicle;
use App\Models\Transport\TransportDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransportStudentController extends Controller
{
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
        $query = TransportStudent::where(
            'school_id',
            $this->schoolId()
        )
        ->with([
            'route',
            'stop',
            'vehicle',
            'driver',
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
                        );
                    }
                )
                ->orWhereHas(
                    'vehicle',
                    function ($vehicle) use ($search) {
                        $vehicle->where(
                            'vehicle_no',
                            'like',
                            "%{$search}%"
                        );
                    }
                )
                ->orWhereHas(
                    'driver',
                    function ($driver) use ($search) {
                        $driver->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                )
                ->orWhere(
                    'student_id',
                    $search
                );

            });
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

        $records = $query
            ->latest('id')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $records,
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

        $routes = TransportRoute::where(
            'school_id',
            $schoolId
        )
        ->where(
            'status',
            'Active'
        )
        ->with([
            'stops'
        ])
        ->orderBy('name')
        ->get();

        $vehicles = TransportVehicle::where(
            'school_id',
            $schoolId
        )
        ->where(
            'status',
            'Active'
        )
        ->orderBy('vehicle_no')
        ->get();

        $drivers = TransportDriver::where(
            'school_id',
            $schoolId
        )
        ->where(
            'status',
            'Active'
        )
        ->orderBy('name')
        ->get();

        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        |
        | Change this query if your actual student table/model uses
        | different column names.
        |
        */

        $students = DB::table('students')
            ->where(
                'school_id',
                $schoolId
            )
            ->orderBy(
                'id',
                'desc'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Academic Sessions
        |--------------------------------------------------------------------------
        */

        $sessions = DB::table(
            'school_sessions'
        )
        ->where(
            'school_id',
            $schoolId
        )
        ->orderBy(
            'session_year',
            'desc'
        )
        ->get();

        return response()->json([
            'success' => true,

            'data' => [
                'students' => $students,
                'routes' => $routes,
                'vehicles' => $vehicles,
                'drivers' => $drivers,
                'sessions' => $sessions,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ROUTE STOPS
    |--------------------------------------------------------------------------
    */

    public function routeStops($routeId)
    {
        $route = TransportRoute::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $routeId
        )
        ->first();

        if (!$route) {

            return response()->json([
                'success' => false,
                'message' => 'Route not found.',
            ], 404);
        }

        $stops = TransportRouteStop::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'transport_route_id',
            $routeId
        )
        ->orderBy(
            'stop_order'
        )
        ->get();

        return response()->json([
            'success' => true,
            'data' => $stops,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $record = TransportStudent::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->with([
            'route',
            'stop',
            'vehicle',
            'driver',
        ])
        ->first();

        if (!$record) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport assignment not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $record,
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

                'transport_route_id' => [
                    'required',
                    'integer',
                ],

                'transport_route_stop_id' => [
                    'nullable',
                    'integer',
                ],

                'transport_vehicle_id' => [
                    'nullable',
                    'integer',
                ],

                'transport_driver_id' => [
                    'nullable',
                    'integer',
                ],

                'academic_session' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'pickup_type' => [
                    'required',
                    'in:Pickup,Drop,Both',
                ],

                'transport_fee' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'start_date' => [
                    'nullable',
                    'date',
                ],

                'end_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:start_date',
                ],

                'status' => [
                    'nullable',
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
                'message' =>
                    'Validation failed.',
                'errors' =>
                    $validator->errors(),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Route
        |--------------------------------------------------------------------------
        */

        $route = TransportRoute::where(
            'school_id',
            $schoolId
        )
        ->where(
            'id',
            $request->transport_route_id
        )
        ->first();

        if (!$route) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid transport route.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Stop
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'transport_route_stop_id'
            )
        ) {

            $stop = TransportRouteStop::where(
                'school_id',
                $schoolId
            )
            ->where(
                'transport_route_id',
                $route->id
            )
            ->where(
                'id',
                $request->transport_route_stop_id
            )
            ->first();

            if (!$stop) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Invalid route stop.',
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Vehicle
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'transport_vehicle_id'
            )
        ) {

            $vehicle = TransportVehicle::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->transport_vehicle_id
            )
            ->where(
                'status',
                'Active'
            )
            ->first();

            if (!$vehicle) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Invalid or inactive vehicle.',
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Driver
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'transport_driver_id'
            )
        ) {

            $driver = TransportDriver::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->transport_driver_id
            )
            ->where(
                'status',
                'Active'
            )
            ->first();

            if (!$driver) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Invalid or inactive driver.',
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Active Assignment
        |--------------------------------------------------------------------------
        */

        $duplicate = TransportStudent::where(
            'school_id',
            $schoolId
        )
        ->where(
            'student_id',
            $request->student_id
        )
        ->where(
            'academic_session',
            $request->academic_session
        )
        ->where(
            'status',
            'Active'
        )
        ->exists();

        if ($duplicate) {

            return response()->json([
                'success' => false,
                'message' =>
                    'This student already has an active transport assignment for this session.',
            ], 422);
        }

        $record = TransportStudent::create([

            'school_id' =>
                $schoolId,

            'student_id' =>
                $request->student_id,

            'transport_route_id' =>
                $request->transport_route_id,

            'transport_route_stop_id' =>
                $request->transport_route_stop_id,

            'transport_vehicle_id' =>
                $request->transport_vehicle_id,

            'transport_driver_id' =>
                $request->transport_driver_id,

            'academic_session' =>
                $request->academic_session,

            'pickup_type' =>
                $request->pickup_type,

            'transport_fee' =>
                $request->transport_fee ?? 0,

            'start_date' =>
                $request->start_date,

            'end_date' =>
                $request->end_date,

            'status' =>
                $request->status ?: 'Active',

            'remarks' =>
                $request->remarks,

        ]);

        $record->load([
            'route',
            'stop',
            'vehicle',
            'driver',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Student transport assignment created successfully.',
            'data' => $record,
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
        $record = TransportStudent::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$record) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport assignment not found.',
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [

                'student_id' => [
                    'required',
                    'integer',
                ],

                'transport_route_id' => [
                    'required',
                    'integer',
                ],

                'transport_route_stop_id' => [
                    'nullable',
                    'integer',
                ],

                'transport_vehicle_id' => [
                    'nullable',
                    'integer',
                ],

                'transport_driver_id' => [
                    'nullable',
                    'integer',
                ],

                'academic_session' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'pickup_type' => [
                    'required',
                    'in:Pickup,Drop,Both',
                ],

                'transport_fee' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'start_date' => [
                    'nullable',
                    'date',
                ],

                'end_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:start_date',
                ],

                'status' => [
                    'nullable',
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
                'message' =>
                    'Validation failed.',
                'errors' =>
                    $validator->errors(),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Route
        |--------------------------------------------------------------------------
        */

        $route = TransportRoute::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $request->transport_route_id
        )
        ->first();

        if (!$route) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid transport route.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Stop
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'transport_route_stop_id'
            )
        ) {

            $stop = TransportRouteStop::where(
                'school_id',
                $this->schoolId()
            )
            ->where(
                'transport_route_id',
                $route->id
            )
            ->where(
                'id',
                $request->transport_route_stop_id
            )
            ->first();

            if (!$stop) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Invalid route stop.',
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Check
        |--------------------------------------------------------------------------
        */

        $duplicate = TransportStudent::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'student_id',
            $request->student_id
        )
        ->where(
            'academic_session',
            $request->academic_session
        )
        ->where(
            'status',
            'Active'
        )
        ->where(
            'id',
            '!=',
            $id
        )
        ->exists();

        if ($duplicate) {

            return response()->json([
                'success' => false,
                'message' =>
                    'This student already has another active transport assignment for this session.',
            ], 422);
        }

        $record->update([

            'student_id' =>
                $request->student_id,

            'transport_route_id' =>
                $request->transport_route_id,

            'transport_route_stop_id' =>
                $request->transport_route_stop_id,

            'transport_vehicle_id' =>
                $request->transport_vehicle_id,

            'transport_driver_id' =>
                $request->transport_driver_id,

            'academic_session' =>
                $request->academic_session,

            'pickup_type' =>
                $request->pickup_type,

            'transport_fee' =>
                $request->transport_fee ?? 0,

            'start_date' =>
                $request->start_date,

            'end_date' =>
                $request->end_date,

            'status' =>
                $request->status ?: 'Active',

            'remarks' =>
                $request->remarks,

        ]);

        $record->load([
            'route',
            'stop',
            'vehicle',
            'driver',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Student transport assignment updated successfully.',
            'data' => $record,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $record = TransportStudent::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$record) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport assignment not found.',
            ], 404);
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Transport assignment deleted successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public function status(
        Request $request,
        $id
    ) {
        $record = TransportStudent::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$record) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport assignment not found.',
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'status' => [
                    'required',
                    'in:Active,Inactive',
                ],
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Invalid status.',
                'errors' =>
                    $validator->errors(),
            ], 422);
        }

        $record->update([
            'status' =>
                $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Transport assignment status updated successfully.',
            'data' => $record,
        ]);
    }
}