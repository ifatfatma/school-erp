<?php

namespace App\Repositories\Academic;

use App\Models\TeacherAttendance;

class TeacherAttendanceRepository
{
    public function paginate(array $filters = [])
    {
        return TeacherAttendance::with(['teacher'])
            ->when($filters['teacher_id'] ?? null, fn($q, $v) => $q->where('teacher_id', $v))
            ->when($filters['attendance_date'] ?? null, fn($q, $v) => $q->whereDate('attendance_date', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->latest('attendance_date')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function find($id)
    {
        return TeacherAttendance::with('teacher')->findOrFail($id);
    }

    public function create(array $data)
    {
        return TeacherAttendance::create($data);
    }

    public function update(TeacherAttendance $attendance, array $data)
    {
        $attendance->update($data);
        return $attendance->fresh();
    }

    public function delete(TeacherAttendance $attendance)
    {
        return $attendance->delete();
    }
}