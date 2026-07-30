<?php

namespace App\Repositories\Academic;

use App\Models\Teacher;

class TeacherRepository
{
    public function paginate(array $filters = [])
    {
        $query = Teacher::query()
            ->where('school_id', auth()->user()->school_id);

        if (!empty($filters['search'])) {

            $search = $filters['search'];

            $query->where(function ($q) use ($search) {

                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");

            });

        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        return $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 10);
    }

    public function find(int $id): ?Teacher
    {
        return Teacher::where('school_id', auth()->user()->school_id)
            ->find($id);
    }

    public function create(array $data): Teacher
    {
        return Teacher::create($data);
    }

    public function update(Teacher $teacher, array $data): Teacher
    {
        $teacher->update($data);

        return $teacher->fresh();
    }

    public function delete(Teacher $teacher): bool
    {
        return $teacher->delete();
    }

    public function exists(string $employeeId, ?int $ignoreId = null): bool
    {
        $query = Teacher::where('school_id', auth()->user()->school_id)
            ->where('employee_id', $employeeId);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}