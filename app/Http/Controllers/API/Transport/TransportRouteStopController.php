<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\TransportRouteStop;
use App\Models\Transport\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportRouteStopController extends Controller
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
        $query = TransportRouteStop::where(
            'school_id',
            $this->schoolId()
        )->with('route');

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'stop_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'stop_code',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'route',
                    function ($route) use ($search) {
                        $route->where(
                            'name',
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

        $stops = $query
            ->orderBy(
                'transport_route_id'
            )
            ->orderBy(
                'stop_order'
            )
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $stops,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    */

    public function options()
    {
        $routes = TransportRoute::where(
            'school_id',
            $this->schoolId()
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

        return response()->json([
            'success' => true,
            'data' => [
                'routes' => $routes,
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
        $stop = TransportRouteStop::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->with('route')
        ->first();

        if (!$stop) {

            return response()->json([
                'success' => false,
                'message' => 'Stop not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stop,
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

                'transport_route_id' => [
                    'required',
                    'integer',
                ],

                'stop_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'stop_code' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'stop_order' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'pickup_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'drop_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'fare' => [
                    'nullable',
                    'numeric',
                    'min:0',
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
                'message' => 'Invalid transport route.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Stop Code
        |--------------------------------------------------------------------------
        */

        if ($request->filled('stop_code')) {

            $exists = TransportRouteStop::where(
                'school_id',
                $schoolId
            )
            ->where(
                'transport_route_id',
                $request->transport_route_id
            )
            ->where(
                'stop_code',
                $request->stop_code
            )
            ->exists();

            if ($exists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Stop code already exists for this route.',
                ], 422);
            }
        }


        $stop = TransportRouteStop::create([

            'school_id' =>
                $schoolId,

            'transport_route_id' =>
                $request->transport_route_id,

            'stop_name' =>
                $request->stop_name,

            'stop_code' =>
                $request->stop_code,

            'stop_order' =>
                $request->stop_order,

            'pickup_time' =>
                $request->pickup_time,

            'drop_time' =>
                $request->drop_time,

            'fare' =>
                $request->fare ?? 0,

            'remarks' =>
                $request->remarks,

        ]);

        $stop->load('route');

        return response()->json([
            'success' => true,
            'message' => 'Stop created successfully.',
            'data' => $stop,
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
        $stop = TransportRouteStop::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$stop) {

            return response()->json([
                'success' => false,
                'message' => 'Stop not found.',
            ], 404);
        }


        $validator = Validator::make(
            $request->all(),
            [

                'transport_route_id' => [
                    'required',
                    'integer',
                ],

                'stop_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'stop_code' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'stop_order' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'pickup_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'drop_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'fare' => [
                    'nullable',
                    'numeric',
                    'min:0',
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
                'message' => 'Invalid transport route.',
            ], 422);
        }


        if ($request->filled('stop_code')) {

            $exists = TransportRouteStop::where(
                'school_id',
                $this->schoolId()
            )
            ->where(
                'transport_route_id',
                $request->transport_route_id
            )
            ->where(
                'stop_code',
                $request->stop_code
            )
            ->where(
                'id',
                '!=',
                $id
            )
            ->exists();

            if ($exists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Stop code already exists for this route.',
                ], 422);
            }
        }


        $stop->update([

            'transport_route_id' =>
                $request->transport_route_id,

            'stop_name' =>
                $request->stop_name,

            'stop_code' =>
                $request->stop_code,

            'stop_order' =>
                $request->stop_order,

            'pickup_time' =>
                $request->pickup_time,

            'drop_time' =>
                $request->drop_time,

            'fare' =>
                $request->fare ?? 0,

            'remarks' =>
                $request->remarks,

        ]);

        $stop->load('route');

        return response()->json([
            'success' => true,
            'message' => 'Stop updated successfully.',
            'data' => $stop,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $stop = TransportRouteStop::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$stop) {

            return response()->json([
                'success' => false,
                'message' => 'Stop not found.',
            ], 404);
        }

        $stop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stop deleted successfully.',
        ]);
    }
}