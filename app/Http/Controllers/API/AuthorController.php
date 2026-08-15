<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthorService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthorController extends Controller
{
    public function __construct(
        protected AuthorService $service
    ) {
    }

    /**
     * List authors.
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

            'data' => $this->service->list(
                $data
            ),
        ]);
    }


    /**
     * Active authors.
     *
     * Used by Book create/edit dropdown.
     */
    public function active()
    {
        return response()->json([
            'success' => true,

            'data' => $this->service->active(),
        ]);
    }


    /**
     * Show author.
     */
    public function show(int $id)
    {
        return response()->json([
            'success' => true,

            'data' => $this->service->find(
                $id
            ),
        ]);
    }


    /**
     * Create author.
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

            'biography' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

        ]);

        $author =
            $this->service->create(
                $data
            );

        return response()->json([
            'success' => true,

            'message' =>
                'Author created successfully.',

            'data' => $author,
        ], 201);
    }


    /**
     * Update author.
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

            'biography' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'status' => [
                'required',
                'boolean',
            ],

        ]);

        $author =
            $this->service->update(
                $id,
                $data
            );

        return response()->json([
            'success' => true,

            'message' =>
                'Author updated successfully.',

            'data' => $author,
        ]);
    }


    /**
     * Delete author.
     */
    public function destroy(int $id)
    {
        $this->service->delete(
            $id
        );

        return response()->json([
            'success' => true,

            'message' =>
                'Author deleted successfully.',
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

        $author =
            $this->service->changeStatus(
                $id,
                $data['status']
            );

        return response()->json([
            'success' => true,

            'message' =>
                'Author status updated successfully.',

            'data' => $author,
        ]);
    }
}