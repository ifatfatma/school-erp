<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeeReceiptService;
use Illuminate\Http\Request;

class FeeReceiptController extends Controller
{
    public function __construct(
        protected FeeReceiptService $service
    ) {
    }

    /**
     * Receipt list.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
            ],

            'payment_method' => [
                'nullable',
                'string',
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
                $filters
            ),
        ]);
    }

    /**
     * Receipt details.
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
     * Download PDF receipt.
     */
    public function download(int $id)
    {
        return $this->service->download(
            $id
        );
    }

    /**
     * View PDF receipt.
     */
    public function pdf(int $id)
    {
        return $this->service
            ->pdf($id)
            ->stream(
                'receipt-' . $id . '.pdf'
            );
    }
}