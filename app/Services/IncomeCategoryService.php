<?php

namespace App\Services;

use App\Repositories\IncomeCategoryRepository;
use Illuminate\Validation\ValidationException;

class IncomeCategoryService
{
    public function __construct(
        protected IncomeCategoryRepository $repository
    ) {
    }


    /**
     * List income categories.
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
    public function find(
        int $id
    ) {
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

        $name =
            trim($data['name']);

        $code =
            isset($data['code'])
                ? trim($data['code'])
                : null;


        /*
        |--------------------------------------------------------------------------
        | Duplicate Name
        |--------------------------------------------------------------------------
        */

        if (
            $this->repository->existsByName(
                $name
            )
        ) {

            throw ValidationException::withMessages([

                'name' =>
                    'This income category already exists.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Code
        |--------------------------------------------------------------------------
        */

        if (
            $code &&
            $this->repository->existsByCode(
                $code
            )
        ) {

            throw ValidationException::withMessages([

                'code' =>
                    'This income category code already exists.',

            ]);
        }


        $data['name'] =
            $name;

        $data['code'] =
            $code;


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

        /*
        |--------------------------------------------------------------------------
        | Make sure category belongs
        | to current school
        |--------------------------------------------------------------------------
        */

        $this->repository->find(
            $id
        );


        $name =
            trim($data['name']);

        $code =
            isset($data['code'])
                ? trim($data['code'])
                : null;


        /*
        |--------------------------------------------------------------------------
        | Duplicate Name
        |--------------------------------------------------------------------------
        */

        if (
            $this->repository->existsByName(
                $name,
                $id
            )
        ) {

            throw ValidationException::withMessages([

                'name' =>
                    'This income category already exists.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Code
        |--------------------------------------------------------------------------
        */

        if (
            $code &&
            $this->repository->existsByCode(
                $code,
                $id
            )
        ) {

            throw ValidationException::withMessages([

                'code' =>
                    'This income category code already exists.',

            ]);
        }


        $data['name'] =
            $name;

        $data['code'] =
            $code;


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

        /*
        |--------------------------------------------------------------------------
        | Verify ownership
        |--------------------------------------------------------------------------
        */

        $this->repository->find(
            $id
        );


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
     * Get active categories.
     */
    public function active()
    {
        return $this->repository->active();
    }
}