<?php

namespace App\Services;

use App\Repositories\BookCategoryRepository;
use Illuminate\Validation\ValidationException;

class BookCategoryService
{
    public function __construct(
        protected BookCategoryRepository $repository
    ) {
    }


    /**
     * List categories.
     */
    public function list(
        array $filters = []
    ) {
        return $this->repository->list(
            $filters
        );
    }


    /**
     * Find category.
     */
    public function find(int $id)
    {
        return $this->repository->find(
            $id
        );
    }


    /**
     * Create category.
     */
    public function create(
        array $data
    ) {

        $data =
            $this->prepareData(
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
     * Update category.
     */
    public function update(
        int $id,
        array $data
    ) {

        $this->repository->find(
            $id
        );

        $data =
            $this->prepareData(
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
     * Delete category.
     */
    public function delete(
        int $id
    ) {

        return $this->repository->delete(
            $id
        );
    }


    /**
     * Change status.
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
     * Active categories.
     */
    public function active()
    {
        return $this->repository->active();
    }


    /**
     * Prepare data.
     */
    protected function prepareData(
        array $data
    ): array {

        if (isset($data['name'])) {

            $data['name'] =
                trim(
                    $data['name']
                );
        }


        if (isset($data['code'])) {

            $data['code'] =
                trim(
                    strtoupper(
                        $data['code']
                    )
                );

            if (
                $data['code'] === ''
            ) {
                $data['code'] = null;
            }
        }


        if (
            isset(
                $data['description']
            )
        ) {

            $data['description'] =
                trim(
                    $data['description']
                );

            if (
                $data['description'] === ''
            ) {
                $data['description'] = null;
            }
        }


        if (
            isset(
                $data['status']
            )
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
            empty(
                $data['name']
            )
        ) {

            throw ValidationException::withMessages([
                'name' =>
                    'Book category name is required.',
            ]);
        }


        if (
            mb_strlen(
                $data['name']
            ) > 255
        ) {

            throw ValidationException::withMessages([
                'name' =>
                    'Book category name cannot exceed 255 characters.',
            ]);
        }


        if (
            !empty(
                $data['code']
            ) &&
            mb_strlen(
                $data['code']
            ) > 255
        ) {

            throw ValidationException::withMessages([
                'code' =>
                    'Book category code cannot exceed 255 characters.',
            ]);
        }
    }
}