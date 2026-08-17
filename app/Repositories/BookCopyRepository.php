<?php

namespace App\Repositories;

use App\Models\BookCopy;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookCopyRepository
{
    /**
     * Base query restricted to current school.
     */
    protected function query()
    {
        return BookCopy::query()
            ->where(
                'book_copies.school_id',
                auth()->user()->school_id
            );
    }


    /**
     * List book copies.
     */
    public function list(array $filters = [])
    {
        $query = $this->query()
            ->with([
                'book:id,title,book_code,isbn',
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
                    'book_copies.accession_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'book_copies.barcode',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'book_copies.copy_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'book_copies.shelf_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'book_copies.rack_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'book',
                    function ($bookQuery) use ($search) {

                        $bookQuery->where(
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
                        );

                    }
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Book
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['book_id'])) {

            $query->where(
                'book_copies.book_id',
                $filters['book_id']
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
                'book_copies.status',
                $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Condition
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['condition']) &&
            $filters['condition'] !== ''
        ) {

            $query->where(
                'book_copies.condition',
                $filters['condition']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Shelf
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['shelf_no'])) {

            $query->where(
                'book_copies.shelf_no',
                $filters['shelf_no']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'book_copies.id',
            'desc'
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
     * Find copy.
     */
    public function find(
        int $id
    ): BookCopy {

        return $this->query()
            ->with([
                'book',
            ])
            ->findOrFail($id);
    }


    /**
     * Create copy.
     */
    public function create(
        array $data
    ): BookCopy {

        $schoolId =
            auth()->user()->school_id;

        $userId =
            auth()->id();


        /*
        |--------------------------------------------------------------------------
        | Validate Accession Number
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['accession_no']
            )
        ) {

            $exists =
                BookCopy::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'accession_no',
                        $data['accession_no']
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'accession_no' =>
                        'Accession number already exists.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Barcode
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['barcode']
            )
        ) {

            $exists =
                BookCopy::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'barcode',
                        $data['barcode']
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'barcode' =>
                        'Barcode already exists.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $data['school_id'] =
            $schoolId;

        $data['created_by'] =
            $userId;

        $data['updated_by'] =
            $userId;

        $data['status'] =
            $data['status']
            ?? 'Available';

        $data['condition'] =
            $data['condition']
            ?? 'New';


        return BookCopy::create(
            $data
        )->load(
            'book'
        );
    }


    /**
     * Update copy.
     */
    public function update(
        int $id,
        array $data
    ): BookCopy {

        $copy =
            $this->find($id);

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Accession Number
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['accession_no']
            )
        ) {

            $exists =
                BookCopy::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'accession_no',
                        $data['accession_no']
                    )
                    ->where(
                        'id',
                        '!=',
                        $copy->id
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'accession_no' =>
                        'Accession number already exists.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Barcode
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['barcode']
            )
        ) {

            $exists =
                BookCopy::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'barcode',
                        $data['barcode']
                    )
                    ->where(
                        'id',
                        '!=',
                        $copy->id
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'barcode' =>
                        'Barcode already exists.',
                ]);
            }
        }


        $data['updated_by'] =
            auth()->id();


        $copy->update(
            $data
        );


        return $copy->fresh(
            'book'
        );
    }


    /**
     * Delete copy.
     */
    public function delete(
        int $id
    ): bool {

        $copy =
            $this->find($id);


        /*
        |--------------------------------------------------------------------------
        | Do not delete issued copy.
        |--------------------------------------------------------------------------
        */

        if (
            $copy->status === 'Issued'
        ) {

            throw ValidationException::withMessages([
                'copy' =>
                    'An issued book copy cannot be deleted.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Do not delete lost copy.
        |--------------------------------------------------------------------------
        */

        if (
            $copy->status === 'Lost'
        ) {

            throw ValidationException::withMessages([
                'copy' =>
                    'A lost book copy cannot be deleted.',
            ]);
        }


        return (bool) $copy->delete();
    }


    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        string $status
    ): BookCopy {

        $copy =
            $this->find($id);


        /*
        |--------------------------------------------------------------------------
        | Issued copy protection
        |--------------------------------------------------------------------------
        */

        if (
            $copy->status === 'Issued' &&
            $status !== 'Issued'
        ) {

            throw ValidationException::withMessages([
                'status' =>
                    'An issued copy cannot be manually changed to another status. Process the return first.',
            ]);
        }


        $copy->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);


        return $copy->fresh(
            'book'
        );
    }


    /**
     * Change condition.
     */
    public function changeCondition(
        int $id,
        string $condition
    ): BookCopy {

        $copy =
            $this->find($id);


        $copy->update([

            'condition' =>
                $condition,

            'updated_by' =>
                auth()->id(),

        ]);


        return $copy->fresh(
            'book'
        );
    }


    /**
     * Get available copies.
     */
    public function available(
        ?int $bookId = null
    ) {

        $query =
            $this->query()
                ->where(
                    'book_copies.status',
                    'Available'
                )
                ->with([
                    'book:id,title,book_code,isbn',
                ])
                ->orderBy(
                    'book_copies.accession_no'
                );


        if ($bookId) {

            $query->where(
                'book_copies.book_id',
                $bookId
            );
        }


        return $query->get([
            'id',
            'book_id',
            'accession_no',
            'barcode',
            'copy_no',
            'shelf_no',
            'rack_no',
            'condition',
            'status',
        ]);
    }


    /**
     * Find copy by barcode.
     */
    public function findByBarcode(
        string $barcode
    ): BookCopy {

        return $this->query()
            ->with([
                'book',
            ])
            ->where(
                'book_copies.barcode',
                $barcode
            )
            ->firstOrFail();
    }


    /**
     * Find copy by accession number.
     */
    public function findByAccession(
        string $accessionNo
    ): BookCopy {

        return $this->query()
            ->with([
                'book',
            ])
            ->where(
                'book_copies.accession_no',
                $accessionNo
            )
            ->firstOrFail();
    }


    /**
     * Statistics.
     */
    public function statistics(): array
    {
        $schoolId =
            auth()->user()->school_id;


        $query =
            BookCopy::query()
                ->where(
                    'school_id',
                    $schoolId
                );


        return [

            'total' =>
                (clone $query)->count(),

            'available' =>
                (clone $query)
                    ->where(
                        'status',
                        'Available'
                    )
                    ->count(),

            'issued' =>
                (clone $query)
                    ->where(
                        'status',
                        'Issued'
                    )
                    ->count(),

            'reserved' =>
                (clone $query)
                    ->where(
                        'status',
                        'Reserved'
                    )
                    ->count(),

            'lost' =>
                (clone $query)
                    ->where(
                        'status',
                        'Lost'
                    )
                    ->count(),

            'damaged' =>
                (clone $query)
                    ->where(
                        'status',
                        'Damaged'
                    )
                    ->count(),

            'maintenance' =>
                (clone $query)
                    ->where(
                        'status',
                        'Maintenance'
                    )
                    ->count(),

        ];
    }
}