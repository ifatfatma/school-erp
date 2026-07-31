<?php

namespace App\Repositories\Academic;

use App\Models\LeaveRequest;

class LeaveRequestRepository
{
    protected $model;

    public function __construct(LeaveRequest $model)
    {
        $this->model = $model;
    }

    /**
     * Paginated Listing
     */
    public function paginate(array $filters = [])
    {
        $query = $this->model
            ->with([
                'teacher:id,first_name,last_name',
                'leaveType:id,name',
                'approver:id,name'
            ])
            ->where('school_id', auth()->user()->school_id);

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {

                $q->whereHas('teacher', function ($teacher) use ($search) {
                    $teacher->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                });

                $q->orWhereHas('leaveType', function ($leaveType) use ($search) {
                    $leaveType->where('name', 'like', "%{$search}%");
                });

                $q->orWhere('reason', 'like', "%{$search}%");
            });
        }

        // Teacher Filter
        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        // Leave Type Filter
        if (!empty($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        // Status Filter
        if ($filters['status'] !== '' && isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date Range
        if (!empty($filters['from_date'])) {
            $query->whereDate('from_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('to_date', '<=', $filters['to_date']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate(
            $filters['per_page'] ?? 15
        );
    }

    /**
     * All Records
     */
    public function all()
    {
        return $this->model
            ->with([
                'teacher',
                'leaveType'
            ])
            ->where('school_id', auth()->user()->school_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Find by ID
     */
    public function find($id)
    {
        return $this->model
            ->with([
                'teacher',
                'leaveType',
                'approver',
                'creator',
                'updater'
            ])
            ->where('school_id', auth()->user()->school_id)
            ->findOrFail($id);
    }

    /**
     * Store
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update($id, array $data)
    {
        $leaveRequest = $this->find($id);

        $leaveRequest->update($data);

        return $leaveRequest->fresh([
            'teacher',
            'leaveType',
            'approver'
        ]);
    }

    /**
     * Delete
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}