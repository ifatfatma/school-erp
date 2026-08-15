<?php

namespace App\Services;

use App\Repositories\ExpenseCategoryRepository;

class ExpenseCategoryService
{
    public function __construct(
        protected ExpenseCategoryRepository $repository
    ) {
    }


    public function list(
        array $filters = []
    ) {
        return $this->repository->list(
            $filters
        );
    }


    public function find(
        int $id
    ) {
        return $this->repository->find(
            $id
        );
    }


    public function create(
        array $data
    ) {
        return $this->repository->create(
            $data
        );
    }


    public function update(
        int $id,
        array $data
    ) {
        return $this->repository->update(
            $id,
            $data
        );
    }


    public function delete(
        int $id
    ) {
        return $this->repository->delete(
            $id
        );
    }


    public function toggleStatus(
        int $id
    ) {
        return $this->repository->toggleStatus(
            $id
        );
    }
}