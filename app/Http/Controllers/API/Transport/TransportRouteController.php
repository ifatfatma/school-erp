<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransportRouteController extends Controller
{
    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }

    public function index(Request $request)
    {
        $query = TransportRoute::where(
            'school_id',
            $this->schoolId()
        )->with('stops');

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'start_location',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'end_location',
                    'like',
                    "%{$search}%"
                );

            });
        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        $routes = $query
            ->latest('id')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $routes,
        ]);
    }

    public function show($id)
    {
        $route = TransportRoute::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->with('stops')
        ->first();

        if (!$route) {

            return response()->json([
                'success' => false,
                'message' => 'Route not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $route,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'start_location' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'end_location' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'distance' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'travel_time' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'fare' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'status' => [
                    'nullable',
                    'in:Active,Inactive',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

                'stops' => [
                    'nullable',
                    'array',
                ],

                'stops.*.stop_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'stops.*.stop_code' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'stops.*.stop_order' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'stops.*.pickup_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'stops.*.drop_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'stops.*.fare' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'stops.*.remarks' => [
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

        $route = DB::transaction(function () use ($request) {

            $route = TransportRoute::create([
                'school_id' =>
                    $this->schoolId(),

                'name' =>
                    $request->name,

                'code' =>
                    $request->code,

                'start_location' =>
                    $request->start_location,

                'end_location' =>
                    $request->end_location,

                'distance' =>
                    $request->distance ?? 0,

                'travel_time' =>
                    $request->travel_time,

                'fare' =>
                    $request->fare ?? 0,

                'status' =>
                    $request->status ?: 'Active',

                'remarks' =>
                    $request->remarks,
            ]);

            foreach (
                $request->input('stops', [])
                as $stop
            ) {

                $route->stops()->create([
                    'school_id' =>
                        $this->schoolId(),

                    'stop_name' =>
                        $stop['stop_name'],

                    'stop_code' =>
                        $stop['stop_code'] ?? null,

                    'stop_order' =>
                        $stop['stop_order'],

                    'pickup_time' =>
                        $stop['pickup_time'] ?? null,

                    'drop_time' =>
                        $stop['drop_time'] ?? null,

                    'fare' =>
                        $stop['fare'] ?? 0,

                    'remarks' =>
                        $stop['remarks'] ?? null,
                ]);
            }

            return $route;
        });

        $route->load('stops');

        return response()->json([
            'success' => true,
            'message' =>
                'Transport route created successfully.',
            'data' => $route,
        ], 201);
    }

    public function update(
        Request $request,
        $id
    ) {
        $route = TransportRoute::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$route) {

            return response()->json([
                'success' => false,
                'message' => 'Route not found.',
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'start_location' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'end_location' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'distance' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'travel_time' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'fare' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'status' => [
                    'nullable',
                    'in:Active,Inactive',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

                'stops' => [
                    'nullable',
                    'array',
                ],

                'stops.*.stop_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'stops.*.stop_code' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'stops.*.stop_order' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'stops.*.pickup_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'stops.*.drop_time' => [
                    'nullable',
                    'date_format:H:i',
                ],

                'stops.*.fare' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'stops.*.remarks' => [
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

        DB::transaction(function () use (
            $request,
            $route
        ) {

            $route->update([
                'name' =>
                    $request->name,

                'code' =>
                    $request->code,

                'start_location' =>
                    $request->start_location,

                'end_location' =>
                    $request->end_location,

                'distance' =>
                    $request->distance ?? 0,

                'travel_time' =>
                    $request->travel_time,

                'fare' =>
                    $request->fare ?? 0,

                'status' =>
                    $request->status ?: 'Active',

                'remarks' =>
                    $request->remarks,
            ]);

            $route->stops()->delete();

            foreach (
                $request->input('stops', [])
                as $stop
            ) {

                $route->stops()->create([
                    'school_id' =>
                        $this->schoolId(),

                    'stop_name' =>
                        $stop['stop_name'],

                    'stop_code' =>
                        $stop['stop_code'] ?? null,

                    'stop_order' =>
                        $stop['stop_order'],

                    'pickup_time' =>
                        $stop['pickup_time'] ?? null,

                    'drop_time' =>
                        $stop['drop_time'] ?? null,

                    'fare' =>
                        $stop['fare'] ?? 0,

                    'remarks' =>
                        $stop['remarks'] ?? null,
                ]);
            }
        });

        $route->load('stops');

        return response()->json([
            'success' => true,
            'message' =>
                'Transport route updated successfully.',
            'data' => $route,
        ]);
    }

    public function destroy($id)
    {
        $route = TransportRoute::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$route) {

            return response()->json([
                'success' => false,
                'message' => 'Route not found.',
            ], 404);
        }

        DB::transaction(function () use ($route) {

            $route->stops()->delete();

            $route->delete();
        });

        return response()->json([
            'success' => true,
            'message' =>
                'Transport route deleted successfully.',
        ]);
    }

    public function status(
        Request $request,
        $id
    ) {
        $route = TransportRoute::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$route) {

            return response()->json([
                'success' => false,
                'message' => 'Route not found.',
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
                'message' => 'Invalid status.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $route->update([
            'status' =>
                $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Route status updated successfully.',
            'data' => $route,
        ]);
    }
}