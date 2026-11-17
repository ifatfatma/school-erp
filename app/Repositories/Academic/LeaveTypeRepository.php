<?php

namespace App\Repositories\Academic;

use App\Models\LeaveType;

class LeaveTypeRepository
{
    /**
     * Resolve target school ID strictly respecting multi-tenancy.
     */
    protected function resolveSchoolId(?array $filters = []): int
    {
        if (!empty($filters['school_id']) && (int)$filters['school_id'] > 0) {
            return (int)$filters['school_id'];
        }

        $user = auth()->user();
        if ($user && $user->school_id) {
            return (int)$user->school_id;
        }

        $tenantId = app(\App\Services\TenantManager::class)->getTenantId();
        if ($tenantId) {
            return (int)$tenantId;
        }

        return (int)(\App\Models\School::first()?->id ?? 1);
    }

    /**
     * Auto-seed standard institutional leave types for a school if none exist.
     */
    public function seedDefaultsForSchool(int $schoolId): void
    {
        if (LeaveType::withoutGlobalScopes()->where('school_id', $schoolId)->exists()) {
            return;
        }

        $defaults = [
            [
                'name' => 'Casual Leave (CL)',
                'description' => 'Short planned leaves for personal affairs or urgent work.',
                'max_days' => 12,
                'is_paid' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Sick / Medical Leave (SL)',
                'description' => 'Medical leaves due to illness or health checkups.',
                'max_days' => 10,
                'is_paid' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Earned Leave (EL)',
                'description' => 'Privilege leave accrued through continuous institutional service.',
                'max_days' => 15,
                'is_paid' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Paid statutory leave for female faculty for childbirth and infant care.',
                'max_days' => 90,
                'is_paid' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Short leave for new fathers.',
                'max_days' => 7,
                'is_paid' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Bereavement Leave',
                'description' => 'Compassionate leave for family bereavement.',
                'max_days' => 5,
                'is_paid' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Leave Without Pay (LWP)',
                'description' => 'Unpaid leave when all paid quotas are exhausted.',
                'max_days' => 30,
                'is_paid' => 0,
                'status' => 1,
            ],
        ];

        foreach ($defaults as $item) {
            LeaveType::create(array_merge($item, [
                'school_id' => $schoolId,
                'created_by' => auth()->id() ?? null,
                'updated_by' => auth()->id() ?? null,
            ]));
        }
    }

    /**
     * Get paginated leave types
     */
    public function paginate(array $filters = [])
    {
        $schoolId = $this->resolveSchoolId($filters);
        $this->seedDefaultsForSchool($schoolId);

        $query = LeaveType::query()->where('school_id', $schoolId);

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
        $sortOrder = $filters['sort_order'] ?? 'asc';

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get all leave types
     */
    public function all()
    {
        $schoolId = $this->resolveSchoolId();
        $this->seedDefaultsForSchool($schoolId);

        return LeaveType::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find by ID
     */
    public function find(int $id): LeaveType
    {
        $schoolId = $this->resolveSchoolId();

        return LeaveType::where('school_id', $schoolId)
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