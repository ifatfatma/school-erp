<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\TransportAssignment;
use App\Models\Transport\TransportRoute;
use App\Models\Transport\TransportRouteStop;
use App\Models\Transport\TransportVehicle;
use App\Models\Transport\TransportDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportAssignmentController extends Controller
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
        $query = TransportAssignment::where(
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

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->whereHas(
                    'route',
                    function ($route) use ($search) {

                        $route->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'code',
                            'like',
                            "%{$search}%"
                        );

                    }
                );

                $q->orWhereHas(
                    'vehicle',
                    function ($vehicle) use ($search) {

                        $vehicle->where(
                            'vehicle_no',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'registration_no',
                            'like',
                            "%{$search}%"
                        );

                    }
                );

                $q->orWhereHas(
                    'driver',
                    function ($driver) use ($search) {

                        $driver->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'code',
                            'like',
                            "%{$search}%"
                        );

                    }
                );

            });
        }

        if ($request->filled('route_id')) {

            $query->where(
                'transport_route_id',
                $request->route_id
            );
        }

        if ($request->filled('vehicle_id')) {

            $query->where(
                'transport_vehicle_id',
                $request->vehicle_id
            );
        }

        if ($request->filled('driver_id')) {

            $query->where(
                'transport_driver_id',
                $request->driver_id
            );
        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        $assignments = $query
            ->latest('id')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $assignments,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    */

    public function options()
    {
        $schoolId =
            $this->schoolId();

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

        $vehicles = TransportVehicle::where(
            'school_id',
            $schoolId
        )
        ->where(
            'status',
            'Active'
        )
        ->orderBy('vehicle_no')
        ->get([
            'id',
            'vehicle_no',
            'registration_no',
            'vehicle_type',
            'capacity',
        ]);

        $drivers = TransportDriver::where(
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
            'phone',
            'license_no',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'routes' => $routes,
                'vehicles' => $vehicles,
                'drivers' => $drivers,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ROUTE STOPS
    |--------------------------------------------------------------------------
    */

    public function routeStops(
        $routeId
    ) {
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
        ->get([
            'id',
            'transport_route_id',
            'stop_name',
            'stop_code',
            'stop_order',
            'pickup_time',
            'drop_time',
            'fare',
        ]);

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
        $assignment =
            TransportAssignment::where(
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

        if (!$assignment) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport assignment not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $assignment,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {
        $schoolId =
            $this->schoolId();

        $validator = Validator::make(
            $request->all(),
            [

                'transport_route_id' => [
                    'required',
                    'integer',
                ],

                'transport_route_stop_id' => [
                    'nullable',
                    'integer',
                ],

                'transport_vehicle_id' => [
                    'required',
                    'integer',
                ],

                'transport_driver_id' => [
                    'required',
                    'integer',
                ],

                'assignment_date' => [
                    'nullable',
                    'date',
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

            $stop =
                TransportRouteStop::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $request->transport_route_stop_id
                )
                ->where(
                    'transport_route_id',
                    $request->transport_route_id
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

        $vehicle =
            TransportVehicle::where(
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


        /*
        |--------------------------------------------------------------------------
        | Verify Driver
        |--------------------------------------------------------------------------
        */

        $driver =
            TransportDriver::where(
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


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        $assignment =
            TransportAssignment::create([

                'school_id' =>
                    $schoolId,

                'transport_route_id' =>
                    $request->transport_route_id,

                'transport_route_stop_id' =>
                    $request->transport_route_stop_id,

                'transport_vehicle_id' =>
                    $request->transport_vehicle_id,

                'transport_driver_id' =>
                    $request->transport_driver_id,

                'assignment_date' =>
                    $request->assignment_date,

                'start_date' =>
                    $request->start_date,

                'end_date' =>
                    $request->end_date,

                'status' =>
                    $request->status,

                'remarks' =>
                    $request->remarks,

            ]);

        $assignment->load([
            'route',
            'stop',
            'vehicle',
            'driver',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Transport assignment created successfully.',
            'data' => $assignment,
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
        $assignment =
            TransportAssignment::where(
                'school_id',
                $this->schoolId()
            )
            ->where(
                'id',
                $id
            )
            ->first();

        if (!$assignment) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport assignment not found.',
            ], 404);
        }


        $validator = Validator::make(
            $request->all(),
            [

                'transport_route_id' => [
                    'required',
                    'integer',
                ],

                'transport_route_stop_id' => [
                    'nullable',
                    'integer',
                ],

                'transport_vehicle_id' => [
                    'required',
                    'integer',
                ],

                'transport_driver_id' => [
                    'required',
                    'integer',
                ],

                'assignment_date' => [
                    'nullable',
                    'date',
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
                'message' =>
                    'Validation failed.',
                'errors' =>
                    $validator->errors(),
            ], 422);
        }


        $schoolId =
            $this->schoolId();


        /*
        |--------------------------------------------------------------------------
        | Validate Route
        |--------------------------------------------------------------------------
        */

        $route =
            TransportRoute::where(
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
        | Validate Stop
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'transport_route_stop_id'
            )
        ) {

            $stop =
                TransportRouteStop::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $request->transport_route_stop_id
                )
                ->where(
                    'transport_route_id',
                    $request->transport_route_id
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
        | Validate Vehicle
        |--------------------------------------------------------------------------
        */

        $vehicle =
            TransportVehicle::where(
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


        /*
        |--------------------------------------------------------------------------
        | Validate Driver
        |--------------------------------------------------------------------------
        */

        $driver =
            TransportDriver::where(
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


        $assignment->update([

            'transport_route_id' =>
                $request->transport_route_id,

            'transport_route_stop_id' =>
                $request->transport_route_stop_id,

            'transport_vehicle_id' =>
                $request->transport_vehicle_id,

            'transport_driver_id' =>
                $request->transport_driver_id,

            'assignment_date' =>
                $request->assignment_date,

            'start_date' =>
                $request->start_date,

            'end_date' =>
                $request->end_date,

            'status' =>
                $request->status,

            'remarks' =>
                $request->remarks,

        ]);

        $assignment->load([
            'route',
            'stop',
            'vehicle',
            'driver',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Transport assignment updated successfully.',
            'data' => $assignment,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $assignment =
            TransportAssignment::where(
                'school_id',
                $this->schoolId()
            )
            ->where(
                'id',
                $id
            )
            ->first();

        if (!$assignment) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport assignment not found.',
            ], 404);
        }

        $assignment->delete();

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
        $assignment =
            TransportAssignment::where(
                'school_id',
                $this->schoolId()
            )
            ->where(
                'id',
                $id
            )
            ->first();

        if (!$assignment) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transport assignment not found.',
            ], 404);
        }

        $request->validate([
            'status' =>
                'required|in:Active,Inactive',
        ]);

        $assignment->update([
            'status' =>
                $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Assignment status updated successfully.',
            'data' => $assignment,
        ]);
    }
}