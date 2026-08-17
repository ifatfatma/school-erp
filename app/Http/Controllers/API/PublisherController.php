<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PublisherService;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function __construct(
        protected PublisherService $service
    ) {
    }

    /**
     * List publishers.
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
     * Active publishers.
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
     * Show publisher.
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
     * Create publisher.
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
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'website' => [
                'nullable',
                'url',
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

        $publisher =
            $this->service->create(
                $data
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Publisher created successfully.',

            'data' =>
                $publisher,

        ], 201);
    }

    /**
     * Update publisher.
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
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'website' => [
                'nullable',
                'url',
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

        $publisher =
            $this->service->update(
                $id,
                $data
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Publisher updated successfully.',

            'data' =>
                $publisher,

        ]);
    }

    /**
     * Delete publisher.
     */
    public function destroy(int $id)
    {
        $this->service->delete(
            $id
        );

        return response()->json([

            'success' => true,

            'message' =>
                'Publisher deleted successfully.',

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

        $publisher =
            $this->service->changeStatus(
                $id,
                $data['status']
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Publisher status updated successfully.',

            'data' =>
                $publisher,

        ]);
    }
}