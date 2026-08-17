<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookCopyService;
use Illuminate\Http\Request;

class BookCopyController extends Controller
{
    public function __construct(
        protected BookCopyService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List Book Copies
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

            'book_id' => [
                'nullable',
                'integer',
            ],

            'status' => [
                'nullable',
                'string',
                'in:Available,Issued,Reserved,Lost,Damaged,Maintenance',
            ],

            'condition' => [
                'nullable',
                'string',
                'in:New,Good,Fair,Damaged,Lost',
            ],

            'shelf_no' => [
                'nullable',
                'string',
                'max:255',
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
    | Available Copies
    |--------------------------------------------------------------------------
    */

    public function available(Request $request)
    {
        $data = $request->validate([

            'book_id' => [
                'nullable',
                'integer',
            ],

        ]);


        return response()->json([

            'success' => true,

            'data' => $this->service->available(
                $data['book_id'] ?? null
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
    | Find By Barcode
    |--------------------------------------------------------------------------
    */

    public function barcode(Request $request)
    {
        $data = $request->validate([

            'barcode' => [
                'required',
                'string',
                'max:255',
            ],

        ]);


        return response()->json([

            'success' => true,

            'data' => $this->service->findByBarcode(
                $data['barcode']
            ),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Find By Accession Number
    |--------------------------------------------------------------------------
    */

    public function accession(Request $request)
    {
        $data = $request->validate([

            'accession_no' => [
                'required',
                'string',
                'max:255',
            ],

        ]);


        return response()->json([

            'success' => true,

            'data' => $this->service->findByAccession(
                $data['accession_no']
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
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([

            'book_id' => [
                'required',
                'integer',
            ],

            'accession_no' => [
                'required',
                'string',
                'max:255',
            ],

            'barcode' => [
                'nullable',
                'string',
                'max:255',
            ],

            'copy_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'shelf_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'rack_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'purchase_date' => [
                'nullable',
                'date',
            ],

            'purchase_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'condition' => [
                'nullable',
                'string',
                'in:New,Good,Fair,Damaged,Lost',
            ],

            'status' => [
                'nullable',
                'string',
                'in:Available,Issued,Reserved,Lost,Damaged,Maintenance',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $copy =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book copy created successfully.',

            'data' => $copy,

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

            'book_id' => [
                'required',
                'integer',
            ],

            'accession_no' => [
                'required',
                'string',
                'max:255',
            ],

            'barcode' => [
                'nullable',
                'string',
                'max:255',
            ],

            'copy_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'shelf_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'rack_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'purchase_date' => [
                'nullable',
                'date',
            ],

            'purchase_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'condition' => [
                'required',
                'string',
                'in:New,Good,Fair,Damaged,Lost',
            ],

            'status' => [
                'required',
                'string',
                'in:Available,Issued,Reserved,Lost,Damaged,Maintenance',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

        ]);


        $copy =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book copy updated successfully.',

            'data' => $copy,

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
                'Book copy deleted successfully.',

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
                'string',
                'in:Available,Issued,Reserved,Lost,Damaged,Maintenance',
            ],

        ]);


        $copy =
            $this->service->changeStatus(
                $id,
                $data['status']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book copy status updated successfully.',

            'data' => $copy,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Change Condition
    |--------------------------------------------------------------------------
    */

    public function condition(
        Request $request,
        int $id
    ) {

        $data = $request->validate([

            'condition' => [
                'required',
                'string',
                'in:New,Good,Fair,Damaged,Lost',
            ],

        ]);


        $copy =
            $this->service->changeCondition(
                $id,
                $data['condition']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Book copy condition updated successfully.',

            'data' => $copy,

        ]);
    }
}