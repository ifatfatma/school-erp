<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IncomePaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomePaymentController extends Controller
{
    public function __construct(
        protected IncomePaymentService $service
    ) {
    }

    /**
     * List payments.
     */
    public function index(Request $request)
    {
        $data = $request->validate([

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'income_id' => [
                'nullable',
                'integer',
            ],

            'payment_method' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'Success',
                    'Pending',
                    'Cancelled',
                ]),
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
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

    /**
     * Payment summary.
     */
    public function summary(Request $request)
    {
        $data = $request->validate([

            'income_id' => [
                'nullable',
                'integer',
            ],

            'payment_method' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'Success',
                    'Pending',
                    'Cancelled',
                ]),
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],

        ]);

        return response()->json([
            'success' => true,
            'data' => $this->service->summary($data),
        ]);
    }

    /**
     * Show payment.
     */
    public function show(int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->find($id),
        ]);
    }

    /**
     * Get remaining amount for income.
     */
    public function remaining(int $incomeId)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'income_id' => $incomeId,
                'remaining_amount' =>
                    $this->service->remainingAmount(
                        $incomeId
                    ),
            ],
        ]);
    }

    /**
     * Create payment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'income_id' => [
                'required',
                'integer',
                Rule::exists(
                    'incomes',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'school_id',
                        auth()->user()->school_id
                    );
                }),
            ],

            'receipt_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'Cash',
                    'Card',
                    'Bank Transfer',
                    'UPI',
                    'Cheque',
                    'Online',
                    'Other',
                ]),
            ],

            'transaction_no' => [
                'nullable',
                'string',
                'max:150',
            ],

            'bank_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'cheque_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'Success',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);

        $data['status'] =
            $data['status'] ?? 'Success';

        $payment =
            $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' =>
                'Income payment created successfully.',
            'data' => $payment,
        ], 201);
    }

    /**
     * Update payment.
     */
    public function update(
        Request $request,
        int $id
    ) {
        $data = $request->validate([

            'payment_date' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'Cash',
                    'Card',
                    'Bank Transfer',
                    'UPI',
                    'Cheque',
                    'Online',
                    'Other',
                ]),
            ],

            'transaction_no' => [
                'nullable',
                'string',
                'max:150',
            ],

            'bank_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'cheque_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Success',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);

        $payment =
            $this->service->update(
                $id,
                $data
            );

        return response()->json([
            'success' => true,
            'message' =>
                'Income payment updated successfully.',
            'data' => $payment,
        ]);
    }

    /**
     * Delete payment.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' =>
                'Income payment deleted successfully.',
        ]);
    }

    /**
     * Change payment status.
     */
    public function status(
        Request $request,
        int $id
    ) {
        $data = $request->validate([

            'status' => [
                'required',
                Rule::in([
                    'Success',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);

        $payment =
            $this->service->changeStatus(
                $id,
                $data['status']
            );

        return response()->json([
            'success' => true,
            'message' =>
                'Income payment status updated successfully.',
            'data' => $payment,
        ]);
    }
}