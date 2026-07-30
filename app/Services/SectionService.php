<?php

namespace App\Services;

use App\Models\Section;
use App\Repositories\Academic\SectionRepository;
use Illuminate\Validation\ValidationException;

class SectionService
{
    protected SectionRepository $repository;

    public function __construct(SectionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function paginate(array $filters = [], int $perPage = 10)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id): ?Section
    {
        return $this->repository->find($id);
    }

    public function store(array $data): Section
    {
        // Get logged-in user's school
        $schoolId = auth()->user()->school_id;

        $data['school_id'] = $schoolId;

        if (
            $this->repository->exists(
                $schoolId,
                $data['class_id'],
                $data['name']
            )
        ) {
            throw ValidationException::withMessages([
                'name' => 'Section already exists for this class.'
            ]);
        }

        return $this->repository->create($data);
    }

    public function update(Section $section, array $data): Section
    {
        $schoolId = auth()->user()->school_id;

        if (
            $this->repository->exists(
                $schoolId,
                $data['class_id'],
                $data['name'],
                $section->id
            )
        ) {
            throw ValidationException::withMessages([
                'name' => 'Section already exists for this class.'
            ]);
        }

        $this->repository->update($section, $data);

        return $section->fresh('class');
    }

    public function delete(Section $section): bool
    {
        return $this->repository->delete($section);
    }
}