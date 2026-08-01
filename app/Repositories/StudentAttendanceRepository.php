<?php

namespace App\Repositories;

use App\Models\StudentAttendance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StudentAttendanceRepository
{
    protected StudentAttendance $model;

    public function __construct(StudentAttendance $model)
    {
        $this->model = $model;
    }

    /**
     * List Attendance
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->with([
                'student',
                'class',
                'section',
            ])
            ->filter($filters)
            ->latest('attendance_date')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find Attendance
     */
    public function find(int $id): ?StudentAttendance
    {
        return $this->model
            ->with([
                'student',
                'class',
                'section',
            ])
            ->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): StudentAttendance
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(StudentAttendance $attendance, array $data): StudentAttendance
    {
        $attendance->update($data);

        return $attendance->fresh();
    }

    /**
     * Delete
     */
    public function delete(StudentAttendance $attendance): bool
    {
        return $attendance->delete();
    }

    /**
     * Bulk Attendance Save
     */
    public function bulkStore(array $records): void
    {
        DB::transaction(function () use ($records) {

            foreach ($records as $row) {

                $this->model->updateOrCreate(

                    [
                        'student_id'       => $row['student_id'],
                        'attendance_date'  => $row['attendance_date'],
                    ],

                    [
                        'school_id'        => $row['school_id'],
                        'class_id'         => $row['class_id'],
                        'section_id'       => $row['section_id'],
                        'status'           => $row['status'],
                        'remarks'          => $row['remarks'] ?? null,
                        'check_in'         => $row['check_in'] ?? null,
                        'check_out'        => $row['check_out'] ?? null,
                        'created_by'       => $row['created_by'] ?? null,
                        'updated_by'       => $row['updated_by'] ?? null,
                    ]

                );

            }

        });
    }

    /**
     * Attendance By Date
     */
    public function byDate(string $date, int $classId, int $sectionId): Collection
    {
        return $this->model
            ->whereDate('attendance_date', $date)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->with('student')
            ->get();
    }

    /**
     * Monthly Attendance
     */
    public function monthly(
        int $studentId,
        int $month,
        int $year
    ): Collection {

        return $this->model
            ->where('student_id', $studentId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->orderBy('attendance_date')
            ->get();
    }

    /**
     * Attendance Summary
     */
    public function summary(
        int $studentId,
        int $month,
        int $year
    ): array {

        $records = $this->monthly($studentId, $month, $year);

        return [

            'present' => $records->where('status', 'Present')->count(),

            'absent' => $records->where('status', 'Absent')->count(),

            'late' => $records->where('status', 'Late')->count(),

            'half_day' => $records->where('status', 'Half Day')->count(),

            'leave' => $records->where('status', 'Leave')->count(),

            'total' => $records->count(),

        ];
    }
}