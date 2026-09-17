<?php

namespace App\Services\Hostel;

use App\Models\HostelBuilding;
use App\Repositories\Hostel\HostelBuildingRepository;
use Illuminate\Support\Facades\DB;

class HostelBuildingService
{
    public function __construct(
        protected HostelBuildingRepository $repository
    ) {
    }

    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function find(int $id): ?HostelBuilding
    {
        return $this->repository->find($id);
    }

    public function create(array $data): HostelBuilding
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    public function update(
        HostelBuilding $building,
        array $data
    ): HostelBuilding {
        return DB::transaction(function () use ($building, $data) {
            return $this->repository->update($building, $data);
        });
    }

    public function delete(HostelBuilding $building): bool
    {
        return DB::transaction(function () use ($building) {
            return $this->repository->delete($building);
        });
    }
}