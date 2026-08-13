<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountController extends Controller
{
    public function __construct(
        protected DiscountService $service
    ) {
    }

    /**
     * List discounts.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'discount_type' => [
                'nullable',
                Rule::in([
                    'Percentage',
                    'Fixed',
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
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->service->list($filters),
        ]);
    }


    /**
     * Show discount.
     */
    public function show(int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->find($id),
        ]);
    }


    /**
     * Create discount.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

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
                Rule::unique('discounts', 'code')
                    ->where(function ($query) use ($schoolId) {
                        return $query->where(
                            'school_id',
                            $schoolId
                        );
                    }),
            ],

            'discount_type' => [
                'required',
                Rule::in([
                    'Percentage',
                    'Fixed',
                ]),
            ],

            'value' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'max_discount' => [
                'nullable',
                'numeric',
                'min:0',
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
            $data['discount_type'] === 'Percentage'
            && (float) $data['value'] > 100
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Percentage discount cannot exceed 100%.',
                'errors' => [
                    'value' => [
                        'Percentage discount cannot exceed 100%.'
                    ],
                ],
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Fixed Discount
        |--------------------------------------------------------------------------
        */

        if (
            $data['discount_type'] === 'Fixed'
        ) {

            $data['max_discount'] = null;

        }


        $discount =
            $this->service->create($data);


        return response()->json([
            'success' => true,
            'message' =>
                'Discount created successfully.',
            'data' => $discount,
        ], 201);
    }


    /**
     * Update discount.
     */
    public function update(
        Request $request,
        int $id
    ) {

        $schoolId =
            auth()->user()->school_id;


        $discount =
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
                    'discounts',
                    'code'
                )
                    ->where(function ($query) use ($schoolId) {
                        return $query->where(
                            'school_id',
                            $schoolId
                        );
                    })
                    ->ignore($discount->id),
            ],

            'discount_type' => [
                'required',
                Rule::in([
                    'Percentage',
                    'Fixed',
                ]),
            ],

            'value' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'max_discount' => [
                'nullable',
                'numeric',
                'min:0',
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


        if (
            $data['discount_type'] === 'Percentage'
            && (float) $data['value'] > 100
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Percentage discount cannot exceed 100%.',
                'errors' => [
                    'value' => [
                        'Percentage discount cannot exceed 100%.'
                    ],
                ],
            ], 422);
        }


        if (
            $data['discount_type'] === 'Fixed'
        ) {

            $data['max_discount'] = null;

        }


        $discount =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([
            'success' => true,
            'message' =>
                'Discount updated successfully.',
            'data' => $discount,
        ]);
    }


    /**
     * Delete discount.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' =>
                'Discount deleted successfully.',
        ]);
    }


    /**
     * Toggle status.
     */
    public function toggleStatus(int $id)
    {
        $discount =
            $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' =>
                $discount->status
                    ? 'Discount activated successfully.'
                    : 'Discount deactivated successfully.',
            'data' => $discount,
        ]);
    }
}