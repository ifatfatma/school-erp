<?php

namespace App\Repositories\Academic;

use App\Models\Section;

class SectionRepository
{
    public function paginate(array $filters = [], int $perPage = 10)
    {
        $query = Section::query()->with(['class', 'school:id,name,code']);

        if (isset($filters['school_id']) && $filters['school_id'] !== '') {
            if ($filters['school_id'] === 'all' || $filters['school_id'] === '0') {
                $query->withoutGlobalScope(\App\Scopes\SchoolScope::class);
            } else {
                $query->withoutGlobalScope(\App\Scopes\SchoolScope::class)
                      ->where('sections.school_id', $filters['school_id']);
            }
        }

        return $query
            ->when(!empty($filters['search']), function ($query) use ($filters) {

                $search = trim($filters['search']);

                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('class', function ($class) use ($search) {

                            $class->where('name', 'like', "%{$search}%");

                        });

                });

            })
            ->when(!empty($filters['class_id']), function ($query) use ($filters) {

                $query->where('class_id', $filters['class_id']);

            })
            ->when(isset($filters['status']) && $filters['status'] !== '', function ($query) use ($filters) {

                $status = filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($status !== null) {
                    $query->where('status', $status ? 1 : 0);
                }

            })
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Section
    {
        return Section::with('class')->find($id);
    }

    public function create(array $data): Section
    {
        return Section::create($data);
    }

    public function update(Section $section, array $data): bool
    {
        return $section->update($data);
    }

    public function delete(Section $section): bool
    {
        return $section->delete();
    }

    public function exists(?int $schoolId, int $classId, string $name, ?int $ignoreId = null): bool
    {
        return Section::when($schoolId, function ($query, $schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->where('class_id', $classId)
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($name))])
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();
    }
}