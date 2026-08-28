<?php

namespace App\Http\Controllers\Api\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\TransportDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportDriverController extends Controller
{
    protected function schoolId()
    {
        return auth()->user()->school_id;
    }

    public function index(Request $request)
    {
        $query = TransportDriver::where(
            'school_id',
            $this->schoolId()
        );

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
                    'phone',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'license_no',
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

        $drivers = $query
            ->latest('id')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $drivers,
        ]);
    }

    public function show($id)
    {
        $driver = TransportDriver::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$driver) {

            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $driver,
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

                'phone' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],

                'license_no' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'license_expiry' => [
                    'nullable',
                    'date',
                ],

                'joining_date' => [
                    'nullable',
                    'date',
                ],

                'address' => [
                    'nullable',
                    'string',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

                'status' => [
                    'nullable',
                    'in:Active,Inactive',
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

        $driver = TransportDriver::create([
            'school_id' =>
                $this->schoolId(),

            'name' =>
                $request->name,

            'code' =>
                $request->code,

            'phone' =>
                $request->phone,

            'email' =>
                $request->email,

            'license_no' =>
                $request->license_no,

            'license_expiry' =>
                $request->license_expiry,

            'joining_date' =>
                $request->joining_date,

            'address' =>
                $request->address,

            'remarks' =>
                $request->remarks,

            'status' =>
                $request->status ?: 'Active',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Driver created successfully.',
            'data' => $driver,
        ], 201);
    }

    public function update(
        Request $request,
        $id
    ) {
        $driver = TransportDriver::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$driver) {

            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
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

                'phone' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],

                'license_no' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'license_expiry' => [
                    'nullable',
                    'date',
                ],

                'joining_date' => [
                    'nullable',
                    'date',
                ],

                'address' => [
                    'nullable',
                    'string',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                ],

                'status' => [
                    'nullable',
                    'in:Active,Inactive',
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

        $driver->update([
            'name' =>
                $request->name,

            'code' =>
                $request->code,

            'phone' =>
                $request->phone,

            'email' =>
                $request->email,

            'license_no' =>
                $request->license_no,

            'license_expiry' =>
                $request->license_expiry,

            'joining_date' =>
                $request->joining_date,

            'address' =>
                $request->address,

            'remarks' =>
                $request->remarks,

            'status' =>
                $request->status ?: 'Active',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Driver updated successfully.',
            'data' => $driver,
        ]);
    }

    public function destroy($id)
    {
        $driver = TransportDriver::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$driver) {

            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        $driver->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Driver deleted successfully.',
        ]);
    }

    public function status(
        Request $request,
        $id
    ) {
        $driver = TransportDriver::where(
            'school_id',
            $this->schoolId()
        )
        ->where(
            'id',
            $id
        )
        ->first();

        if (!$driver) {

            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
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

        $driver->update([
            'status' =>
                $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Driver status updated successfully.',
            'data' => $driver,
        ]);
    }
}