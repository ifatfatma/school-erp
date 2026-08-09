<?php

namespace App\Repositories;

use App\Models\Guardian;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
class GuardianRepository
{
    protected Guardian $model;

    public function __construct(Guardian $model)
    {
        $this->model = $model;
    }

    /**
     * List Guardians
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->with([
                'students',
                'school',
            ])
            ->filter($filters)
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find Guardian
     */
    public function find(int $id): Guardian
    {
        return $this->model
            ->with([
                'students',
                'school',
            ])
            ->findOrFail($id);
    }

    /**
     * Create Guardian
     */
    public function create(array $data): Guardian
    {
        $guardian = $this->model->create($data);

        if (!empty($data['student_ids'])) {

            $sync = [];

            foreach ($data['student_ids'] as $studentId) {

                $sync[$studentId] = [
                    'is_primary' => $studentId == ($data['primary_student_id'] ?? null),
                ];

            }

            $guardian->students()->sync($sync);
        }

        return $guardian->load('students');
    }

    /**
     * Update Guardian
     */
    public function update(Guardian $guardian, array $data): Guardian
    {
        $guardian->update($data);

        if (isset($data['student_ids'])) {

            $sync = [];

            foreach ($data['student_ids'] as $studentId) {

                $sync[$studentId] = [
                    'is_primary' => $studentId == ($data['primary_student_id'] ?? null),
                ];

            }

            $guardian->students()->sync($sync);
        }

        return $guardian->fresh()->load('students');
    }

    /**
     * Delete Guardian
     */
    public function delete(Guardian $guardian): bool
    {
        return $guardian->delete();
    }

    /**
     * Attach Student
     */
    public function attachStudent(
        Guardian $guardian,
        int $studentId,
        bool $primary = false
    ): void {

        $guardian->students()->syncWithoutDetaching([
            $studentId => [
                'is_primary' => $primary,
            ],
        ]);
    }

    /**
     * Detach Student
     */
    public function detachStudent(
        Guardian $guardian,
        int $studentId
    ): void {

        $guardian->students()->detach($studentId);
    }

    /**
     * Update Primary Guardian
     */
    public function updatePrimaryStudent(
        Guardian $guardian,
        int $studentId
    ): void {

        foreach ($guardian->students as $student) {

            $guardian->students()->updateExistingPivot(
                $student->id,
                [
                    'is_primary' => $student->id == $studentId,
                ]
            );
        }
    }
}