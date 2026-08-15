<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IncomeCategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeCategoryController extends Controller
{
    public function __construct(
        protected IncomeCategoryService $service
    ) {
    }


    /**
     * List income categories.
     */
    public function index(Request $request)
    {
        $data = $request->validate([

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    '0',
                    '1',
                    0,
                    1,
                ]),
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
                    $data
                ),

        ]);
    }


    /**
     * Get active categories.
     */
    public function active()
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->active(),

        ]);
    }


    /**
     * Show category.
     */
    public function show(int $id)
    {
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
    public function store(Request $request)
    {
        $data = $request->validate([

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

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

        ]);


        $data['status'] =
            $data['status'] ?? true;


        $category =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Income category created successfully.',

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

        $data = $request->validate([

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

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'required',
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
                'Income category updated successfully.',

            'data' =>
                $category,

        ]);
    }


    /**
     * Delete category.
     */
    public function destroy(int $id)
    {
        $this->service->delete(
            $id
        );


        return response()->json([

            'success' => true,

            'message' =>
                'Income category deleted successfully.',

        ]);
    }


    /**
     * Change status.
     */
    public function status(
        Request $request,
        int $id
    ) {

        $data = $request->validate([

            'status' => [
                'required',
                'boolean',
            ],

        ]);


        $category =
            $this->service->changeStatus(
                $id,
                (bool) $data['status']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Income category status updated successfully.',

            'data' =>
                $category,

        ]);
    }
}