<?php

namespace App\Repositories\Academic;

use App\Models\Timetable;

class TimetableRepository
{
    public function paginate(array $filters = [])
    {
        $query = Timetable::query()
            ->with([
                'class:id,name',
                'section:id,name',
                'subject:id,name',
                'teacher:id,first_name',
            ])
            ->where('school_id', auth()->user()->school_id);

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('room', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
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

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'day';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        return $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 10);
    }

    public function find(int $id): ?Timetable
    {
        return Timetable::with([
                'class',
                'section',
                'subject',
                'teacher',
            ])
            ->where('school_id', auth()->user()->school_id)
            ->find($id);
    }

    public function create(array $data): Timetable
    {
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

        $query = Timetable::where('school_id', auth()->user()->school_id)
            ->where('class_id', $data['class_id'])
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