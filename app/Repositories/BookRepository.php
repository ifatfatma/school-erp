<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookRepository
{
    /**
     * Base query restricted to current school.
     */
    protected function query()
    {
        return Book::query()
            ->where(
                'books.school_id',
                auth()->user()->school_id
            );
    }


    /**
     * List books.
     */
    public function list(array $filters = [])
    {
        $query = $this->query()
            ->with([
                'category:id,name,code',
                'publisher:id,name,code',
                'authors:id,name,code',
            ]);


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
                    'books.title',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'books.book_code',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'books.isbn',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'authors',
                    function ($authorQuery) use ($search) {

                        $authorQuery->where(
                            'authors.name',
                            'like',
                            "%{$search}%"
                        );

                    }
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['category_id'])) {

            $query->where(
                'books.category_id',
                $filters['category_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Publisher
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['publisher_id'])) {

            $query->where(
                'books.publisher_id',
                $filters['publisher_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Author
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['author_id'])) {

            $query->whereHas(
                'authors',
                function ($authorQuery) use ($filters) {

                    $authorQuery->where(
                        'authors.id',
                        $filters['author_id']
                    );

                }
            );
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
                'books.status',
                (bool) $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'books.title'
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
     * Find book.
     */
    public function find(int $id): Book
    {
        return $this->query()
            ->with([
                'category',
                'publisher',
                'authors',
            ])
            ->findOrFail($id);
    }


    /**
     * Create book.
     */
    public function create(
        array $data,
        array $authorIds = []
    ): Book {

        $schoolId =
            auth()->user()->school_id;

        $userId =
            auth()->id();


        /*
        |--------------------------------------------------------------------------
        | Duplicate Book Code
        |--------------------------------------------------------------------------
        */

        if (!empty($data['book_code'])) {

            $exists = Book::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'book_code',
                    $data['book_code']
                )
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'book_code' =>
                        'Book code already exists.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate ISBN
        |--------------------------------------------------------------------------
        */

        if (!empty($data['isbn'])) {

            $exists = Book::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'isbn',
                    $data['isbn']
                )
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'isbn' =>
                        'ISBN already exists.',
                ]);
            }
        }


        DB::transaction(function () use (
            &$book,
            $data,
            $authorIds,
            $schoolId,
            $userId
        ) {

            $data['school_id'] =
                $schoolId;

            $data['created_by'] =
                $userId;

            $data['updated_by'] =
                $userId;

            $data['status'] =
                $data['status'] ?? true;


            $book = Book::create(
                $data
            );


            /*
            |--------------------------------------------------------------------------
            | Attach Authors
            |--------------------------------------------------------------------------
            */

            if (!empty($authorIds)) {

                $book->authors()->sync(
                    $authorIds
                );
            }

        });


        return $book->load([
            'category',
            'publisher',
            'authors',
        ]);
    }


    /**
     * Update book.
     */
    public function update(
        int $id,
        array $data,
        array $authorIds = []
    ): Book {

        $book =
            $this->find($id);

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Duplicate Book Code
        |--------------------------------------------------------------------------
        */

        if (!empty($data['book_code'])) {

            $exists = Book::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'book_code',
                    $data['book_code']
                )
                ->where(
                    'id',
                    '!=',
                    $book->id
                )
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'book_code' =>
                        'Book code already exists.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate ISBN
        |--------------------------------------------------------------------------
        */

        if (!empty($data['isbn'])) {

            $exists = Book::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'isbn',
                    $data['isbn']
                )
                ->where(
                    'id',
                    '!=',
                    $book->id
                )
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'isbn' =>
                        'ISBN already exists.',
                ]);
            }
        }


        DB::transaction(function () use (
            $book,
            $data,
            $authorIds
        ) {

            $data['updated_by'] =
                auth()->id();


            $book->update(
                $data
            );


            /*
            |--------------------------------------------------------------------------
            | Sync Authors
            |--------------------------------------------------------------------------
            */
            $authorIds = array_values(
                array_unique(
                    array_map('intval', $authorIds)
                )
            );
            $book->authors()->sync(
                $authorIds
            );

        });


        return $book->fresh([
            'category',
            'publisher',
            'authors',
        ]);
    }


    /**
     * Delete book.
     */
    public function delete(
        int $id
    ): bool {

        $book =
            $this->find($id);


        /*
        |--------------------------------------------------------------------------
        | Prevent deletion if copies exist.
        |--------------------------------------------------------------------------
        |
        | Book copies module will add the "copies"
        | relationship.
        |
        */

        if (
            method_exists(
                $book,
                'copies'
            ) &&
            $book->copies()->exists()
        ) {

            throw ValidationException::withMessages([
                'book' =>
                    'This book cannot be deleted because physical copies are associated with it.',
            ]);
        }


        return (bool) $book->delete();
    }


    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        bool $status
    ): Book {

        $book =
            $this->find($id);


        $book->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);


        return $book->fresh([
            'category',
            'publisher',
            'authors',
        ]);
    }


    /**
     * Active books.
     *
     * Useful for Book Copy form.
     */
    public function active()
    {
        return $this->query()
            ->where(
                'books.status',
                true
            )
            ->orderBy(
                'books.title'
            )
            ->get([
                'id',
                'title',
                'book_code',
                'isbn',
            ]);
    }
}