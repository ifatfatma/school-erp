<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExpensePaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpensePaymentController extends Controller
{
    public function __construct(
        protected ExpensePaymentService $service
    ) {
    }

    /**
     * List payments.
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'expense_id' => [
                'nullable',
                'integer',
                Rule::exists('expenses', 'id')
                    ->where(function ($query) use ($schoolId) {
                        $query->where(
                            'school_id',
                            $schoolId
                        );
                    }),
            ],

            'payment_method' => [
                'nullable',
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
            'data' => $this->service->list($filters),
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
     * Create payment.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $data = $request->validate([
            'expense_id' => [
                'required',
                'integer',
                Rule::exists('expenses', 'id')
                    ->where(function ($query) use ($schoolId) {
                        $query->where(
                            'school_id',
                            $schoolId
                        );
                    }),
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
                'required',
                Rule::in([
                    'Success',
                    'Pending',
                    'Cancelled',
                ]),
            ],
        ]);

        $payment = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' =>
                'Expense payment recorded successfully.',
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
        $this->service->find($id);

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

        $payment = $this->service->update(
            $id,
            $data
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Expense payment updated successfully.',
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
                'Expense payment deleted successfully.',
        ]);
    }


    /**
     * Payment summary for an expense.
     */
    public function summary(int $expenseId)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->summary(
                $expenseId
            ),
        ]);
    }
}