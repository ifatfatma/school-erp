<?php

namespace App\Services;

use App\Models\Student;
use App\Repositories\StudentRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class StudentService
{
    protected StudentRepository $repository;

    public function __construct(StudentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Student List
     */
    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Find Student
     */
    public function find(int $id): Student
    {
        return $this->repository->find($id);
    }

    /**
     * Store Student
     */
    public function store(array $data): Student
    {
        return DB::transaction(function () use ($data) {

            // Auto Admission No
            if (empty($data['admission_no'])) {
                $data['admission_no'] = $this->repository->nextAdmissionNumber();
            }

            // Duplicate Check
            if ($this->repository->findByAdmissionNo($data['admission_no'])) {
                throw ValidationException::withMessages([
                    'admission_no' => [
                        'Admission number already exists.'
                    ]
                ]);
            }

            // Upload Photo
            if (
                isset($data['photo']) &&
                $data['photo'] instanceof UploadedFile
            ) {
                $data['photo'] = $data['photo']->store(
                    'students',
                    'public'
                );
            }

            $data['school_id'] = Auth::user()->school_id;
            $data['created_by'] = Auth::id();

            return $this->repository->create($data);
        });
    }

    /**
     * Update Student
     */
    public function update(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {

            // Duplicate Admission Number
            if (
                isset($data['admission_no']) &&
                $data['admission_no'] !== $student->admission_no
            ) {

                $exists = $this->repository
                    ->findByAdmissionNo($data['admission_no']);

                if ($exists) {
                    throw ValidationException::withMessages([
                        'admission_no' => [
                            'Admission number already exists.'
                        ]
                    ]);
                }
            }

            // Upload New Photo
            if (
                isset($data['photo']) &&
                $data['photo'] instanceof UploadedFile
            ) {

                if (
                    $student->photo &&
                    Storage::disk('public')->exists($student->photo)
                ) {
                    Storage::disk('public')->delete($student->photo);
                }

                $data['photo'] = $data['photo']->store(
                    'students',
                    'public'
                );
            }

            $data['updated_by'] = Auth::id();

            return $this->repository->update(
                $student,
                $data
            );
        });
    }

    /**
     * Delete Student
     */
    public function delete(Student $student): bool
    {
        return DB::transaction(function () use ($student) {

            if (
                $student->photo &&
                Storage::disk('public')->exists($student->photo)
            ) {
                Storage::disk('public')->delete($student->photo);
            }

            return $this->repository->delete($student);
        });
    }

    /**
     * Active Students
     */
    public function active()
    {
        return $this->repository->active();
    }

    /**
     * Students By Class
     */
    public function byClass(
        int $classId,
        ?int $sectionId = null
    ) {
        return $this->repository->byClass(
            $classId,
            $sectionId
        );
    }
}