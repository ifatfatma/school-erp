<?php

namespace App\Repositories;

use App\Models\FeePayment;
use App\Models\FeePaymentItem;
use App\Models\StudentFeeDue;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeeCollectionRepository
{
    /**
     * Get fee due.
     */
    public function due(int $id): StudentFeeDue
    {
        return StudentFeeDue::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->with([
                'student',
                'student.class',
                'student.section',
                'feeType',
                'feeStructure',
                'payments',
            ])
            ->findOrFail($id);
    }


    /**
     * Collect payment.
     */
    public function collect(
        array $data,
        int $schoolId,
        int $userId
    ): FeePayment {

        return DB::transaction(function () use (
            $data,
            $schoolId,
            $userId
        ) {

            /*
            |--------------------------------------------------------------------------
            | Prepare Items
            |--------------------------------------------------------------------------
            */

            $items = [];


            /*
            |--------------------------------------------------------------------------
            | Multi Due
            |--------------------------------------------------------------------------
            */

            if (
                isset($data['items']) &&
                is_array($data['items']) &&
                count($data['items']) > 0
            ) {

                $items = $data['items'];

            }


            /*
            |--------------------------------------------------------------------------
            | Single Due
            |--------------------------------------------------------------------------
            */

            elseif (
                !empty(
                    $data['student_fee_due_id']
                )
            ) {

                $items[] = [

                    'student_fee_due_id' =>
                        $data['student_fee_due_id'],

                    'amount' =>
                        $data['amount'] ?? 0,

                ];

            }


            /*
            |--------------------------------------------------------------------------
            | Validate Items
            |--------------------------------------------------------------------------
            */

            if (empty($items)) {

                throw ValidationException::withMessages([
                    'items' => [
                        'At least one fee due is required.'
                    ]
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            $studentId =
                $data['student_id'] ?? null;


            /*
            |--------------------------------------------------------------------------
            | Academic Session
            |--------------------------------------------------------------------------
            */

            $academicSessionId =
                $data['academic_session_id']
                ?? null;


            /*
            |--------------------------------------------------------------------------
            | Payment Details
            |--------------------------------------------------------------------------
            */

            $paymentDate =
                $data['payment_date']
                ?? now()->toDateString();


            $paymentMethod =
                $data['payment_method']
                ?? 'Cash';


            $transactionNo =
                $data['transaction_no']
                ?? null;


            $bankName =
                $data['bank_name']
                ?? null;


            $chequeNo =
                $data['cheque_no']
                ?? null;


            $remarks =
                $data['remarks']
                ?? null;


            /*
            |--------------------------------------------------------------------------
            | Validate Payment Method
            |--------------------------------------------------------------------------
            */

            $allowedMethods = [
                'Cash',
                'Card',
                'Bank Transfer',
                'UPI',
                'Cheque',
                'Online',
                'Other',
            ];


            if (
                !in_array(
                    $paymentMethod,
                    $allowedMethods,
                    true
                )
            ) {

                throw ValidationException::withMessages([
                    'payment_method' => [
                        'Invalid payment method.'
                    ]
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Process Dues
            |--------------------------------------------------------------------------
            */

            $totalAmount = 0;

            $processedDues = [];


            foreach (
                $items as $index => $item
            ) {

                $dueId =
                    $item['student_fee_due_id']
                    ?? null;


                if (!$dueId) {

                    throw ValidationException::withMessages([
                        "items.$index.student_fee_due_id" => [
                            'Fee due is required.'
                        ]
                    ]);

                }


                $itemAmount =
                    (float) (
                        $item['amount'] ?? 0
                    );


                if ($itemAmount <= 0) {

                    throw ValidationException::withMessages([
                        "items.$index.amount" => [
                            'Payment amount must be greater than zero.'
                        ]
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | Lock Fee Due
                |--------------------------------------------------------------------------
                */

                $due = StudentFeeDue::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $dueId
                    )
                    ->lockForUpdate()
                    ->first();


                if (!$due) {

                    throw ValidationException::withMessages([
                        "items.$index.student_fee_due_id" => [
                            'Fee due not found.'
                        ]
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | Student Check
                |--------------------------------------------------------------------------
                */

                if (
                    $studentId !== null &&
                    (int) $due->student_id !==
                    (int) $studentId
                ) {

                    throw ValidationException::withMessages([
                        "items.$index.student_fee_due_id" => [
                            'Fee due does not belong to selected student.'
                        ]
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                if (
                    $due->status === 'Paid'
                ) {

                    throw ValidationException::withMessages([
                        "items.$index.amount" => [
                            'This fee due is already paid.'
                        ]
                    ]);

                }


                if (
                    $due->status === 'Cancelled'
                ) {

                    throw ValidationException::withMessages([
                        "items.$index.amount" => [
                            'This fee due has been cancelled.'
                        ]
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | Calculate Balance
                |--------------------------------------------------------------------------
                */

                $netAmount =
                    (float) (
                        $due->net_amount ?? 0
                    );


                $paidAmount =
                    (float) (
                        $due->paid_amount ?? 0
                    );


                $balanceAmount =
                    $due->balance_amount !== null
                        ? (float) $due->balance_amount
                        : max(
                            0,
                            $netAmount - $paidAmount
                        );


                /*
                |--------------------------------------------------------------------------
                | Prevent Over Payment
                |--------------------------------------------------------------------------
                */

                if (
                    $itemAmount >
                    ($balanceAmount + 0.01)
                ) {

                    throw ValidationException::withMessages([
                        "items.$index.amount" => [
                            'Payment amount cannot exceed outstanding balance of ₹'
                            . number_format(
                                $balanceAmount,
                                2
                            )
                            . '.'
                        ]
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | Total
                |--------------------------------------------------------------------------
                */

                $totalAmount +=
                    $itemAmount;


                /*
                |--------------------------------------------------------------------------
                | Save Processed Due
                |--------------------------------------------------------------------------
                */

                $processedDues[] = [

                    'due' =>
                        $due,

                    'amount' =>
                        $itemAmount,

                ];


                /*
                |--------------------------------------------------------------------------
                | Student
                |--------------------------------------------------------------------------
                */

                if (!$studentId) {

                    $studentId =
                        $due->student_id;

                }


                /*
                |--------------------------------------------------------------------------
                | Academic Session
                |--------------------------------------------------------------------------
                */

                if (!$academicSessionId) {

                    $academicSessionId =
                        $due->academic_session_id
                        ?? $due->student?->academic_session_id;

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Validate Student
            |--------------------------------------------------------------------------
            */

            if (!$studentId) {

                throw ValidationException::withMessages([
                    'student_id' => [
                        'Student is required.'
                    ]
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Validate Academic Session
            |--------------------------------------------------------------------------
            */

            if (!$academicSessionId) {

                throw ValidationException::withMessages([
                    'academic_session_id' => [
                        'Academic session is required.'
                    ]
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Generate Receipt
            |--------------------------------------------------------------------------
            */

            $receiptNo =
                $this->generateReceiptNo(
                    $schoolId
                );


            /*
            |--------------------------------------------------------------------------
            | Create Payment
            |--------------------------------------------------------------------------
            */

            $payment = FeePayment::create([

                'school_id' =>
                    $schoolId,

                'academic_session_id' =>
                    $academicSessionId,

                'student_id' =>
                    $studentId,

                'receipt_no' =>
                    $receiptNo,

                'payment_date' =>
                    $paymentDate,

                'total_amount' =>
                    $totalAmount,

                'payment_method' =>
                    $paymentMethod,

                'transaction_no' =>
                    $transactionNo,

                'bank_name' =>
                    $bankName,

                'cheque_no' =>
                    $chequeNo,

                'remarks' =>
                    $remarks,

                'status' =>
                    'Success',

                'created_by' =>
                    $userId,

                'updated_by' =>
                    $userId,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Create Payment Items + Update Dues
            |--------------------------------------------------------------------------
            */

            foreach (
                $processedDues as $processed
            ) {

                /** @var StudentFeeDue $due */
                $due =
                    $processed['due'];

                $itemAmount =
                    $processed['amount'];


                /*
                |--------------------------------------------------------------------------
                | Payment Item
                |--------------------------------------------------------------------------
                */

                FeePaymentItem::create([

                    'fee_payment_id' =>
                        $payment->id,

                    'student_fee_due_id' =>
                        $due->id,

                    'amount' =>
                        $itemAmount,

                ]);


                /*
                |--------------------------------------------------------------------------
                | New Paid Amount
                |--------------------------------------------------------------------------
                */

                $newPaidAmount =
                    (float) $due->paid_amount
                    +
                    $itemAmount;


                /*
                |--------------------------------------------------------------------------
                | New Balance
                |--------------------------------------------------------------------------
                */

                $newBalanceAmount =
                    max(
                        0,
                        (float) $due->net_amount
                        -
                        $newPaidAmount
                    );


                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                if (
                    $newBalanceAmount <= 0
                ) {

                    $newStatus = 'Paid';

                } elseif (
                    $newPaidAmount > 0
                ) {

                    $newStatus = 'Partial';

                } else {

                    $newStatus = 'Pending';

                }


                /*
                |--------------------------------------------------------------------------
                | Update Due
                |--------------------------------------------------------------------------
                */

                $due->update([

                    'paid_amount' =>
                        $newPaidAmount,

                    'balance_amount' =>
                        $newBalanceAmount,

                    'status' =>
                        $newStatus,

                    'updated_by' =>
                        $userId,

                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Return
            |--------------------------------------------------------------------------
            */

            return $payment->load([
                'student',
                'items',
                'items.feeDue',
            ]);

        });

    }


    /**
     * Generate unique receipt number.
     */
    protected function generateReceiptNo(
        int $schoolId
    ): string {

        $year =
            now()->format('Y');


        $lastPayment =
            FeePayment::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->whereYear(
                    'created_at',
                    $year
                )
                ->latest('id')
                ->lockForUpdate()
                ->first();


        $number = 1;


        if ($lastPayment) {

            $receiptNo =
                $lastPayment->receipt_no;


            if (
                preg_match(
                    '/(\d+)$/',
                    $receiptNo,
                    $matches
                )
            ) {

                $number =
                    ((int) $matches[1]) + 1;

            }

        }


        return sprintf(
            'RCPT-%s-%06d',
            $year,
            $number
        );

    }


    /**
     * Payment history for a due.
     */
    public function payments(
        int $dueId
    ) {

        return FeePayment::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->whereHas(
                'items',
                function ($query) use ($dueId) {

                    $query->where(
                        'student_fee_due_id',
                        $dueId
                    );

                }
            )
            ->with([
                'student',
                'items',
                'items.feeDue',
            ])
            ->latest('id')
            ->get();

    }


    /**
     * Get payment by ID.
     */
    public function findPayment(
        int $id
    ): FeePayment {

        return FeePayment::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->with([
                'student',
                'items',
                'items.feeDue',
            ])
            ->findOrFail($id);

    }

public function receipt(int $paymentId): FeePayment
{
    return FeePayment::query()
        ->where(
            'school_id',
            auth()->user()->school_id
        )
        ->with([
            'student',
            'items',
            'items.feeDue',
            'items.feeDue.feeType',
            'items.feeDue.feeStructure',
        ])
        ->findOrFail($paymentId);
}
}