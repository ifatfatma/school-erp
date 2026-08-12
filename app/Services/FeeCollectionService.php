<?php

namespace App\Services;

use App\Repositories\FeeCollectionRepository;

class FeeCollectionService
{
    public function __construct(
        protected FeeCollectionRepository $repository
    ) {
    }

    public function students(array $filters = [])
    {
        return $this->repository->students($filters);
    }

    public function dues(int $studentId)
    {
        return $this->repository->dues($studentId);
    }

    public function collect(array $data)
    {
        return $this->repository->collect(
            $data,
            auth()->user()->school_id,
            auth()->id()
        );
    }

    public function show(int $paymentId)
    {
        return $this->repository->show(
            $paymentId
        );
    }

    public function receipt(int $paymentId)
    {
        return $this->repository->receipt(
            $paymentId
        );
    }
}