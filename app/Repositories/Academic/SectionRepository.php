<?php

namespace App\Repositories\Academic;

use App\Models\Section;

class SectionRepository
{
    public function paginate(array $filters = [], int $perPage = 10)
    {
        return Section::query()
            ->with('class')
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

    public function exists(int $schoolId, int $classId, string $name, ?int $ignoreId = null): bool
    {
        return Section::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($name))])
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();
    }
}