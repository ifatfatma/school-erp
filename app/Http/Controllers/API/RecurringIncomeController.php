<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecurringIncomeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecurringIncomeController extends Controller
{
    public function __construct(
        protected RecurringIncomeService $service
    ) {
    }

    /**
     * List recurring incomes.
     */
    public function index(Request $request)
    {
        $data = $request->validate([

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'income_category_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'income_categories',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'school_id',
                        auth()->user()->school_id
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
                    'Cancelled',
                ]),
            ],

            'auto_create' => [
                'nullable',
                'boolean',
            ],

            'due' => [
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
            'data' => $this->service->list($data),
        ]);
    }


    /**
     * Show recurring income.
     */
    public function show(int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->find($id),
        ]);
    }


    /**
     * Create recurring income.
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'income_category_id' => [
                'required',
                'integer',
                Rule::exists(
                    'income_categories',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'school_id',
                        auth()->user()->school_id
                    );
                }),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'received_from' => [
                'required',
                'string',
                'max:255',
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

            'next_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
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
                    'Cancelled',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

        ]);

        $data['status'] =
            $data['status'] ?? 'Active';

        $data['auto_create'] =
            $data['auto_create'] ?? true;

        $recurring =
            $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' =>
                'Recurring income created successfully.',
            'data' => $recurring,
        ], 201);
    }


    /**
     * Update recurring income.
     */
    public function update(
        Request $request,
        int $id
    ) {
        $data = $request->validate([

            'income_category_id' => [
                'required',
                'integer',
                Rule::exists(
                    'income_categories',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'school_id',
                        auth()->user()->school_id
                    );
                }),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'received_from' => [
                'required',
                'string',
                'max:255',
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

            'next_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
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
                    'Cancelled',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
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
                'Recurring income updated successfully.',
            'data' => $recurring,
        ]);
    }


    /**
     * Delete recurring income.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' =>
                'Recurring income deleted successfully.',
        ]);
    }


    /**
     * Change status.
     */
    public function status(
        Request $request,
        int $id
    ) {
        $data = $request->validate([

            'status' => [
                'required',
                Rule::in([
                    'Active',
                    'Paused',
                    'Completed',
                    'Cancelled',
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
                'Recurring income status updated successfully.',
            'data' => $recurring,
        ]);
    }


    /**
     * Generate income manually.
     */
    public function generate(int $id)
    {
        $income =
            $this->service->generateIncome(
                $id
            );

        return response()->json([
            'success' => true,
            'message' =>
                'Income generated successfully.',
            'data' => $income,
        ], 201);
    }


    /**
     * Generate all due recurring incomes.
     */
    public function generateDue()
    {
        $count =
            $this->service->generateDue();

        return response()->json([
            'success' => true,
            'message' =>
                $count . ' recurring income(s) generated successfully.',
            'generated' => $count,
        ]);
    }
}