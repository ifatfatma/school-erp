<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeeCollectionService;
use Illuminate\Http\Request;

class FeeCollectionController extends Controller
{
    public function __construct(
        protected FeeCollectionService $service
    ) {
    }

    public function students(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->students(
                $request->all()
            ),
        ]);
    }

    public function dues(int $student)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->dues($student),
        ]);
    }

    public function collect(Request $request)
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                'exists:students,id'
            ],

            'payment_date' => [
                'required',
                'date'
            ],

            'payment_method' => [
                'required',
                'string',
                'in:Cash,Card,UPI,Bank Transfer,Cheque'
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01'
            ],

            'items' => [
                'required',
                'array',
                'min:1'
            ],

            'items.*.student_fee_due_id' => [
                'required',
                'integer'
            ],

            'items.*.amount' => [
                'required',
                'numeric',
                'min:0.01'
            ],

            'remarks' => [
                'nullable',
                'string'
            ],
        ]);

        $payment = $this->service->collect(
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Fee collected successfully.',
            'data' => $payment,
        ]);
    }

    public function show(int $payment)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->show($payment),
        ]);
    }

    public function receipt(int $payment)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->receipt($payment),
        ]);
    }
}