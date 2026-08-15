<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecurringExpenseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecurringExpenseController extends Controller
{
    public function __construct(
        protected RecurringExpenseService $service
    ) {
    }


    /**
     * List recurring expenses.
     */
    public function index(Request $request)
    {
        $schoolId =
            auth()->user()->school_id;


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
                )->where(function ($query) use (
                    $schoolId
                ) {
                    $query->where(
                        'school_id',
                        $schoolId
                    );
                }),
            ],

            'frequency' => [
                'nullable',

                Rule::in([
                    'Daily',
                    'Weekly',
                    'Monthly',
                    'Quarterly',
                    'Yearly',
                ]),
            ],

            'status' => [
                'nullable',

                Rule::in([
                    'Active',
                    'Paused',
                    'Completed',
                ]),
            ],

            'auto_create' => [
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

            'data' =>
                $this->service->list(
                    $filters
                ),

        ]);
    }


    /**
     * Show recurring expense.
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
     * Create recurring expense.
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
                    );
                }),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'payee_name' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'frequency' => [
                'required',

                Rule::in([
                    'Daily',
                    'Weekly',
                    'Monthly',
                    'Quarterly',
                    'Yearly',
                ]),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'next_due_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
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

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'auto_create' => [
                'nullable',
                'boolean',
            ],

            'status' => [
                'nullable',

                Rule::in([
                    'Active',
                    'Paused',
                    'Completed',
                ]),
            ],

        ]);


        $data['auto_create'] =
            $data['auto_create']
            ?? true;

        $data['status'] =
            $data['status']
            ?? 'Active';


        $recurring =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Recurring expense created successfully.',

            'data' =>
                $recurring,

        ], 201);
    }


    /**
     * Update recurring expense.
     */
    public function update(
        Request $request,
        int $id
    ) {
        $schoolId =
            auth()->user()->school_id;


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

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'payee_name' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'frequency' => [
                'required',

                Rule::in([
                    'Daily',
                    'Weekly',
                    'Monthly',
                    'Quarterly',
                    'Yearly',
                ]),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'next_due_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
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

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'auto_create' => [
                'required',
                'boolean',
            ],

            'status' => [
                'required',

                Rule::in([
                    'Active',
                    'Paused',
                    'Completed',
                ]),
            ],

        ]);


        $recurring =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Recurring expense updated successfully.',

            'data' =>
                $recurring,

        ]);
    }


    /**
     * Delete recurring expense.
     */
    public function destroy(int $id)
    {
        $this->service->delete(
            $id
        );


        return response()->json([

            'success' => true,

            'message' =>
                'Recurring expense deleted successfully.',

        ]);
    }


    /**
     * Change status.
     */
    public function status(
        Request $request,
        int $id
    ) {
        $data =
            $request->validate([

                'status' => [
                    'required',

                    Rule::in([
                        'Active',
                        'Paused',
                        'Completed',
                    ]),
                ],

            ]);


        $recurring =
            $this->service->changeStatus(
                $id,
                $data['status']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Recurring expense status updated.',

            'data' =>
                $recurring,

        ]);
    }


    /**
     * Toggle automatic creation.
     */
    public function toggleAutoCreate(
        int $id
    ) {
        $recurring =
            $this->service->toggleAutoCreate(
                $id
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Automatic expense creation setting updated.',

            'data' =>
                $recurring,

        ]);
    }


    /**
     * Manually generate an expense.
     */
    public function generate(
        int $id
    ) {
        $expense =
            $this->service->generateExpense(
                $id
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Expense generated successfully.',

            'data' =>
                $expense,

        ], 201);
    }
}