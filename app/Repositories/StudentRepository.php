<?php

namespace App\Repositories;

use App\Models\Student;

class StudentRepository
{
    /**
     * Student List
     */
    public function paginate(array $filters = [])
    {
        return Student::query()

            ->with([
                'class',
                'section',
            ])

            ->when($filters['search'] ?? null, function ($query, $search) {

                $query->search($search);

            })

            ->when($filters['class_id'] ?? null, function ($query, $classId) {

                $query->where('class_id', $classId);

            })

            ->when($filters['section_id'] ?? null, function ($query, $sectionId) {

                $query->where('section_id', $sectionId);

            })

            ->when(isset($filters['status']) && $filters['status'] !== '', function ($query) use ($filters) {

                $query->where('status', $filters['status']);

            })

            ->latest()

            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find Student
     */
    public function find(int $id): Student
    {
        return Student::with([
            'class',
            'section',
        ])->findOrFail($id);
    }

    /**
     * Find Admission No
     */
    public function findByAdmissionNo(string $admissionNo): ?Student
    {
        return Student::where(
            'admission_no',
            $admissionNo
        )->first();
    }

    /**
     * Create Student
     */
    public function create(array $data): Student
    {
        return Student::create($data);
    }

    /**
     * Update Student
     */
    public function update(
        Student $student,
        array $data
    ): Student {

        $student->update($data);

        return $student->fresh([
            'class',
            'section',
        ]);
    }

    /**
     * Delete Student
     */
    public function delete(Student $student): bool
    {
        return $student->delete();
    }

    /**
     * Active Students
     */
    public function active()
    {
        return Student::active()

            ->orderBy('first_name')

            ->get();
    }

    /**
     * Students By Class
     */
    public function byClass(
        int $classId,
        ?int $sectionId = null
    ) {

        return Student::active()

            ->where('class_id', $classId)

            ->when($sectionId, function ($query) use ($sectionId) {

                $query->where(
                    'section_id',
                    $sectionId
                );

            })

            ->orderBy('roll_no')

            ->orderBy('first_name')

            ->get();
    }

    /**
     * Student Count
     */
    public function count(): int
    {
        return Student::count();
    }

    /**
     * Generate Admission Number
     */
    public function nextAdmissionNumber(): string
    {
        $last = Student::latest('id')->first();

        if (!$last) {
            return 'ADM000001';
        }

        $number = (int) preg_replace(
            '/[^0-9]/',
            '',
            $last->admission_no
        );

        return 'ADM' . str_pad(
            $number + 1,
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}