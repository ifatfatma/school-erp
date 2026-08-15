<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookCategoryService;
use Illuminate\Http\Request;

class BookCategoryController extends Controller
{
    public function __construct(
        protected BookCategoryService $service
    ) {
    }


    /**
     * List categories.
     */
    public function index(
        Request $request
    ) {

        $data =
            $request->validate([

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
                    $data
                ),

        ]);
    }


    /**
     * Active categories.
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

        $data =
            $request->validate([

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:10000',
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
                'Book category created successfully.',

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

        $data =
            $request->validate([

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:10000',
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
                'Book category updated successfully.',

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
                'Book category deleted successfully.',

        ]);
    }


    /**
     * Change status.
     */
    public function status(
        Request $request,
        int $id
    ) {

        $data =
            $request->validate([

                'status' => [
                    'required',
                    'boolean',
                ],

            ]);


        $category =
            $this->service->changeStatus(
                $id,
                $data['status']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book category status updated successfully.',

            'data' =>
                $category,

        ]);
    }
}