<?php

namespace App\Services;

use App\Models\StudentPromotion;
use App\Repositories\StudentPromotionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentPromotionService
{
    protected StudentPromotionRepository $repository;

    public function __construct(StudentPromotionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Promotion List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Find Promotion
     */
    public function find(int $id): StudentPromotion
    {
        return $this->repository->find($id);
    }

    /**
     * Promote Single Student
     */
    public function promote(array $data): StudentPromotion
    {
        return $this->repository->promote($data);
    }

    /**
     * Bulk Promote Students
     */
    public function bulkPromote(array $data): void
    {
        $this->repository->bulkPromote($data);
    }

    /**
     * Delete Promotion
     */
    public function delete(StudentPromotion $promotion): bool
    {
        return $this->repository->delete($promotion);
    }
}