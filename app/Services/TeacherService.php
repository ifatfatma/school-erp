<?php

namespace App\Services;

use App\Models\Teacher;
use App\Repositories\Academic\TeacherRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TeacherService
{
    protected TeacherRepository $repository;

    public function __construct(TeacherRepository $repository)
    {
        $this->repository = $repository;
    }

    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters);
    }

    public function find(int $id): ?Teacher
    {
        return $this->repository->find($id);
    }

    public function store(array $data): Teacher
    {
        if ($this->repository->exists($data['employee_id'])) {
            throw ValidationException::withMessages([
                'employee_id' => ['Employee ID already exists.']
            ]);
        }

        return DB::transaction(function () use ($data) {

            $data['school_id'] = auth()->user()->school_id;
            $data['created_by'] = auth()->id();

            if (
                isset($data['photo']) &&
                $data['photo'] instanceof UploadedFile
            ) {
                $data['photo'] = $data['photo']->store(
                    'teachers',
                    'public'
                );
            }

            return $this->repository->create($data);
        });
    }

    public function update(int $id, array $data): Teacher
    {
        $teacher = $this->repository->find($id);

        if (!$teacher) {
            abort(404, 'Teacher not found.');
        }

        if (
            $this->repository->exists(
                $data['employee_id'],
                $teacher->id
            )
        ) {
            throw ValidationException::withMessages([
                'employee_id' => ['Employee ID already exists.']
            ]);
        }

        return DB::transaction(function () use ($teacher, $data) {

            $data['updated_by'] = auth()->id();

            if (
                isset($data['photo']) &&
                $data['photo'] instanceof UploadedFile
            ) {

                if (
                    $teacher->photo &&
                    Storage::disk('public')->exists($teacher->photo)
                ) {
                    Storage::disk('public')->delete($teacher->photo);
                }

                $data['photo'] = $data['photo']->store(
                    'teachers',
                    'public'
                );
            } else {
                unset($data['photo']);
            }

            return $this->repository->update(
                $teacher,
                $data
            );
        });
    }

    public function delete(int $id): bool
    {
        $teacher = $this->repository->find($id);

        if (!$teacher) {
            abort(404, 'Teacher not found.');
        }

        if (
            $teacher->photo &&
            Storage::disk('public')->exists($teacher->photo)
        ) {
            Storage::disk('public')->delete($teacher->photo);
        }

        return $this->repository->delete($teacher);
    }
}