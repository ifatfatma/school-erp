<?php

namespace App\Services;

use App\Repositories\PublisherRepository;
use Illuminate\Validation\ValidationException;

class PublisherService
{
    public function __construct(
        protected PublisherRepository $repository
    ) {
    }

    public function list(array $filters = [])
    {
        return $this->repository->list(
            $filters
        );
    }

    public function find(int $id)
    {
        return $this->repository->find(
            $id
        );
    }

    public function create(array $data)
    {
        $data =
            $this->prepareData($data);

        $this->validateData($data);

        return $this->repository->create(
            $data
        );
    }

    public function update(
        int $id,
        array $data
    ) {

        $this->repository->find($id);

        $data =
            $this->prepareData($data);

        $this->validateData($data);

        return $this->repository->update(
            $id,
            $data
        );
    }

    public function delete(int $id)
    {
        return $this->repository->delete(
            $id
        );
    }

    public function changeStatus(
        int $id,
        bool $status
    ) {
        return $this->repository->changeStatus(
            $id,
            $status
        );
    }

    public function active()
    {
        return $this->repository->active();
    }

    /**
     * Clean submitted data.
     */
    protected function prepareData(
        array $data
    ): array {

        if (isset($data['name'])) {
            $data['name'] =
                trim($data['name']);
        }

        if (isset($data['code'])) {

            $data['code'] =
                strtoupper(
                    trim($data['code'])
                );

            if ($data['code'] === '') {
                $data['code'] = null;
            }
        }

        if (isset($data['email'])) {

            $data['email'] =
                strtolower(
                    trim($data['email'])
                );

            if ($data['email'] === '') {
                $data['email'] = null;
            }
        }

        if (isset($data['phone'])) {

            $data['phone'] =
                trim($data['phone']);

            if ($data['phone'] === '') {
                $data['phone'] = null;
            }
        }

        if (isset($data['address'])) {

            $data['address'] =
                trim($data['address']);

            if ($data['address'] === '') {
                $data['address'] = null;
            }
        }

        if (isset($data['website'])) {

            $data['website'] =
                trim($data['website']);

            if ($data['website'] === '') {
                $data['website'] = null;
            }
        }

        if (isset($data['description'])) {

            $data['description'] =
                trim($data['description']);

            if ($data['description'] === '') {
                $data['description'] = null;
            }
        }

        if (isset($data['status'])) {

            $status = filter_var(
                $data['status'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            $data['status'] =
                $status === null
                    ? true
                    : $status;
        }

        return $data;
    }

    /**
     * Business validation.
     */
    protected function validateData(
        array $data
    ): void {

        if (empty($data['name'])) {

            throw ValidationException::withMessages([
                'name' =>
                    'Publisher name is required.',
            ]);
        }

        if (
            mb_strlen(
                $data['name']
            ) > 255
        ) {

            throw ValidationException::withMessages([
                'name' =>
                    'Publisher name cannot exceed 255 characters.',
            ]);
        }

        if (
            !empty($data['email']) &&
            !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw ValidationException::withMessages([
                'email' =>
                    'Please enter a valid email address.',
            ]);
        }

        if (
            !empty($data['website']) &&
            !filter_var(
                $data['website'],
                FILTER_VALIDATE_URL
            )
        ) {

            throw ValidationException::withMessages([
                'website' =>
                    'Please enter a valid website URL.',
            ]);
        }
    }
}