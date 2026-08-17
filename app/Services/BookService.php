<?php

namespace App\Services;

use App\Models\Author;
use App\Models\BookCategory;
use App\Models\Publisher;
use App\Repositories\BookRepository;
use Illuminate\Validation\ValidationException;

class BookService
{
    public function __construct(
        protected BookRepository $repository
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

    public function create(
        array $data
    ) {

        $authorIds =
            $data['author_ids'] ?? [];

        unset(
            $data['author_ids']
        );


        $data =
            $this->prepareData(
                $data
            );


        $this->validateData(
            $data,
            $authorIds
        );


        $this->validateRelations(
            $data,
            $authorIds
        );


        return $this->repository->create(
            $data,
            $authorIds
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

        $authorIds =
            $data['author_ids'] ?? [];

        unset(
            $data['author_ids']
        );


        $data =
            $this->prepareData(
                $data
            );


        $this->validateData(
            $data,
            $authorIds
        );


        $this->validateRelations(
            $data,
            $authorIds
        );


        return $this->repository->update(
            $id,
            $data,
            $authorIds
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id
    ) {
        return $this->repository->delete(
            $id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | Active Books
    |--------------------------------------------------------------------------
    */

    public function active()
    {
        return $this->repository->active();
    }


    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareData(
        array $data
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Title
        |--------------------------------------------------------------------------
        */

        if (isset($data['title'])) {

            $data['title'] =
                trim(
                    $data['title']
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Book Code
        |--------------------------------------------------------------------------
        */

        if (isset($data['book_code'])) {

            $data['book_code'] =
                strtoupper(
                    trim(
                        $data['book_code']
                    )
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ISBN
        |--------------------------------------------------------------------------
        */

        if (isset($data['isbn'])) {

            $data['isbn'] =
                trim(
                    $data['isbn']
                );

            if (
                $data['isbn'] === ''
            ) {
                $data['isbn'] = null;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Edition
        |--------------------------------------------------------------------------
        */

        if (isset($data['edition'])) {

            $data['edition'] =
                trim(
                    $data['edition']
                );

            if (
                $data['edition'] === ''
            ) {
                $data['edition'] = null;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Language
        |--------------------------------------------------------------------------
        */

        if (isset($data['language'])) {

            $data['language'] =
                trim(
                    $data['language']
                );

            if (
                $data['language'] === ''
            ) {
                $data['language'] = null;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        if (isset($data['description'])) {

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


        /*
        |--------------------------------------------------------------------------
        | Price
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['price']) &&
            $data['price'] === ''
        ) {
            $data['price'] = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Foreign Keys
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['publisher_id']) &&
            $data['publisher_id'] === ''
        ) {
            $data['publisher_id'] = null;
        }


        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function validateData(
        array $data,
        array $authorIds
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Title
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['title']
            )
        ) {

            throw ValidationException::withMessages([
                'title' =>
                    'Book title is required.',
            ]);
        }


        if (
            mb_strlen(
                $data['title']
            ) > 255
        ) {

            throw ValidationException::withMessages([
                'title' =>
                    'Book title cannot exceed 255 characters.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Book Code
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['book_code']
            )
        ) {

            throw ValidationException::withMessages([
                'book_code' =>
                    'Book code is required.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['category_id']
            )
        ) {

            throw ValidationException::withMessages([
                'category_id' =>
                    'Book category is required.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Authors
        |--------------------------------------------------------------------------
        */

        if (
            empty($authorIds)
        ) {

            throw ValidationException::withMessages([
                'author_ids' =>
                    'Please select at least one author.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Publication Year
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['publication_year']
            )
        ) {

            $currentYear =
                (int) date('Y');


            if (
                $data['publication_year'] < 1000 ||
                $data['publication_year'] > $currentYear + 1
            ) {

                throw ValidationException::withMessages([
                    'publication_year' =>
                        'Please enter a valid publication year.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Pages
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['pages']) &&
            $data['pages'] !== null &&
            $data['pages'] < 0
        ) {

            throw ValidationException::withMessages([
                'pages' =>
                    'Pages cannot be negative.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Price
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['price']) &&
            $data['price'] < 0
        ) {

            throw ValidationException::withMessages([
                'price' =>
                    'Book price cannot be negative.',
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Relations
    |--------------------------------------------------------------------------
    */

    protected function validateRelations(
        array $data,
        array $authorIds
    ): void {

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        $categoryExists =
            BookCategory::query()
                ->where(
                    'id',
                    $data['category_id']
                )
                ->where(
                    'school_id',
                    $schoolId
                )
                ->exists();


        if (!$categoryExists) {

            throw ValidationException::withMessages([
                'category_id' =>
                    'Selected book category is invalid.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Publisher
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['publisher_id']
            )
        ) {

            $publisherExists =
                Publisher::query()
                    ->where(
                        'id',
                        $data['publisher_id']
                    )
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->exists();


            if (!$publisherExists) {

                throw ValidationException::withMessages([
                    'publisher_id' =>
                        'Selected publisher is invalid.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Authors
        |--------------------------------------------------------------------------
        */

        $authorIds =
            array_values(
                array_unique(
                    array_map(
                        'intval',
                        $authorIds
                    )
                )
            );


        $authorCount =
            Author::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->whereIn(
                    'id',
                    $authorIds
                )
                ->count();


        if (
            $authorCount !==
            count($authorIds)
        ) {

            throw ValidationException::withMessages([
                'author_ids' =>
                    'One or more selected authors are invalid.',
            ]);
        }
    }
}