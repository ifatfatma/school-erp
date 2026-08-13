<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentHistoryService;
use Illuminate\Http\Request;

class PaymentHistoryController extends Controller
{
    public function __construct(
        protected PaymentHistoryService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Payment History
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $filters = $request->validate([

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payment_method' => [
                'nullable',
                'string',
            ],

            'status' => [
                'nullable',
                'in:Success,Pending,Cancelled',
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
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
                $this->service->list($filters),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Payment Details
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
}