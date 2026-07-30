<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function query()
    {
        return $this->model->newQuery();
    }

    public function all(array $columns = ['*'])
    {
        return $this->query()->get($columns);
    }

    public function paginate(int $perPage = 20)
    {
        return $this->query()->paginate($perPage);
    }

    public function find(int|string $id)
    {
        return $this->query()->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data)
    {
        $model = $this->find($id);

        $model->update($data);

        return $model->fresh();
    }

    public function delete(int|string $id): bool
    {
        return $this->find($id)->delete();
    }

    public function restore(int|string $id)
    {
        return $this->model
            ->onlyTrashed()
            ->findOrFail($id)
            ->restore();
    }
}