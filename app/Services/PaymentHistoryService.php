<?php

namespace App\Services;

use App\Repositories\PaymentHistoryRepository;
use App\Models\FeePayment;

class PaymentHistoryService
{
    public function __construct(
        protected PaymentHistoryRepository $repository
    ) {
    }

    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }

    public function find(int $id): FeePayment
    {
        return $this->repository->find($id);
    }
}