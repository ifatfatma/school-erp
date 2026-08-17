<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookIssueService;
use Illuminate\Http\Request;

class BookIssueController extends Controller
{
    public function __construct(
        protected BookIssueService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List
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

            'book_copy_id' => [
                'nullable',
                'integer',
            ],

            'status' => [
                'nullable',
                'string',
                'in:Issued,Returned,Overdue,Lost,Damaged',
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
    | Overdue
    |--------------------------------------------------------------------------
    */

    public function overdue()
    {
        return response()->json([

            'success' => true,

            'data' => $this->service->overdue(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Member Issues
    |--------------------------------------------------------------------------
    */

    public function memberIssues(
        int $memberId
    ) {
        return response()->json([

            'success' => true,

            'data' => $this->service->memberIssues(
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
    | Issue Book
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'library_member_id' => [
                'required',
                'integer',
            ],

            'book_id' => [
                'required',
                'integer',
            ],

            'book_copy_id' => [
                'required',
                'integer',
            ],

            'issue_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'issue_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'required',
                'date',
                'after_or_equal:issue_date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $issue =
            $this->service->issue(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book issued successfully.',

            'data' => $issue,

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

            'issue_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'issue_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'required',
                'date',
                'after_or_equal:issue_date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $issue =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book issue updated successfully.',

            'data' => $issue,

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
                'Book issue deleted successfully.',

        ]);
    }
}