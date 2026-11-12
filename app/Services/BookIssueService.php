<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LibraryMember;
use App\Repositories\BookIssueRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookIssueService
{
    public function __construct(
        protected BookIssueRepository $repository
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }


    /*
    |--------------------------------------------------------------------------
    | Find
    |--------------------------------------------------------------------------
    */

    public function find(int $id)
    {
        return $this->repository->find($id);
    }


    /*
    |--------------------------------------------------------------------------
    | Issue Book
    |--------------------------------------------------------------------------
    */

    public function issue(array $data)
    {
        $data = $this->prepareData($data);

        $this->validateIssueData($data);

        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Lock Member
            |--------------------------------------------------------------------------
            */

            $member = LibraryMember::query()
                ->where(
                    'id',
                    $data['library_member_id']
                )
                ->where(
                    'school_id',
                    (app(\App\Services\TenantManager::class)->getTenantId() ?? auth()->user()?->school_id)
                )
                ->lockForUpdate()
                ->first();


            if (!$member) {

                throw ValidationException::withMessages([
                    'library_member_id' =>
                        'Library member not found.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Member Status
            |--------------------------------------------------------------------------
            */

            if (!$member->status) {

                throw ValidationException::withMessages([
                    'library_member_id' =>
                        'This library member is inactive.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Membership Expiry
            |--------------------------------------------------------------------------
            */

            if (
                $member->expiry_date &&
                $member->expiry_date->isPast()
            ) {

                throw ValidationException::withMessages([
                    'library_member_id' =>
                        'Library membership has expired.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Current Active Issues
            |--------------------------------------------------------------------------
            */

            $activeIssues =
                $this->repository->activeForMember(
                    $member->id
                );


            if (
                $activeIssues >=
                $member->issue_limit
            ) {

                throw ValidationException::withMessages([
                    'library_member_id' =>
                        "Member has already reached the maximum issue limit of {$member->issue_limit} books.",
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Lock Book Copy
            |--------------------------------------------------------------------------
            */

            $copy = BookCopy::query()
                ->where(
                    'id',
                    $data['book_copy_id']
                )
                ->where(
                    'school_id',
                    (app(\App\Services\TenantManager::class)->getTenantId() ?? auth()->user()?->school_id)
                )
                ->lockForUpdate()
                ->first();


            if (!$copy) {

                throw ValidationException::withMessages([
                    'book_copy_id' =>
                        'Book copy not found.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Copy Status
            |--------------------------------------------------------------------------
            */

            if (
                $copy->status !== 'Available'
            ) {

                throw ValidationException::withMessages([
                    'book_copy_id' =>
                        "This book copy is currently {$copy->status}.",
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Book Match
            |--------------------------------------------------------------------------
            */

            if (
                (int) $copy->book_id !==
                (int) $data['book_id']
            ) {

                throw ValidationException::withMessages([
                    'book_copy_id' =>
                        'Selected book copy does not belong to the selected book.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Check Existing Active Issue
            |--------------------------------------------------------------------------
            */

            $existingIssue =
                $this->repository->activeForCopy(
                    $copy->id
                );


            if ($existingIssue) {

                throw ValidationException::withMessages([
                    'book_copy_id' =>
                        'This book copy is already issued.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Validate Book
            |--------------------------------------------------------------------------
            */

            $book = Book::query()
                ->where(
                    'id',
                    $data['book_id']
                )
                ->where(
                    'school_id',
                    (app(\App\Services\TenantManager::class)->getTenantId() ?? auth()->user()?->school_id)
                )
                ->first();


            if (!$book) {

                throw ValidationException::withMessages([
                    'book_id' =>
                        'Selected book was not found.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Create Issue
            |--------------------------------------------------------------------------
            */

            $issue =
                $this->repository->create([
                    'library_member_id' =>
                        $member->id,

                    'book_id' =>
                        $book->id,

                    'book_copy_id' =>
                        $copy->id,

                    'issue_no' =>
                        $data['issue_no'] ?? null,

                    'issue_date' =>
                        $data['issue_date'],

                    'due_date' =>
                        $data['due_date'],

                    'status' =>
                        'Issued',

                    'notes' =>
                        $data['notes'] ?? null,
                ]);


            /*
            |--------------------------------------------------------------------------
            | Change Copy Status
            |--------------------------------------------------------------------------
            */

            $copy->update([
                'status' =>
                    'Issued',

                'updated_by' =>
                    auth()->id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Return Complete Issue
            |--------------------------------------------------------------------------
            */

            return $issue->fresh([
                'libraryMember.student',
                'libraryMember.staff',
                'book',
                'bookCopy',
                'issuedBy',
            ]);
        });
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
        $issue =
            $this->repository->find($id);


        if (
            in_array(
                $issue->status,
                [
                    'Returned',
                    'Lost',
                    'Damaged',
                ],
                true
            )
        ) {

            throw ValidationException::withMessages([
                'issue' =>
                    'This issue cannot be modified because it is already closed.',
            ]);
        }


        $data =
            $this->prepareData($data);


        $this->validateIssueData(
            $data
        );


        /*
        |--------------------------------------------------------------------------
        | Do not change Book Copy after issuing
        |--------------------------------------------------------------------------
        */

        unset(
            $data['library_member_id'],
            $data['book_id'],
            $data['book_copy_id']
        );


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
        return DB::transaction(
            function () use ($id) {

                $issue =
                    $this->repository->find($id);


                if (
                    in_array(
                        $issue->status,
                        [
                            'Issued',
                            'Overdue',
                        ],
                        true
                    )
                ) {

                    throw ValidationException::withMessages([
                        'issue' =>
                            'Active book issue cannot be deleted.',
                    ]);
                }


                return $this->repository->delete(
                    $id
                );
            }
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
    | Overdue
    |--------------------------------------------------------------------------
    */

    public function overdue()
    {
        return $this->repository->overdue();
    }


    /*
    |--------------------------------------------------------------------------
    | Member Issues
    |--------------------------------------------------------------------------
    */

    public function memberIssues(
        int $memberId
    ) {
        return $this->repository->memberIssues(
            $memberId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareData(
        array $data
    ): array {

        if (
            isset($data['issue_no'])
        ) {

            $data['issue_no'] =
                strtoupper(
                    trim(
                        $data['issue_no']
                    )
                );

            if (
                $data['issue_no'] === ''
            ) {
                $data['issue_no'] = null;
            }
        }


        if (
            isset($data['notes'])
        ) {

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


        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Issue Data
    |--------------------------------------------------------------------------
    */

    protected function validateIssueData(
        array $data
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Member
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['library_member_id']
            )
        ) {

            throw ValidationException::withMessages([
                'library_member_id' =>
                    'Library member is required.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Book
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Copy
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['book_copy_id']
            )
        ) {

            throw ValidationException::withMessages([
                'book_copy_id' =>
                    'Book copy is required.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Issue Date
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['issue_date']
            )
        ) {

            throw ValidationException::withMessages([
                'issue_date' =>
                    'Issue date is required.',
            ]);
        }


        if (
            !strtotime(
                $data['issue_date']
            )
        ) {

            throw ValidationException::withMessages([
                'issue_date' =>
                    'Invalid issue date.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Due Date
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['due_date']
            )
        ) {

            throw ValidationException::withMessages([
                'due_date' =>
                    'Due date is required.',
            ]);
        }


        if (
            !strtotime(
                $data['due_date']
            )
        ) {

            throw ValidationException::withMessages([
                'due_date' =>
                    'Invalid due date.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Date Order
        |--------------------------------------------------------------------------
        */

        if (
            strtotime(
                $data['due_date']
            ) <
            strtotime(
                $data['issue_date']
            )
        ) {

            throw ValidationException::withMessages([
                'due_date' =>
                    'Due date cannot be before issue date.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Notes
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['notes']) &&
            mb_strlen(
                $data['notes']
            ) > 10000
        ) {

            throw ValidationException::withMessages([
                'notes' =>
                    'Notes cannot exceed 10000 characters.',
            ]);
        }
    }
}