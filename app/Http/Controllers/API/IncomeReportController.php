<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IncomeReportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeReportController extends Controller
{
    public function __construct(
        protected IncomeReportService $service
    ) {
    }

    /**
     * Common report filters.
     */
    protected function filters(Request $request): array
    {
        return $request->validate([

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],

            'category_id' => [
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

            'status' => [
                'nullable',
                Rule::in([
                    'Received',
                    'Pending',
                    'Cancelled',
                ]),
            ],

        ]);
    }


    /**
     * Overall summary.
     */
    public function summary(Request $request)
    {
        $filters =
            $this->filters($request);

        return response()->json([
            'success' => true,

            'data' =>
                $this->service->summary(
                    $filters
                ),
        ]);
    }


    /**
     * Daily report.
     */
    public function daily(Request $request)
    {
        $filters =
            $this->filters($request);

        return response()->json([
            'success' => true,

            'data' =>
                $this->service->daily(
                    $filters
                ),
        ]);
    }


    /**
     * Monthly report.
     */
    public function monthly(Request $request)
    {
        $filters =
            $this->filters($request);

        return response()->json([
            'success' => true,

            'data' =>
                $this->service->monthly(
                    $filters
                ),
        ]);
    }


    /**
     * Yearly report.
     */
    public function yearly(Request $request)
    {
        $filters =
            $this->filters($request);

        return response()->json([
            'success' => true,

            'data' =>
                $this->service->yearly(
                    $filters
                ),
        ]);
    }


    /**
     * Category-wise report.
     */
    public function categoryWise(
        Request $request
    ) {
        $filters =
            $this->filters($request);

        return response()->json([
            'success' => true,

            'data' =>
                $this->service->categoryWise(
                    $filters
                ),
        ]);
    }


    /**
     * Payment-method-wise report.
     */
    public function paymentMethodWise(
        Request $request
    ) {
        $filters =
            $this->filters($request);

        return response()->json([
            'success' => true,

            'data' =>
                $this->service->paymentMethodWise(
                    $filters
                ),
        ]);
    }
}