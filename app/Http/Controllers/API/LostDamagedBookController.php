<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LostDamagedBookService;
use Illuminate\Http\Request;

class LostDamagedBookController extends Controller
{
    public function __construct(
        protected LostDamagedBookService $service
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

            'type' => [
                'nullable',
                'in:Lost,Damaged',
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

            'charge_status' => [
                'nullable',
                'in:Pending,Paid,Waived',
            ],

            'resolution' => [
                'nullable',
                'in:Pending,Replacement,Payment,Waived',
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
            'data' => $this->service->list($data),
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
    | Pending Charges
    |--------------------------------------------------------------------------
    */

    public function pendingCharges()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->pendingCharges(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Lost Books
    |--------------------------------------------------------------------------
    */

    public function lostBooks()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->lostBooks(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Damaged Books
    |--------------------------------------------------------------------------
    */

    public function damagedBooks()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->damagedBooks(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Member History
    |--------------------------------------------------------------------------
    */

    public function memberHistory(int $memberId)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->memberHistory(
                $memberId
            ),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Book Copy History
    |--------------------------------------------------------------------------
    */

    public function copyHistory(int $copyId)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->copyHistory(
                $copyId
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
            'data' => $this->service->find($id),
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

            'book_issue_id' => [
                'nullable',
                'integer',
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
                'required',
                'integer',
            ],

            'report_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'report_date' => [
                'nullable',
                'date',
            ],

            'type' => [
                'required',
                'in:Lost,Damaged',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'book_value' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'charge_amount' => [
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


        $record =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                $data['type'] === 'Lost'
                    ? 'Book marked as lost successfully.'
                    : 'Book marked as damaged successfully.',

            'data' => $record,

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

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'charge_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'charge_status' => [
                'nullable',
                'in:Pending,Paid,Waived',
            ],

            'resolution' => [
                'nullable',
                'in:Pending,Replacement,Payment,Waived',
            ],

            'resolved_date' => [
                'nullable',
                'date',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $record =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Lost/damaged book record updated successfully.',

            'data' => $record,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([

            'success' => true,

            'message' =>
                'Lost/damaged book record deleted successfully.',

        ]);
    }
}