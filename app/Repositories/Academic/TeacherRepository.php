<?php

namespace App\Repositories\Academic;

use App\Models\Teacher;

class TeacherRepository
{
    public function paginate(array $filters = [])
    {
        $query = Teacher::query()->with('school:id,name,code');

        $user = auth()->user();
        $isSuperAdmin = $user && (
            $user->user_type === 'super_admin' || 
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))) ||
            ($user->roles && $user->roles->contains('name', 'Super Admin'))
        );

        if (!$isSuperAdmin && $user && $user->school_id) {
            $query->where('teachers.school_id', $user->school_id);
        } else {
            // Super Admin can filter by school_id or view all
            if (isset($filters['school_id']) && $filters['school_id'] !== '') {
                if ($filters['school_id'] === 'all' || $filters['school_id'] === '0') {
                    $query->withoutGlobalScope(\App\Scopes\SchoolScope::class);
                } else {
                    $query->withoutGlobalScope(\App\Scopes\SchoolScope::class)
                          ->where('teachers.school_id', $filters['school_id']);
                }
            } else {
                $query->withoutGlobalScope(\App\Scopes\SchoolScope::class);
            }
        }

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
        $user = auth()->user();
        $isSuperAdmin = $user && (
            $user->user_type === 'super_admin' || 
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))) ||
            ($user->roles && $user->roles->contains('name', 'Super Admin'))
        );

        $query = Teacher::query()->with('school:id,name,code');
        if (!$isSuperAdmin && $user && $user->school_id) {
            $query->where('teachers.school_id', $user->school_id);
        } else {
            $query->withoutGlobalScope(\App\Scopes\SchoolScope::class);
        }

        return $query->find($id);
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