<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(
        protected BookService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List Books
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $data = $request->validate([

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'category_id' => [
                'nullable',
                'integer',
            ],

            'publisher_id' => [
                'nullable',
                'integer',
            ],

            'author_id' => [
                'nullable',
                'integer',
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


    /*
    |--------------------------------------------------------------------------
    | Active Books
    |--------------------------------------------------------------------------
    */

    public function active()
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->active(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Show Book
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | Create Book
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'book_code' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'integer',
            ],

            'publisher_id' => [
                'nullable',
                'integer',
            ],

            'author_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'author_ids.*' => [
                'integer',
                'distinct',
            ],

            'isbn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'edition' => [
                'nullable',
                'string',
                'max:255',
            ],

            'language' => [
                'nullable',
                'string',
                'max:100',
            ],

            'publication_year' => [
                'nullable',
                'integer',
                'min:1000',
                'max:' . ((int) date('Y') + 1),
            ],

            'pages' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'cover_image' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

        ]);


        $book =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book created successfully.',

            'data' =>
                $book,

        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Book
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        int $id
    ) {

        $data = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'book_code' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'integer',
            ],

            'publisher_id' => [
                'nullable',
                'integer',
            ],

            'author_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'author_ids.*' => [
                'integer',
                'distinct',
            ],

            'isbn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'edition' => [
                'nullable',
                'string',
                'max:255',
            ],

            'language' => [
                'nullable',
                'string',
                'max:100',
            ],

            'publication_year' => [
                'nullable',
                'integer',
                'min:1000',
                'max:' . ((int) date('Y') + 1),
            ],

            'pages' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'cover_image' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'boolean',
            ],

        ]);


        $book =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book updated successfully.',

            'data' =>
                $book,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Book
    |--------------------------------------------------------------------------
    */

    public function destroy(int $id)
    {
        $this->service->delete(
            $id
        );


        return response()->json([

            'success' => true,

            'message' =>
                'Book deleted successfully.',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Change Status
    |--------------------------------------------------------------------------
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


        $book =
            $this->service->changeStatus(
                $id,
                $data['status']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book status updated successfully.',

            'data' =>
                $book,

        ]);
    }
}