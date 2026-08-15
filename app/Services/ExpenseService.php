<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
    public function __construct(
        protected ExpenseRepository $repository
    ) {
    }

    /**
     * List expenses.
     */
    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }

    /**
     * Find expense.
     */
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Create expense.
     */
    public function create(
        array $data,
        ?UploadedFile $attachment = null
    ) {
        return DB::transaction(function () use (
            $data,
            $attachment
        ) {

            if ($attachment) {

                $data['attachment'] =
                    $attachment->store(
                        'expenses',
                        'public'
                    );
            }

            return $this->repository->create(
                $data
            );
        });
    }

    /**
     * Update expense.
     */
    public function update(
        int $id,
        array $data,
        ?UploadedFile $attachment = null
    ) {
        return DB::transaction(function () use (
            $id,
            $data,
            $attachment
        ) {

            $expense =
                $this->repository->find($id);


            if ($attachment) {

                if (
                    $expense->attachment &&
                    !filter_var(
                        $expense->attachment,
                        FILTER_VALIDATE_URL
                    )
                ) {

                    Storage::disk('public')
                        ->delete(
                            $expense->attachment
                        );
                }


                $data['attachment'] =
                    $attachment->store(
                        'expenses',
                        'public'
                    );
            }


            return $this->repository->update(
                $id,
                $data
            );
        });
    }

    /**
     * Delete expense.
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {

            $expense =
                $this->repository->find($id);


            if (
                $expense->attachment &&
                !filter_var(
                    $expense->attachment,
                    FILTER_VALIDATE_URL
                )
            ) {

                Storage::disk('public')
                    ->delete(
                        $expense->attachment
                    );
            }


            return $expense->delete();
        });
    }

    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        string $status
    ) {
        return $this->repository->update(
            $id,
            [
                'status' => $status,
            ]
        );
    }

    /**
     * Total expenses.
     */
    public function total(
        array $filters = []
    ): float {
        return $this->repository->total(
            $filters
        );
    }
}