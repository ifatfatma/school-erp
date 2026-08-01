<?php

namespace App\Services;

use App\Models\StudentAttendance;
use App\Repositories\StudentAttendanceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StudentAttendanceService
{
    protected StudentAttendanceRepository $repository;

    public function __construct(StudentAttendanceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Attendance List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Find Attendance
     */
    public function find(int $id): StudentAttendance
    {
        return $this->repository->find($id);
    }

    /**
     * Store Attendance
     */
    public function store(array $data): StudentAttendance
    {
        return $this->repository->create($data);
    }

    /**
     * Update Attendance
     */
    public function update(
        StudentAttendance $attendance,
        array $data
    ): StudentAttendance {

        return $this->repository->update(
            $attendance,
            $data
        );
    }

    /**
     * Delete Attendance
     */
    public function delete(StudentAttendance $attendance): bool
    {
        return $this->repository->delete($attendance);
    }

    /**
     * Bulk Attendance
     */
    public function bulkStore(array $records): void
    {
        $this->repository->bulkStore($records);
    }

    /**
     * Attendance By Date
     */
    public function byDate(
        string $date,
        int $classId,
        int $sectionId
    ): Collection {

        return $this->repository->byDate(
            $date,
            $classId,
            $sectionId
        );
    }

    /**
     * Monthly Attendance
     */
    public function monthly(
        int $studentId,
        int $month,
        int $year
    ): Collection {

        return $this->repository->monthly(
            $studentId,
            $month,
            $year
        );
    }

    /**
     * Attendance Summary
     */
    public function summary(
        int $studentId,
        int $month,
        int $year
    ): array {

        return $this->repository->summary(
            $studentId,
            $month,
            $year
        );
    }
}