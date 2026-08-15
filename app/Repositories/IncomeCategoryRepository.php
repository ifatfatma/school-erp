<?php

namespace App\Repositories;

use App\Models\IncomeCategory;

class IncomeCategoryRepository
{
    /**
     * Get all income categories.
     */
    public function list(array $filters = [])
    {
        $schoolId = auth()->user()->school_id;

        $query = IncomeCategory::query()
            ->where('school_id', $schoolId);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {

            $search = $filters['search'];

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['status']) &&
            $filters['status'] !== ''
        ) {

            $query->where(
                'status',
                (bool) $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Order
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'name'
        );


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        return $query->paginate(
            (int) (
                $filters['per_page']
                ?? 20
            )
        );
    }


    /**
     * Find income category.
     */
    public function find(int $id): IncomeCategory
    {
        return IncomeCategory::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->findOrFail($id);
    }


    /**
     * Create income category.
     */
    public function create(
        array $data
    ): IncomeCategory {

        $schoolId =
            auth()->user()->school_id;

        $userId =
            auth()->id();

        $data['school_id'] =
            $schoolId;

        $data['created_by'] =
            $userId;

        $data['updated_by'] =
            $userId;

        return IncomeCategory::create(
            $data
        );
    }


    /**
     * Update income category.
     */
    public function update(
        int $id,
        array $data
    ): IncomeCategory {

        $category =
            $this->find($id);

        $data['updated_by'] =
            auth()->id();

        $category->update(
            $data
        );

        return $category->fresh();
    }


    /**
     * Delete income category.
     */
    public function delete(
        int $id
    ): bool {

        $category =
            $this->find($id);

        return (bool) $category->delete();
    }


    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        bool $status
    ): IncomeCategory {

        $category =
            $this->find($id);

        $category->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);

        return $category->fresh();
    }


    /**
     * Check duplicate name.
     */
    public function existsByName(
        string $name,
        ?int $ignoreId = null
    ): bool {

        $query = IncomeCategory::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'name',
                $name
            );

        if ($ignoreId) {

            $query->where(
                'id',
                '!=',
                $ignoreId
            );
        }

        return $query->exists();
    }


    /**
     * Check duplicate code.
     */
    public function existsByCode(
        string $code,
        ?int $ignoreId = null
    ): bool {

        $query = IncomeCategory::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'code',
                $code
            );

        if ($ignoreId) {

            $query->where(
                'id',
                '!=',
                $ignoreId
            );
        }

        return $query->exists();
    }


    /**
     * Get active categories.
     */
    public function active()
    {
        return IncomeCategory::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'status',
                true
            )
            ->orderBy('name')
            ->get();
    }
}