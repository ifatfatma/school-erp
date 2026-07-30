<?php

namespace App\Services;

use App\Models\Timetable;
use App\Repositories\Academic\TimetableRepository;
use Illuminate\Validation\ValidationException;

class TimetableService
{
    protected TimetableRepository $repository;

    public function __construct(TimetableRepository $repository)
    {
        $this->repository = $repository;
    }

    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters);
    }

    public function find(int $id): ?Timetable
    {
        return $this->repository->find($id);
    }

    public function store(array $data): Timetable
    {
        $data['school_id'] = auth()->user()->school_id;
        $data['created_by'] = auth()->id();

        if ($this->repository->hasConflict($data)) {
            throw ValidationException::withMessages([
                'start_time' => [
                    'This timetable conflicts with an existing period.'
                ]
            ]);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Timetable
    {
        $timetable = $this->repository->find($id);

        if (!$timetable) {
            abort(404, 'Timetable not found.');
        }

        $data['school_id'] = auth()->user()->school_id;
        $data['updated_by'] = auth()->id();

        if ($this->repository->hasConflict($data, $id)) {
            throw ValidationException::withMessages([
                'start_time' => [
                    'This timetable conflicts with an existing period.'
                ]
            ]);
        }

        return $this->repository->update($timetable, $data);
    }

    public function delete(int $id): bool
    {
        $timetable = $this->repository->find($id);

        if (!$timetable) {
            abort(404, 'Timetable not found.');
        }

        return $this->repository->delete($timetable);
    }
}