<?php

namespace App\Services;

use App\Repositories\Academic\ClassRepository;

class ClassService
{
    public function __construct(
        protected ClassRepository $repository
    ) {}

    public function paginate(array $filters = [])
    {
        $filters['school_id'] = auth()->user()->school_id;

        return $this->repository->paginate($filters);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function store(array $data)
    {
        $data['school_id'] = auth()->user()->school_id;
        $data['created_by'] = auth()->id();

        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        $class = $this->repository->find($id);

        $data['updated_by'] = auth()->id();

        return $this->repository->update($class, $data);
    }

    public function delete(int $id)
    {
        $class = $this->repository->find($id);

        return $this->repository->delete($class);
    }

    /**
     * Active class
     */
    public function active()
    {
        return $this->repository->active();
    }
}