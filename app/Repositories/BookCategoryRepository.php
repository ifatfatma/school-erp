<?php

namespace App\Repositories;

use App\Models\BookCategory;
use Illuminate\Validation\ValidationException;

class BookCategoryRepository
{
    /**
     * Base query restricted to current school.
     */
    protected function query()
    {
        return BookCategory::query()
            ->where(
                'book_categories.school_id',
                auth()->user()->school_id
            );
    }


    /**
     * List categories.
     */
    public function list(array $filters = [])
    {
        $query = $this->query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {

            $search = trim(
                $filters['search']
            );

            $query->where(function ($q) use ($search) {

                $q->where(
                    'book_categories.name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'book_categories.code',
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
                'book_categories.status',
                (bool) $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'book_categories.name'
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
     * Find category.
     */
    public function find(int $id): BookCategory
    {
        return $this->query()
            ->findOrFail($id);
    }


    /**
     * Create category.
     */
    public function create(
        array $data
    ): BookCategory {

        $schoolId =
            auth()->user()->school_id;

        $userId =
            auth()->id();


        /*
        |--------------------------------------------------------------------------
        | Duplicate code
        |--------------------------------------------------------------------------
        */

        if (!empty($data['code'])) {

            $exists = BookCategory::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'code',
                    $data['code']
                )
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'code' =>
                        'Book category code already exists.',
                ]);
            }
        }


        $data['school_id'] =
            $schoolId;

        $data['created_by'] =
            $userId;

        $data['updated_by'] =
            $userId;

        $data['status'] =
            $data['status'] ?? true;


        return BookCategory::create(
            $data
        );
    }


    /**
     * Update category.
     */
    public function update(
        int $id,
        array $data
    ): BookCategory {

        $category =
            $this->find($id);

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Duplicate code
        |--------------------------------------------------------------------------
        */

        if (!empty($data['code'])) {

            $exists = BookCategory::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'code',
                    $data['code']
                )
                ->where(
                    'id',
                    '!=',
                    $category->id
                )
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'code' =>
                        'Book category code already exists.',
                ]);
            }
        }


        $data['updated_by'] =
            auth()->id();


        $category->update(
            $data
        );


        return $category->fresh();
    }


    /**
     * Delete category.
     */
    public function delete(
        int $id
    ): bool {

        $category =
            $this->find($id);


        /*
        |--------------------------------------------------------------------------
        | Prevent deletion if books exist.
        |--------------------------------------------------------------------------
        */

        if (
            method_exists(
                $category,
                'books'
            ) &&
            $category->books()->exists()
        ) {

            throw ValidationException::withMessages([
                'category' =>
                    'This category cannot be deleted because books are associated with it.',
            ]);
        }


        return (bool) $category->delete();
    }


    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        bool $status
    ): BookCategory {

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
     * Active categories.
     *
     * Used in Book form dropdown.
     */
    public function active()
    {
        return $this->query()
            ->where(
                'book_categories.status',
                true
            )
            ->orderBy(
                'book_categories.name'
            )
            ->get([
                'id',
                'name',
                'code',
            ]);
    }
}