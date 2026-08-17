<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\BookCopyRepository;
use Illuminate\Validation\ValidationException;

class BookCopyService
{
    public function __construct(
        protected BookCopyRepository $repository
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function list(array $filters = [])
    {
        return $this->repository->list(
            $filters
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Find
    |--------------------------------------------------------------------------
    */

    public function find(int $id)
    {
        return $this->repository->find(
            $id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(array $data)
    {
        $data = $this->prepareData(
            $data
        );

        $this->validateData(
            $data
        );

        $this->validateBook(
            $data['book_id']
        );

        return $this->repository->create(
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        array $data
    ) {
        $copy = $this->repository->find(
            $id
        );

        $data = $this->prepareData(
            $data
        );

        $this->validateData(
            $data,
            true
        );

        $this->validateBook(
            $data['book_id']
        );

        /*
         * Do not allow changing the book
         * of an issued copy.
         */
        if (
            $copy->status === 'Issued' &&
            (int) $copy->book_id !==
                (int) $data['book_id']
        ) {
            throw ValidationException::withMessages([
                'book_id' =>
                    'The book cannot be changed while this copy is issued.',
            ]);
        }

        return $this->repository->update(
            $id,
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(int $id)
    {
        return $this->repository->delete(
            $id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Change Status
    |--------------------------------------------------------------------------
    */

    public function changeStatus(
        int $id,
        string $status
    ) {
        $this->validateStatus(
            $status
        );

        return $this->repository->changeStatus(
            $id,
            $status
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Change Condition
    |--------------------------------------------------------------------------
    */

    public function changeCondition(
        int $id,
        string $condition
    ) {
        $this->validateCondition(
            $condition
        );

        return $this->repository->changeCondition(
            $id,
            $condition
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Available Copies
    |--------------------------------------------------------------------------
    */

    public function available(
        ?int $bookId = null
    ) {
        if ($bookId) {
            $this->validateBook(
                $bookId
            );
        }

        return $this->repository->available(
            $bookId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Find By Barcode
    |--------------------------------------------------------------------------
    */

    public function findByBarcode(
        string $barcode
    ) {
        $barcode = trim($barcode);

        if ($barcode === '') {
            throw ValidationException::withMessages([
                'barcode' =>
                    'Barcode is required.',
            ]);
        }

        return $this->repository->findByBarcode(
            $barcode
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Find By Accession
    |--------------------------------------------------------------------------
    */

    public function findByAccession(
        string $accessionNo
    ) {
        $accessionNo =
            trim($accessionNo);

        if ($accessionNo === '') {
            throw ValidationException::withMessages([
                'accession_no' =>
                    'Accession number is required.',
            ]);
        }

        return $this->repository->findByAccession(
            $accessionNo
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    public function statistics()
    {
        return $this->repository->statistics();
    }


    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareData(
        array $data
    ): array {

        if (isset($data['accession_no'])) {

            $data['accession_no'] =
                strtoupper(
                    trim(
                        $data['accession_no']
                    )
                );
        }


        if (isset($data['barcode'])) {

            $data['barcode'] =
                trim(
                    $data['barcode']
                );

            if (
                $data['barcode'] === ''
            ) {
                $data['barcode'] = null;
            }
        }


        if (isset($data['copy_no'])) {

            $data['copy_no'] =
                trim(
                    $data['copy_no']
                );

            if (
                $data['copy_no'] === ''
            ) {
                $data['copy_no'] = null;
            }
        }


        if (isset($data['shelf_no'])) {

            $data['shelf_no'] =
                trim(
                    $data['shelf_no']
                );

            if (
                $data['shelf_no'] === ''
            ) {
                $data['shelf_no'] = null;
            }
        }


        if (isset($data['rack_no'])) {

            $data['rack_no'] =
                trim(
                    $data['rack_no']
                );

            if (
                $data['rack_no'] === ''
            ) {
                $data['rack_no'] = null;
            }
        }


        if (isset($data['purchase_price'])) {

            if (
                $data['purchase_price'] === ''
            ) {
                $data['purchase_price'] = 0;
            }
        }


        if (isset($data['notes'])) {

            $data['notes'] =
                trim(
                    $data['notes']
                );

            if (
                $data['notes'] === ''
            ) {
                $data['notes'] = null;
            }
        }


        if (
            isset($data['condition']) &&
            $data['condition'] === ''
        ) {
            $data['condition'] = 'New';
        }


        if (
            isset($data['status']) &&
            $data['status'] === ''
        ) {
            $data['status'] = 'Available';
        }


        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Data
    |--------------------------------------------------------------------------
    */

    protected function validateData(
        array $data,
        bool $updating = false
    ): void {

        if (
            empty(
                $data['book_id']
            )
        ) {
            throw ValidationException::withMessages([
                'book_id' =>
                    'Book is required.',
            ]);
        }


        if (
            empty(
                $data['accession_no']
            )
        ) {
            throw ValidationException::withMessages([
                'accession_no' =>
                    'Accession number is required.',
            ]);
        }


        if (
            mb_strlen(
                $data['accession_no']
            ) > 255
        ) {
            throw ValidationException::withMessages([
                'accession_no' =>
                    'Accession number cannot exceed 255 characters.',
            ]);
        }


        if (
            !empty($data['barcode']) &&
            mb_strlen(
                $data['barcode']
            ) > 255
        ) {
            throw ValidationException::withMessages([
                'barcode' =>
                    'Barcode cannot exceed 255 characters.',
            ]);
        }


        if (
            isset($data['purchase_price']) &&
            $data['purchase_price'] < 0
        ) {
            throw ValidationException::withMessages([
                'purchase_price' =>
                    'Purchase price cannot be negative.',
            ]);
        }


        if (
            !empty($data['purchase_date']) &&
            !strtotime(
                $data['purchase_date']
            )
        ) {
            throw ValidationException::withMessages([
                'purchase_date' =>
                    'Please enter a valid purchase date.',
            ]);
        }


        if (
            !empty($data['status'])
        ) {
            $this->validateStatus(
                $data['status']
            );
        }


        if (
            !empty($data['condition'])
        ) {
            $this->validateCondition(
                $data['condition']
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Book
    |--------------------------------------------------------------------------
    */

    protected function validateBook(
        int $bookId
    ): void {

        $schoolId =
            auth()->user()->school_id;


        $exists = Book::query()
            ->where(
                'id',
                $bookId
            )
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                true
            )
            ->exists();


        if (!$exists) {
            throw ValidationException::withMessages([
                'book_id' =>
                    'Selected book is invalid or inactive.',
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Status
    |--------------------------------------------------------------------------
    */

    protected function validateStatus(
        string $status
    ): void {

        $allowed = [

            'Available',

            'Issued',

            'Reserved',

            'Lost',

            'Damaged',

            'Maintenance',

        ];


        if (
            !in_array(
                $status,
                $allowed,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' =>
                    'Invalid book copy status.',
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Condition
    |--------------------------------------------------------------------------
    */

    protected function validateCondition(
        string $condition
    ): void {

        $allowed = [

            'New',

            'Good',

            'Fair',

            'Damaged',

            'Lost',

        ];


        if (
            !in_array(
                $condition,
                $allowed,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'condition' =>
                    'Invalid book copy condition.',
            ]);
        }
    }
}