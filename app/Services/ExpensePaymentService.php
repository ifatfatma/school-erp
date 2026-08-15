<?php

namespace App\Services;

use App\Repositories\ExpensePaymentRepository;

class ExpensePaymentService
{
    public function __construct(
        protected ExpensePaymentRepository $repository
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

    public function summary(int $expenseId)
    {
        return $this->repository->summary(
            $expenseId
        );
    }
}