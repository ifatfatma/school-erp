<?php

namespace App\Repositories\Academic;

use App\Models\SchoolClass;

class ClassRepository
{
    public function paginate(array $filters = [])
    {
        $query = SchoolClass::query();

        // School Filter
        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('numeric_name', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        return $query
            ->latest()
            ->paginate($filters['per_page'] ?? 10);
    }

    public function find(int $id): ?SchoolClass
    {
        return SchoolClass::findOrFail($id);
    }

    public function create(array $data): SchoolClass
    {
        return SchoolClass::create($data);
    }

    public function update(SchoolClass $class, array $data): SchoolClass
    {
        $class->update($data);

        return $class->fresh();
    }

    public function delete(SchoolClass $class): bool
    {
        return $class->delete();
    }

    public function exists(string $name, int $schoolId, ?int $ignoreId = null): bool
    {
        return SchoolClass::where('school_id', $schoolId)
            ->where('name', $name)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();
    }

    /**
     * Active Exams
     */
    public function active()
    {
        return $this->model

            ->where('school_id', auth()->user()->school_id)

            ->where('status', true)

            ->orderBy('id')

            ->get();
    }

}