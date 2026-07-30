<?php

namespace App\Services;

use App\Models\LeaveType;
use App\Repositories\Academic\LeaveTypeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveTypeService
{
    protected LeaveTypeRepository $repository;

    public function __construct(LeaveTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated records
     */
    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Get all records
     */
    public function all()
    {
        return $this->repository->all();
    }

    /**
     * Find record
     */
    public function find(int $id): LeaveType
    {
        return $this->repository->find($id);
    }

    /**
     * Store
     */
    public function store(array $data): LeaveType
    {
        return DB::transaction(function () use ($data) {

            $data['school_id'] = Auth::user()->school_id;
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            return $this->repository->create($data);
        });
    }

    /**
     * Update
     */
    public function update(int $id, array $data): LeaveType
    {
        return DB::transaction(function () use ($id, $data) {

            $leaveType = $this->repository->find($id);

            $data['updated_by'] = Auth::id();

            return $this->repository->update($leaveType, $data);
        });
    }

    /**
     * Delete
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {

            $leaveType = $this->repository->find($id);

            return $this->repository->delete($leaveType);
        });
    }
}