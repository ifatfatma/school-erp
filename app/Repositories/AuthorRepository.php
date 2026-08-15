<?php

namespace App\Repositories;

use App\Models\Author;
use Illuminate\Validation\ValidationException;

class AuthorRepository
{
    /**
     * Get logged-in school's authors.
     */
    protected function query()
    {
        return Author::query()
            ->where(
                'authors.school_id',
                auth()->user()->school_id
            );
    }


    /**
     * List authors.
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
                    'authors.name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'authors.code',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'authors.email',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'authors.phone',
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
                'authors.status',
                (bool) $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'authors.name'
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
     * Find author.
     */
    public function find(
        int $id
    ): Author {

        return $this->query()
            ->findOrFail($id);
    }


    /**
     * Create author.
     */
    public function create(
        array $data
    ): Author {

        $schoolId =
            auth()->user()->school_id;

        $userId =
            auth()->id();


        /*
        |--------------------------------------------------------------------------
        | Duplicate code protection
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['code'])
        ) {

            $exists =
                Author::query()
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
                        'Author code already exists.',
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
            $data['status']
            ?? true;


        return Author::create(
            $data
        );
    }


    /**
     * Update author.
     */
    public function update(
        int $id,
        array $data
    ): Author {

        $author =
            $this->find($id);

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Duplicate code protection
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['code'])
        ) {

            $exists =
                Author::query()
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
                        $author->id
                    )
                    ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'code' =>
                        'Author code already exists.',
                ]);
            }
        }


        $data['updated_by'] =
            auth()->id();


        $author->update(
            $data
        );


        return $author->fresh();
    }


    /**
     * Delete author.
     */
    public function delete(
        int $id
    ): bool {

        $author =
            $this->find($id);


        /*
        |--------------------------------------------------------------------------
        | Prevent deletion if books exist
        |--------------------------------------------------------------------------
        |
        | This check becomes useful when the Book
        | module is created.
        |
        */

        if (
            method_exists(
                $author,
                'books'
            )
            &&
            $author->books()->exists()
        ) {

            throw ValidationException::withMessages([
                'author' =>
                    'This author cannot be deleted because books are associated with this author.',
            ]);
        }


        return (bool) $author->delete();
    }


    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        bool $status
    ): Author {

        $author =
            $this->find($id);


        $author->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);


        return $author->fresh();
    }


    /**
     * Get active authors.
     *
     * Useful for Book form dropdown.
     */
    public function active()
    {
        return $this->query()
            ->where(
                'authors.status',
                true
            )
            ->orderBy(
                'authors.name'
            )
            ->get([
                'id',
                'name',
                'code',
            ]);
    }
}