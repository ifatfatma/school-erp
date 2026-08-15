<?php

namespace App\Services;

use App\Repositories\RecurringIncomeRepository;
use Illuminate\Validation\ValidationException;

class RecurringIncomeService
{
    public function __construct(
        protected RecurringIncomeRepository $repository
    ) {
    }

    /**
     * List recurring incomes.
     */
    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }

    /**
     * Find recurring income.
     */
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Create recurring income.
     */
    public function create(array $data)
    {
        $data = $this->prepareData($data);

        $this->validateDates($data);

        return $this->repository->create($data);
    }

    /**
     * Update recurring income.
     */
    public function update(
        int $id,
        array $data
    ) {
        $this->repository->find($id);

        $data = $this->prepareData($data);

        $this->validateDates($data);

        return $this->repository->update(
            $id,
            $data
        );
    }

    /**
     * Delete recurring income.
     */
    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Change status.
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
     * Generate income manually.
     */
    public function generateIncome(int $id)
    {
        return $this->repository->generateIncome(
            $id
        );
    }

    /**
     * Generate all due incomes.
     */
    public function generateDue(): int
    {
        return $this->repository->generateDue();
    }

    /**
     * Prepare data.
     */
    protected function prepareData(
        array $data
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Name
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['name'])
        ) {
            $data['name'] =
                trim($data['name']);
        }


        /*
        |--------------------------------------------------------------------------
        | Received From
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['received_from'])
        ) {
            $data['received_from'] =
                trim($data['received_from']);
        }


        /*
        |--------------------------------------------------------------------------
        | Reference
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


        /*
        |--------------------------------------------------------------------------
        | Amount
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['amount'])
        ) {

            $data['amount'] =
                round(
                    (float) $data['amount'],
                    2
                );

            if (
                $data['amount'] <= 0
            ) {

                throw ValidationException::withMessages([
                    'amount' =>
                        'Amount must be greater than zero.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Frequency
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['frequency'])
        ) {

            $this->validateFrequency(
                $data['frequency']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Payment Method
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['payment_method'])
        ) {

            $data['payment_method'] =
                trim(
                    $data['payment_method']
                );

            $this->validatePaymentMethod(
                $data['payment_method']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['status'])
        ) {

            $this->validateStatus(
                $data['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Auto Create
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['auto_create'])
        ) {

            $data['auto_create'] =
                filter_var(
                    $data['auto_create'],
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );

            if (
                $data['auto_create'] === null
            ) {
                $data['auto_create'] = true;
            }
        }


        return $data;
    }

    /**
     * Validate dates.
     */
    protected function validateDates(
        array $data
    ): void {

        if (
            empty($data['start_date'])
        ) {
            return;
        }

        $startDate =
            strtotime(
                $data['start_date']
            );

        if (!$startDate) {

            throw ValidationException::withMessages([
                'start_date' =>
                    'Invalid start date.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | End date
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['end_date'])
        ) {

            $endDate =
                strtotime(
                    $data['end_date']
                );

            if (!$endDate) {

                throw ValidationException::withMessages([
                    'end_date' =>
                        'Invalid end date.',
                ]);
            }


            if (
                $endDate < $startDate
            ) {

                throw ValidationException::withMessages([
                    'end_date' =>
                        'End date must be after or equal to start date.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Next date
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['next_date'])
        ) {

            $nextDate =
                strtotime(
                    $data['next_date']
                );

            if (!$nextDate) {

                throw ValidationException::withMessages([
                    'next_date' =>
                        'Invalid next date.',
                ]);
            }


            if (
                $nextDate < $startDate
            ) {

                throw ValidationException::withMessages([
                    'next_date' =>
                        'Next date cannot be before start date.',
                ]);
            }


            if (
                !empty($data['end_date']) &&
                $nextDate >
                    strtotime(
                        $data['end_date']
                    )
            ) {

                throw ValidationException::withMessages([
                    'next_date' =>
                        'Next date cannot be after end date.',
                ]);
            }
        }
    }

    /**
     * Validate frequency.
     */
    protected function validateFrequency(
        string $frequency
    ): void {

        $allowed = [

            'Daily',
            'Weekly',
            'Monthly',
            'Quarterly',
            'Yearly',

        ];

        if (
            !in_array(
                $frequency,
                $allowed,
                true
            )
        ) {

            throw ValidationException::withMessages([
                'frequency' =>
                    'Invalid recurring frequency.',
            ]);
        }
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

            'Active',
            'Paused',
            'Completed',
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
                    'Invalid recurring income status.',
            ]);
        }
    }
}