<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IncomeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    public function __construct(
        protected IncomeService $service
    ) {
    }


    /**
     * List incomes.
     */
    public function index(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

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
                )->where(function ($query) use ($schoolId) {
                    if ($schoolId !== null) {
                        $query->where(
                            'school_id',
                            $schoolId
                        );
                    }
                }),
            ],

            'payment_method' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'Received',
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

            'school_id' => [
                'nullable',
            ],

        ]);

        if ($schoolId !== null) {
            $data['school_id'] = $schoolId;
        }

        $list = $this->service->list($data);

        $response = [
            'success' => true,
            'data' => $list,
        ];

        if ($this->isSuperAdmin($request)) {
            $response['all_schools'] = \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']);
        }

        return response()->json($response);
    }


    /**
     * Get active income categories.
     */
    public function categories()
    {
        return response()->json([

            'success' => true,

            'data' =>
                $this->service->categories(),

        ]);
    }


    /**
     * Income summary.
     */
    public function summary(
        Request $request
    ) {

        $data = $request->validate([

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

            'payment_method' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'Received',
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

            'data' =>
                $this->service->summary(
                    $data
                ),

        ]);
    }


    /**
     * Show income.
     */
    public function show(
        int $id
    ) {

        return response()->json([

            'success' => true,

            'data' =>
                $this->service->find(
                    $id
                ),

        ]);
    }


    /**
     * Create income.
     */
    public function store(
        Request $request
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

            'income_date' => [
                'required',
                'date',
            ],

            'income_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'received_from' => [
                'required',
                'string',
                'max:255',
            ],

            'payment_method' => [
                'required',
                'string',
                'max:100',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'attachment' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'Received',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);


        $data['status'] =
            $data['status'] ??
            'Received';


        $income =
            $this->service->create(
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Income created successfully.',

            'data' =>
                $income,

        ], 201);
    }


    /**
     * Update income.
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

            'income_date' => [
                'required',
                'date',
            ],

            'income_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'received_from' => [
                'required',
                'string',
                'max:255',
            ],

            'payment_method' => [
                'required',
                'string',
                'max:100',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'attachment' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Received',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);


        $income =
            $this->service->update(
                $id,
                $data
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Income updated successfully.',

            'data' =>
                $income,

        ]);
    }


    /**
     * Delete income.
     */
    public function destroy(
        int $id
    ) {

        $this->service->delete(
            $id
        );


        return response()->json([

            'success' => true,

            'message' =>
                'Income deleted successfully.',

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
                    'Received',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);


        $income =
            $this->service->changeStatus(
                $id,
                $data['status']
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Income status updated successfully.',

            'data' =>
                $income,

        ]);
    }
}