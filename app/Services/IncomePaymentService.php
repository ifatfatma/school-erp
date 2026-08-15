<?php

namespace App\Services;

use App\Repositories\IncomePaymentRepository;
use Illuminate\Validation\ValidationException;

class IncomePaymentService
{
    public function __construct(
        protected IncomePaymentRepository $repository
    ) {
    }

    /**
     * List payments.
     */
    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }

    /**
     * Find payment.
     */
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Create payment.
     */
    public function create(array $data)
    {
        $data = $this->prepareData($data);

        $this->validatePaymentMethod(
            $data['payment_method']
        );

        $this->validateStatus(
            $data['status'] ?? 'Success'
        );

        if (
            !empty($data['transaction_no'])
        ) {
            $data['transaction_no'] =
                trim($data['transaction_no']);
        }

        if (
            !empty($data['bank_name'])
        ) {
            $data['bank_name'] =
                trim($data['bank_name']);
        }

        if (
            !empty($data['cheque_no'])
        ) {
            $data['cheque_no'] =
                trim($data['cheque_no']);
        }

        if (
            !empty($data['remarks'])
        ) {
            $data['remarks'] =
                trim($data['remarks']);
        }

        return $this->repository->create(
            $data
        );
    }

    /**
     * Update payment.
     */
    public function update(
        int $id,
        array $data
    ) {
        // Verify that the payment belongs
        // to the current school.
        $this->repository->find($id);

        $data = $this->prepareData($data);

        $this->validatePaymentMethod(
            $data['payment_method']
        );

        $this->validateStatus(
            $data['status']
        );

        if (
            !empty($data['transaction_no'])
        ) {
            $data['transaction_no'] =
                trim($data['transaction_no']);
        }

        if (
            !empty($data['bank_name'])
        ) {
            $data['bank_name'] =
                trim($data['bank_name']);
        }

        if (
            !empty($data['cheque_no'])
        ) {
            $data['cheque_no'] =
                trim($data['cheque_no']);
        }

        if (
            !empty($data['remarks'])
        ) {
            $data['remarks'] =
                trim($data['remarks']);
        }

        return $this->repository->update(
            $id,
            $data
        );
    }

    /**
     * Delete payment.
     */
    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Change payment status.
     */
    public function changeStatus(
        int $id,
        string $status
    ) {
        $this->validateStatus($status);

        return $this->repository->changeStatus(
            $id,
            $status
        );
    }

    /**
     * Get payment summary.
     */
    public function summary(
        array $filters = []
    ): array {
        return $this->repository->summary(
            $filters
        );
    }

    /**
     * Get remaining income amount.
     */
    public function remainingAmount(
        int $incomeId
    ): float {
        return $this->repository->remainingAmount(
            $incomeId
        );
    }

    /**
     * Prepare payment data.
     */
    protected function prepareData(
        array $data
    ): array {

        $data['payment_method'] =
            trim(
                $data['payment_method']
            );

        $data['amount'] =
            round(
                (float) $data['amount'],
                2
            );

        if ($data['amount'] <= 0) {
            throw ValidationException::withMessages([
                'amount' =>
                    'Payment amount must be greater than zero.',
            ]);
        }

        $data['status'] =
            $data['status'] ?? 'Success';

        return $data;
    }

    /**
     * Validate payment method.
     */
    protected function validatePaymentMethod(
        string $method
    ): void {

        $allowed = [

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
                $method,
                $allowed,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'payment_method' =>
                    'Invalid payment method.',
            ]);
        }
    }

    /**
     * Validate status.
     */
    protected function validateStatus(
        string $status
    ): void {

        $allowed = [

            'Success',

            'Pending',

            'Cancelled',

        ];

        if (
            !in_array(
                $status,
                $allowed,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'Invalid payment status.',
            ]);
        }
    }
}