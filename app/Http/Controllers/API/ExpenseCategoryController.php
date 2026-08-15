<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExpenseCategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function __construct(
        protected ExpenseCategoryService $service
    ) {
    }


    /**
     * List categories.
     */
    public function index(
        Request $request
    ) {
        $filters = $request->validate([

            'search' => [
                'nullable',
                'string',
                'max:100',
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

            'data' =>
                $this->service->list(
                    $filters
                ),

        ]);
    }


    /**
     * Show category.
     */
    public function show(
        int $id
    ) {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->find(
                    $id
                ),

        ]);
    }


    /**
     * Create category.
     */
    public function store(
        Request $request
    ) {

        $schoolId =
            auth()->user()->school_id;


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
                    'expense_categories',
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


        $category =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Expense category created successfully.',

            'data' =>
                $category,

        ], 201);
    }


    /**
     * Update category.
     */
    public function update(
        Request $request,
        int $id
    ) {

        $schoolId =
            auth()->user()->school_id;


        $category =
            $this->service->find(
                $id
            );


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
                    'expense_categories',
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
                ->ignore(
                    $category->id
                ),
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


        $category =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Expense category updated successfully.',

            'data' =>
                $category,

        ]);
    }


    /**
     * Delete category.
     */
    public function destroy(
        int $id
    ) {

        $this->service->delete(
            $id
        );


        return response()->json([

            'success' => true,

            'message' =>
                'Expense category deleted successfully.',

        ]);
    }


    /**
     * Toggle status.
     */
    public function toggleStatus(
        int $id
    ) {

        $category =
            $this->service->toggleStatus(
                $id
            );


        return response()->json([

            'success' => true,

            'message' =>
                $category->status
                    ? 'Expense category activated successfully.'
                    : 'Expense category deactivated successfully.',

            'data' =>
                $category,

        ]);
    }
}