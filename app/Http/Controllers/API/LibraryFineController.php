<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LibraryFineService;
use Illuminate\Http\Request;

class LibraryFineController extends Controller
{
    public function __construct(
        protected LibraryFineService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List Fines
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

            'fine_type' => [
                'nullable',
                'in:Overdue,Lost,Damaged,Other',
            ],

            'status' => [
                'nullable',
                'in:Pending,Partially Paid,Paid,Waived',
            ],

            'library_member_id' => [
                'nullable',
                'integer',
            ],

            'book_id' => [
                'nullable',
                'integer',
            ],

            'book_issue_id' => [
                'nullable',
                'integer',
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

            'data' =>
                $this->service->list($data),

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

            'data' =>
                $this->service->statistics(),

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

            'data' =>
                $this->service->pendingFines(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Overdue Fines
    |--------------------------------------------------------------------------
    */

    public function overdueFines()
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->overdueFines(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Lost / Damaged Fines
    |--------------------------------------------------------------------------
    */

    public function lostDamagedFines()
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->lostDamagedFines(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Member Fines
    |--------------------------------------------------------------------------
    */

    public function memberFines(
        int $memberId
    ) {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->memberFines(
                    $memberId
                ),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Issue Fines
    |--------------------------------------------------------------------------
    */

    public function issueFines(
        int $issueId
    ) {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->issueFines(
                    $issueId
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

            'data' =>
                $this->service->find($id),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Create Fine
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'library_member_id' => [
                'required',
                'integer',
            ],

            'book_issue_id' => [
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

            'lost_damaged_book_id' => [
                'nullable',
                'integer',
            ],

            'fine_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'fine_date' => [
                'nullable',
                'date',
            ],

            'fine_type' => [
                'required',
                'in:Overdue,Lost,Damaged,Other',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'overdue_days' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'fine_per_day' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'fine_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'waiver_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $fine =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Library fine created successfully.',

            'data' => $fine,

        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Fine
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        int $id
    ) {
        $data = $request->validate([

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'fine_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'fine_per_day' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'overdue_days' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'waiver_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => [
                'nullable',
                'in:Pending,Partially Paid,Paid,Waived',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $fine =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Library fine updated successfully.',

            'data' => $fine,

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
                'Library fine deleted successfully.',

        ]);
    }
}