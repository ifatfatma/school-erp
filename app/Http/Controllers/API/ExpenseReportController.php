<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExpenseReportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseReportController extends Controller
{
    public function __construct(
        protected ExpenseReportService $service
    ) {
    }

    /**
     * Complete expense report.
     */
    public function index(Request $request)
    {
        $filters = $this->validateFilters(
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $this->service->report(
                $filters
            ),
        ]);
    }

    /**
     * Category summary.
     */
    public function categorySummary(
        Request $request
    ) {
        $filters = $this->validateFilters(
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $this->service->categorySummary(
                $filters
            ),
        ]);
    }

    /**
     * Payment method summary.
     */
    public function paymentMethodSummary(
        Request $request
    ) {
        $filters = $this->validateFilters(
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $this->service->paymentMethodSummary(
                $filters
            ),
        ]);
    }

    /**
     * Daily summary.
     */
    public function dailySummary(
        Request $request
    ) {
        $filters = $this->validateFilters(
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $this->service->dailySummary(
                $filters
            ),
        ]);
    }

    /**
     * Validate report filters.
     */
    protected function validateFilters(
        Request $request
    ): array {

        $schoolId =
            auth()->user()->school_id;

        return $request->validate([

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
    }
}