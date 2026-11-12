<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LateFeeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LateFeeController extends Controller
{
    public function __construct(
        protected LateFeeService $service
    ) {
    }


    /**
     * List late fee rules.
     */
    public function index(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $filters = $request->validate([

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'late_fee_type' => [
                'nullable',
                Rule::in([
                    'Fixed',
                    'Percentage',
                ]),
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'school_id' => [
                'nullable',
            ],

        ]);

        if ($schoolId !== null) {
            $filters['school_id'] = $schoolId;
        }

        $response = [
            'success' => true,
            'data' => $this->service->list($filters),
        ];

        if ($this->isSuperAdmin($request)) {
            $response['all_schools'] = \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']);
        }

        return response()->json($response);
    }


    /**
     * Show late fee.
     */
    public function show(int $id)
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->find($id),

        ]);
    }


    /**
     * Create late fee.
     */
    public function store(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request) ?? auth()->user()?->school_id;
        if (!$schoolId) {
            $schoolId = \App\Models\School::first()?->id ?? 1;
        }


        $data = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:255',

                Rule::unique(
                    'late_fees',
                    'code'
                )->where(
                    function ($query) use ($schoolId) {

                        return $query->where(
                            'school_id',
                            $schoolId
                        );

                    }
                ),
            ],

            'late_fee_type' => [
                'required',

                Rule::in([
                    'Fixed',
                    'Percentage',
                ]),
            ],

            'value' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'grace_days' => [
                'required',
                'integer',
                'min:0',
            ],

            'max_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'per_day' => [
                'nullable',
                'boolean',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Percentage Validation
        |--------------------------------------------------------------------------
        */

        if (
            $data['late_fee_type'] === 'Percentage'
            &&
            (float) $data['value'] > 100
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Percentage late fee cannot exceed 100%.',

                'errors' => [

                    'value' => [
                        'Percentage late fee cannot exceed 100%.'
                    ],

                ],

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Fixed Type
        |--------------------------------------------------------------------------
        */

        /*
         * max_amount is useful for both types,
         * especially when per_day is enabled.
         */

        $lateFee =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Late fee created successfully.',

            'data' => $lateFee,

        ], 201);
    }


    /**
     * Update late fee.
     */
    public function update(
        Request $request,
        int $id
    ) {

        $schoolId = $this->resolveSchoolId($request) ?? auth()->user()?->school_id;
        if (!$schoolId) {
            $schoolId = \App\Models\School::first()?->id ?? 1;
        }


        $lateFee =
            $this->service->find($id);


        $data = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:255',

                Rule::unique(
                    'late_fees',
                    'code'
                )
                ->where(
                    function ($query) use ($schoolId) {

                        return $query->where(
                            'school_id',
                            $schoolId
                        );

                    }
                )
                ->ignore($lateFee->id),
            ],

            'late_fee_type' => [
                'required',

                Rule::in([
                    'Fixed',
                    'Percentage',
                ]),
            ],

            'value' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'grace_days' => [
                'required',
                'integer',
                'min:0',
            ],

            'max_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'per_day' => [
                'nullable',
                'boolean',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Percentage Validation
        |--------------------------------------------------------------------------
        */

        if (
            $data['late_fee_type'] === 'Percentage'
            &&
            (float) $data['value'] > 100
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Percentage late fee cannot exceed 100%.',

                'errors' => [

                    'value' => [
                        'Percentage late fee cannot exceed 100%.'
                    ],

                ],

            ], 422);
        }


        $lateFee =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Late fee updated successfully.',

            'data' => $lateFee,

        ]);
    }


    /**
     * Delete late fee.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);


        return response()->json([

            'success' => true,

            'message' =>
                'Late fee deleted successfully.',

        ]);
    }


    /**
     * Activate / deactivate.
     */
    public function toggleStatus(int $id)
    {
        $lateFee =
            $this->service->toggleStatus(
                $id
            );


        return response()->json([

            'success' => true,

            'message' =>
                $lateFee->status
                    ? 'Late fee activated successfully.'
                    : 'Late fee deactivated successfully.',

            'data' => $lateFee,

        ]);
    }
}