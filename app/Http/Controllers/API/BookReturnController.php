<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookReturnService;
use Illuminate\Http\Request;

class BookReturnController extends Controller
{
    public function __construct(
        protected BookReturnService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List Returns
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

            'library_member_id' => [
                'nullable',
                'integer',
            ],

            'book_id' => [
                'nullable',
                'integer',
            ],

            'book_condition' => [
                'nullable',
                'string',
                'in:Good,Damaged,Lost',
            ],

            'fine_status' => [
                'nullable',
                'string',
                'in:No Fine,Pending,Paid',
            ],

            'overdue' => [
                'nullable',
                'boolean',
            ],

            'from_date' => [
                'nullable',
                'date',
            ],

            'to_date' => [
                'nullable',
                'date',
                'after_or_equal:from_date',
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


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    public function statistics()
    {
        return response()->json([

            'success' => true,

            'data' => $this->service->statistics(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Pending Fines
    |--------------------------------------------------------------------------
    */

    public function pendingFines()
    {
        return response()->json([

            'success' => true,

            'data' => $this->service->pendingFines(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Member Returns
    |--------------------------------------------------------------------------
    */

    public function memberReturns(
        int $memberId
    ) {
        return response()->json([

            'success' => true,

            'data' => $this->service->memberReturns(
                $memberId
            ),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | Return Book
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'book_issue_id' => [
                'required',
                'integer',
            ],

            'return_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'return_date' => [
                'nullable',
                'date',
            ],

            'book_condition' => [
                'nullable',
                'string',
                'in:Good,Damaged,Lost',
            ],

            'fine_status' => [
                'nullable',
                'string',
                'in:No Fine,Pending,Paid',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $return =
            $this->service->returnBook(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book returned successfully.',

            'data' => $return,

        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        int $id
    ) {
        $data = $request->validate([

            'return_date' => [
                'nullable',
                'date',
            ],

            'book_condition' => [
                'nullable',
                'string',
                'in:Good,Damaged,Lost',
            ],

            'fine_status' => [
                'nullable',
                'string',
                'in:No Fine,Pending,Paid',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $return =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book return updated successfully.',

            'data' => $return,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
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
                'Book return deleted successfully.',

        ]);
    }
}