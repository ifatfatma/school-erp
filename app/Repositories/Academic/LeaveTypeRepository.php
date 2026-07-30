<?php

namespace App\Repositories\Academic;

use App\Models\LeaveType;

class LeaveTypeRepository
{
    /**
     * Get paginated leave types
     */
    public function paginate(array $filters = [])
    {
        $query = LeaveType::query();

        // School Wise
        if (auth()->check()) {
            $query->where('school_id', auth()->user()->school_id);
        }

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get all leave types
     */
    public function all()
    {
        return LeaveType::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find by ID
     */
    public function find(int $id): LeaveType
    {
        return LeaveType::where('school_id', auth()->user()->school_id)
            ->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): LeaveType
    {
        return LeaveType::create($data);
    }

    /**
     * Update
     */
    public function update(LeaveType $leaveType, array $data): LeaveType
    {
        $leaveType->update($data);

        return $leaveType->refresh();
    }

    /**
     * Delete
     */
    public function delete(LeaveType $leaveType): bool
    {
        return $leaveType->delete();
    }
}