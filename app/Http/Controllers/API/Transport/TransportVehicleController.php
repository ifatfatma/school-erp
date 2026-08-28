<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\TransportVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportVehicleController extends Controller
{
    protected function schoolId()
    {
        return auth()->user()->school_id;
    }

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = TransportVehicle::where(
            'school_id',
            $schoolId
        );

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'vehicle_no',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'registration_no',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'vehicle_type',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'model',
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

        $vehicles = $query
            ->latest('id')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $vehicles,
        ]);
    }

    public function show($id)
    {
        $vehicle = TransportVehicle::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$vehicle) {

            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vehicle,
        ]);
    }

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();

        $validator = Validator::make(
            $request->all(),
            [
                'vehicle_no' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'registration_no' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'vehicle_type' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'model' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'capacity' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],

                'purchase_date' => [
                    'nullable',
                    'date',
                ],

                'insurance_expiry' => [
                    'nullable',
                    'date',
                ],

                'fitness_expiry' => [
                    'nullable',
                    'date',
                ],

                'pollution_expiry' => [
                    'nullable',
                    'date',
                ],

                'status' => [
                    'nullable',
                    'in:Active,Inactive,Maintenance',
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

        $vehicle = TransportVehicle::create([
            'school_id' =>
                $schoolId,

            'vehicle_no' =>
                $request->vehicle_no,

            'registration_no' =>
                $request->registration_no,

            'vehicle_type' =>
                $request->vehicle_type,

            'model' =>
                $request->model,

            'capacity' =>
                $request->capacity,

            'purchase_date' =>
                $request->purchase_date,

            'insurance_expiry' =>
                $request->insurance_expiry,

            'fitness_expiry' =>
                $request->fitness_expiry,

            'pollution_expiry' =>
                $request->pollution_expiry,

            'status' =>
                $request->status ?: 'Active',

            'remarks' =>
                $request->remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Vehicle created successfully.',
            'data' => $vehicle,
        ], 201);
    }

    public function update(
        Request $request,
        $id
    ) {
        $vehicle = TransportVehicle::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$vehicle) {

            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found.',
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'vehicle_no' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'registration_no' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'vehicle_type' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'model' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'capacity' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],

                'purchase_date' => [
                    'nullable',
                    'date',
                ],

                'insurance_expiry' => [
                    'nullable',
                    'date',
                ],

                'fitness_expiry' => [
                    'nullable',
                    'date',
                ],

                'pollution_expiry' => [
                    'nullable',
                    'date',
                ],

                'status' => [
                    'nullable',
                    'in:Active,Inactive,Maintenance',
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

        $vehicle->update([
            'vehicle_no' =>
                $request->vehicle_no,

            'registration_no' =>
                $request->registration_no,

            'vehicle_type' =>
                $request->vehicle_type,

            'model' =>
                $request->model,

            'capacity' =>
                $request->capacity,

            'purchase_date' =>
                $request->purchase_date,

            'insurance_expiry' =>
                $request->insurance_expiry,

            'fitness_expiry' =>
                $request->fitness_expiry,

            'pollution_expiry' =>
                $request->pollution_expiry,

            'status' =>
                $request->status ?: 'Active',

            'remarks' =>
                $request->remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Vehicle updated successfully.',
            'data' => $vehicle,
        ]);
    }

    public function destroy($id)
    {
        $vehicle = TransportVehicle::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$vehicle) {

            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found.',
            ], 404);
        }

        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Vehicle deleted successfully.',
        ]);
    }

    public function status(
        Request $request,
        $id
    ) {
        $vehicle = TransportVehicle::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$vehicle) {

            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found.',
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'status' => [
                    'required',
                    'in:Active,Inactive,Maintenance',
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

        $vehicle->update([
            'status' =>
                $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Vehicle status updated successfully.',
            'data' => $vehicle,
        ]);
    }
}