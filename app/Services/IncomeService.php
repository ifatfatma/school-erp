<?php

namespace App\Services;

use App\Repositories\IncomeRepository;
use Illuminate\Validation\ValidationException;

class IncomeService
{
    public function __construct(
        protected IncomeRepository $repository
    ) {
    }

    /**
     * List incomes.
     */
    public function list(array $filters = [])
    {
        return $this->repository->list(
            $filters
        );
    }

    /**
     * Find income.
     */
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Create income.
     */
    public function create(array $data)
    {
        $data['received_from'] =
            trim($data['received_from']);

        /*
        |--------------------------------------------------------------------------
        | Amount
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['amount']) &&
            (float) $data['amount'] <= 0
        ) {
            throw ValidationException::withMessages([
                'amount' =>
                    'Amount must be greater than zero.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Income Number
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['income_no'])
        ) {

            $incomeNo =
                trim($data['income_no']);

            if (
                $this->repository->existsByIncomeNo(
                    $incomeNo
                )
            ) {
                throw ValidationException::withMessages([
                    'income_no' =>
                        'This income number already exists.',
                ]);
            }

            $data['income_no'] =
                $incomeNo;
        }

        /*
        |--------------------------------------------------------------------------
        | Reference Number
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['reference_no'])
        ) {
            $data['reference_no'] =
                trim($data['reference_no']);
        }

        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['description'])
        ) {
            $data['description'] =
                trim($data['description']);
        }

        return $this->repository->create(
            $data
        );
    }

    /**
     * Update income.
     */
    public function update(
        int $id,
        array $data
    ) {

        /*
        |--------------------------------------------------------------------------
        | Verify income belongs to school
        |--------------------------------------------------------------------------
        */

        $this->repository->find($id);

        $data['received_from'] =
            trim($data['received_from']);

        /*
        |--------------------------------------------------------------------------
        | Amount
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['amount']) &&
            (float) $data['amount'] <= 0
        ) {
            throw ValidationException::withMessages([
                'amount' =>
                    'Amount must be greater than zero.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Income Number
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['income_no'])
        ) {

            $incomeNo =
                trim($data['income_no']);

            if (
                $this->repository->existsByIncomeNo(
                    $incomeNo,
                    $id
                )
            ) {
                throw ValidationException::withMessages([
                    'income_no' =>
                        'This income number already exists.',
                ]);
            }

            $data['income_no'] =
                $incomeNo;
        }

        /*
        |--------------------------------------------------------------------------
        | Reference Number
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['reference_no'])
        ) {
            $data['reference_no'] =
                trim($data['reference_no']);
        }

        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['description'])
        ) {
            $data['description'] =
                trim($data['description']);
        }

        return $this->repository->update(
            $id,
            $data
        );
    }

    /**
     * Delete income.
     */
    public function delete(int $id)
    {
        return $this->repository->delete(
            $id
        );
    }

    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        string $status
    ) {

        $allowedStatuses = [
            'Received',
            'Pending',
            'Cancelled',
        ];

        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'Invalid income status.',
            ]);
        }

        return $this->repository->changeStatus(
            $id,
            $status
        );
    }

    /**
     * Get active income categories.
     */
    public function categories()
    {
        return $this->repository->categories();
    }

    /**
     * Get income summary.
     */
    public function summary(
        array $filters = []
    ): array {

        return [

            'total_amount' =>
                $this->repository->total(
                    $filters
                ),

            'received_amount' =>
                $this->repository->receivedTotal(
                    $filters
                ),

            'pending_amount' =>
                $this->repository->pendingTotal(
                    $filters
                ),

            'cancelled_amount' =>
                $this->repository->cancelledTotal(
                    $filters
                ),

        ];
    }
}