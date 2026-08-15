<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $service
    ) {
    }

    /**
     * List expenses.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'expense_category_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'expense_categories',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'school_id',
                        auth()->user()->school_id
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
                    'Paid',
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

            'data' =>
                $this->service->list(
                    $filters
                ),

        ]);
    }


    /**
     * Show expense.
     */
    public function show(int $id)
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->find(
                    $id
                ),

        ]);
    }


    /**
     * Create expense.
     */
    public function store(Request $request)
    {
        $schoolId =
            auth()->user()->school_id;


        $data = $request->validate([

            'expense_category_id' => [
                'required',
                'integer',

                Rule::exists(
                    'expense_categories',
                    'id'
                )->where(function ($query) use (
                    $schoolId
                ) {

                    $query->where(
                        'school_id',
                        $schoolId
                    )->where(
                        'status',
                        true
                    );

                }),
            ],

            'expense_date' => [
                'required',
                'date',
            ],

            'expense_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payee_name' => [
                'required',
                'string',
                'max:255',
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

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Paid',
                    'Pending',
                    'Cancelled',
                ]),
            ],

            'attachment' => [
                'nullable',
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf,webp',
            ],

        ]);


        $expense =
            $this->service->create(
                $data,
                $request->file(
                    'attachment'
                )
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Expense created successfully.',

            'data' =>
                $expense,

        ], 201);
    }


    /**
     * Update expense.
     */
    public function update(
        Request $request,
        int $id
    ) {
        $schoolId =
            auth()->user()->school_id;


        /*
         * Make sure the expense belongs
         * to the current school.
         */
        $expense =
            $this->service->find($id);


        $data = $request->validate([

            'expense_category_id' => [
                'required',
                'integer',

                Rule::exists(
                    'expense_categories',
                    'id'
                )->where(function ($query) use (
                    $schoolId
                ) {

                    $query->where(
                        'school_id',
                        $schoolId
                    );

                }),
            ],

            'expense_date' => [
                'required',
                'date',
            ],

            'expense_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payee_name' => [
                'required',
                'string',
                'max:255',
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

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Paid',
                    'Pending',
                    'Cancelled',
                ]),
            ],

            'attachment' => [
                'nullable',
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf,webp',
            ],

        ]);


        $expense =
            $this->service->update(
                $id,
                $data,
                $request->file(
                    'attachment'
                )
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Expense updated successfully.',

            'data' =>
                $expense,

        ]);
    }


    /**
     * Delete expense.
     */
    public function destroy(int $id)
    {
        $this->service->delete(
            $id
        );


        return response()->json([

            'success' => true,

            'message' =>
                'Expense deleted successfully.',

        ]);
    }


    /**
     * Change expense status.
     */
    public function status(
        Request $request,
        int $id
    ) {
        $data = $request->validate([

            'status' => [
                'required',
                Rule::in([
                    'Paid',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);


        $expense =
            $this->service->changeStatus(
                $id,
                $data['status']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Expense status updated successfully.',

            'data' =>
                $expense,

        ]);
    }


    /**
     * Total expense.
     */
    public function total(
        Request $request
    ) {
        $filters = $request->validate([

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],

            'expense_category_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'expense_categories',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'school_id',
                        auth()->user()->school_id
                    );
                }),
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'Paid',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);


        return response()->json([

            'success' => true,

            'total' =>
                $this->service->total(
                    $filters
                ),

        ]);
    }
}