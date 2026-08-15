<?php

namespace App\Services;

use App\Repositories\RecurringExpenseRepository;

class RecurringExpenseService
{
    public function __construct(
        protected RecurringExpenseRepository $repository
    ) {
    }

    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update(
            $id,
            $data
        );
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function changeStatus(
        int $id,
        string $status
    ) {
        return $this->repository->changeStatus(
            $id,
            $status
        );
    }

    public function toggleAutoCreate(int $id)
    {
        return $this->repository->toggleAutoCreate(
            $id
        );
    }

    public function generateExpense(int $id)
    {
        return $this->repository->generateExpense(
            $id
        );
    }
}