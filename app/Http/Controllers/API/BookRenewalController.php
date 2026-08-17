<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookRenewalService;
use Illuminate\Http\Request;

class BookRenewalController extends Controller
{
    public function __construct(
        protected BookRenewalService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List Renewals
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
    | Member Renewals
    |--------------------------------------------------------------------------
    */

    public function memberRenewals(
        int $memberId
    ) {
        return response()->json([

            'success' => true,

            'data' => $this->service->memberRenewals(
                $memberId
            ),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Issue Renewals
    |--------------------------------------------------------------------------
    */

    public function issueRenewals(
        int $issueId
    ) {
        return response()->json([

            'success' => true,

            'data' => $this->service->issueRenewals(
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

            'data' => $this->service->find(
                $id
            ),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Renew Book
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'book_issue_id' => [
                'required',
                'integer',
            ],

            'renewal_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'renewal_date' => [
                'nullable',
                'date',
            ],

            'renewal_days' => [
                'nullable',
                'integer',
                'min:1',
                'max:365',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $renewal =
            $this->service->renew(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book renewed successfully.',

            'data' => $renewal,

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

            'remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $renewal =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Renewal remarks updated successfully.',

            'data' => $renewal,

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
                'Book renewal deleted successfully.',

        ]);
    }
}