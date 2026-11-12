<?php

namespace App\Repositories\Academic;

use App\Models\Timetable;
use App\Services\TenantManager;

class TimetableRepository
{
    protected function resolveSchoolId(?int $filterSchoolId = null): ?int
    {
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);

        if (!$tenantManager->isSuperAdmin($user)) {
            return $user?->school_id ? (int) $user->school_id : null;
        }

        if ($filterSchoolId !== null && $filterSchoolId > 0) {
            return (int) $filterSchoolId;
        }

        $tenantId = $tenantManager->getTenantId();
        if ($tenantId) {
            return (int) $tenantId;
        }

        return $user?->school_id ? (int) $user->school_id : null;
    }

    public function paginate(array $filters = [])
    {
        $filterSchoolId = !empty($filters['school_id']) && $filters['school_id'] !== 'all' ? (int) $filters['school_id'] : null;
        $schoolId = $this->resolveSchoolId($filterSchoolId);

        $query = Timetable::query()
            ->with([
                'class:id,name',
                'section:id,name',
                'subject:id,name',
                'teacher:id,first_name,last_name',
                'school:id,name,code',
            ]);

        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        }

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('room', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher', function ($q) use ($search) {
                      $q->where(function ($sub) use ($search) {
                          $sub->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
                      });
                  });
            });
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['day'])) {
            $query->where('day', $filters['day']);
        }

        return $query
            ->orderByRaw("FIELD(day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
            ->orderBy('start_time')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id): ?Timetable
    {
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);
        $isSuperAdmin = $tenantManager->isSuperAdmin($user);

        $query = Timetable::with([
            'class',
            'section',
            'subject',
            'teacher',
            'school:id,name,code',
        ]);

        if (!$isSuperAdmin && $user?->school_id) {
            $query->where('school_id', $user->school_id);
        }

        return $query->find($id);
    }

    public function create(array $data): Timetable
    {
        if (empty($data['school_id'])) {
            $data['school_id'] = $this->resolveSchoolId();
        }
        return Timetable::create($data);
    }

    public function update(Timetable $timetable, array $data): Timetable
    {
        $timetable->update($data);

        return $timetable->fresh();
    }

    public function delete(Timetable $timetable): bool
    {
        return $timetable->delete();
    }

    /**
     * Check if a timetable slot overlaps an existing one.
     */
    public function hasConflict(
        array $data,
        ?int $ignoreId = null
    ): bool {
        $schoolId = !empty($data['school_id']) ? (int) $data['school_id'] : $this->resolveSchoolId();

        $query = Timetable::query();
        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        }
        $query->where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->where('day', $data['day'])
            ->where(function ($q) use ($data) {
                $q->where('start_time', '<', $data['end_time'])
                  ->where('end_time', '>', $data['start_time']);
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
