<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LibraryMemberService;
use Illuminate\Http\Request;

class LibraryMemberController extends Controller
{
    public function __construct(
        protected LibraryMemberService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List Members
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

            'member_type' => [
                'nullable',
                'string',
                'in:Student,Staff',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'expired' => [
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


    /*
    |--------------------------------------------------------------------------
    | Active Members
    |--------------------------------------------------------------------------
    */

    public function active()
    {
        return response()->json([

            'success' => true,

            'data' => $this->service->active(),

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
    | Create
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'member_no' => [
                'required',
                'string',
                'max:255',
            ],

            'member_type' => [
                'required',
                'string',
                'in:Student,Staff',
            ],

            'student_id' => [
                'nullable',
                'integer',
            ],

            'staff_id' => [
                'nullable',
                'integer',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'membership_date' => [
                'required',
                'date',
            ],

            'expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:membership_date',
            ],

            'issue_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $member =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Library member created successfully.',

            'data' => $member,

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

            'member_no' => [
                'required',
                'string',
                'max:255',
            ],

            'member_type' => [
                'required',
                'string',
                'in:Student,Staff',
            ],

            'student_id' => [
                'nullable',
                'integer',
            ],

            'staff_id' => [
                'nullable',
                'integer',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'membership_date' => [
                'required',
                'date',
            ],

            'expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:membership_date',
            ],

            'issue_limit' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $member =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Library member updated successfully.',

            'data' => $member,

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
                'Library member deleted successfully.',

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

        $data = $request->validate([

            'status' => [
                'required',
                'boolean',
            ],

        ]);


        $member =
            $this->service->changeStatus(
                $id,
                $data['status']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Library member status updated successfully.',

            'data' => $member,

        ]);
    }
}