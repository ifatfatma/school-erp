<?php

namespace App\Services;

use App\Repositories\AuthorRepository;
use Illuminate\Validation\ValidationException;

class AuthorService
{
    public function __construct(
        protected AuthorRepository $repository
    ) {
    }

    /**
     * List authors.
     */
    public function list(array $filters = [])
    {
        return $this->repository->list(
            $filters
        );
    }

    /**
     * Find author.
     */
    public function find(int $id)
    {
        return $this->repository->find(
            $id
        );
    }

    /**
     * Create author.
     */
    public function create(array $data)
    {
        $data = $this->prepareData(
            $data
        );

        $this->validateData(
            $data
        );

        return $this->repository->create(
            $data
        );
    }

    /**
     * Update author.
     */
    public function update(
        int $id,
        array $data
    ) {
        $this->repository->find($id);

        $data = $this->prepareData(
            $data
        );

        $this->validateData(
            $data
        );

        return $this->repository->update(
            $id,
            $data
        );
    }

    /**
     * Delete author.
     */
    public function delete(int $id)
    {
        return $this->repository->delete(
            $id
        );
    }

    /**
     * Change author status.
     */
    public function changeStatus(
        int $id,
        bool $status
    ) {
        return $this->repository->changeStatus(
            $id,
            $status
        );
    }

    /**
     * Get active authors.
     */
    public function active()
    {
        return $this->repository->active();
    }

    /**
     * Prepare author data.
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
                trim($data['code']);

            if (
                $data['code'] === ''
            ) {
                $data['code'] = null;
            }
        }

        if (isset($data['email'])) {

            $data['email'] =
                trim(
                    strtolower(
                        $data['email']
                    )
                );

            if (
                $data['email'] === ''
            ) {
                $data['email'] = null;
            }
        }

        if (isset($data['phone'])) {

            $data['phone'] =
                trim(
                    $data['phone']
                );

            if (
                $data['phone'] === ''
            ) {
                $data['phone'] = null;
            }
        }

        if (isset($data['biography'])) {

            $data['biography'] =
                trim(
                    $data['biography']
                );

            if (
                $data['biography'] === ''
            ) {
                $data['biography'] = null;
            }
        }

        if (
            isset($data['status'])
        ) {

            $data['status'] =
                filter_var(
                    $data['status'],
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );

            if (
                $data['status'] === null
            ) {
                $data['status'] = true;
            }
        }

        return $data;
    }

    /**
     * Business validation.
     */
    protected function validateData(
        array $data
    ): void {

        if (
            empty($data['name'])
        ) {

            throw ValidationException::withMessages([
                'name' =>
                    'Author name is required.',
            ]);
        }

        if (
            mb_strlen(
                $data['name']
            ) > 255
        ) {

            throw ValidationException::withMessages([
                'name' =>
                    'Author name cannot exceed 255 characters.',
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
            !empty($data['phone'])
        ) {

            $phone =
                preg_replace(
                    '/[\s\-\(\)\+]/',
                    '',
                    $data['phone']
                );

            if (
                !preg_match(
                    '/^[0-9]{7,15}$/',
                    $phone
                )
            ) {

                throw ValidationException::withMessages([
                    'phone' =>
                        'Please enter a valid phone number.',
                ]);
            }
        }
    }
}