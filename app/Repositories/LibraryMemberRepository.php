<?php

namespace App\Repositories;

use App\Models\LibraryMember;
use Illuminate\Validation\ValidationException;

class LibraryMemberRepository
{
    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    protected function query()
    {
        return LibraryMember::query()
            ->where(
                'library_members.school_id',
                auth()->user()->school_id
            );
    }


    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function list(
        array $filters = []
    ) {
        $query = $this->query()
            ->with([
                'student',
                'staff',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['search']
            )
        ) {

            $search =
                trim(
                    $filters['search']
                );


            $query->where(function ($q) use ($search) {

                $q->where(
                    'library_members.member_no',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'library_members.phone',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'library_members.email',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'student',
                    function ($studentQuery) use ($search) {

                        $studentQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );

                    }
                )

                ->orWhereHas(
                    'staff',
                    function ($staffQuery) use ($search) {

                        $staffQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );

                    }
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Member Type
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['member_type']
            )
        ) {

            $query->where(
                'library_members.member_type',
                $filters['member_type']
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
                'library_members.status',
                (bool) $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Expired
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['expired']) &&
            $filters['expired'] !== ''
        ) {

            if (
                (bool) $filters['expired']
            ) {

                $query->whereNotNull(
                    'library_members.expiry_date'
                )
                ->whereDate(
                    'library_members.expiry_date',
                    '<',
                    now()->toDateString()
                );

            } else {

                $query->where(function ($q) {

                    $q->whereNull(
                        'library_members.expiry_date'
                    )
                    ->orWhereDate(
                        'library_members.expiry_date',
                        '>=',
                        now()->toDateString()
                    );

                });

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'library_members.id',
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


    /*
    |--------------------------------------------------------------------------
    | Find
    |--------------------------------------------------------------------------
    */

    public function find(
        int $id
    ): LibraryMember {

        return $this->query()
            ->with([
                'student',
                'staff',
            ])
            ->findOrFail(
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
    ): LibraryMember {

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Duplicate Member Number
        |--------------------------------------------------------------------------
        */

        $exists =
            LibraryMember::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'member_no',
                    $data['member_no']
                )
                ->exists();


        if ($exists) {

            throw ValidationException::withMessages([
                'member_no' =>
                    'Member number already exists.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Student Duplicate
        |--------------------------------------------------------------------------
        */

        if (
            $data['member_type'] === 'Student' &&
            !empty($data['student_id'])
        ) {

            $exists =
                LibraryMember::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'member_type',
                        'Student'
                    )
                    ->where(
                        'student_id',
                        $data['student_id']
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'student_id' =>
                        'This student is already registered as a library member.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Staff Duplicate
        |--------------------------------------------------------------------------
        */

        if (
            $data['member_type'] === 'Staff' &&
            !empty($data['staff_id'])
        ) {

            $exists =
                LibraryMember::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'member_type',
                        'Staff'
                    )
                    ->where(
                        'staff_id',
                        $data['staff_id']
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'staff_id' =>
                        'This staff member is already registered in the library.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        $data['school_id'] =
            $schoolId;

        $data['created_by'] =
            auth()->id();

        $data['updated_by'] =
            auth()->id();


        return LibraryMember::create(
            $data
        )->load([
            'student',
            'staff',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        array $data
    ): LibraryMember {

        $member =
            $this->find(
                $id
            );

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Duplicate Member Number
        |--------------------------------------------------------------------------
        */

        $exists =
            LibraryMember::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'member_no',
                    $data['member_no']
                )
                ->where(
                    'id',
                    '!=',
                    $member->id
                )
                ->exists();


        if ($exists) {

            throw ValidationException::withMessages([
                'member_no' =>
                    'Member number already exists.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Student Duplicate
        |--------------------------------------------------------------------------
        */

        if (
            $data['member_type'] === 'Student' &&
            !empty($data['student_id'])
        ) {

            $exists =
                LibraryMember::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'member_type',
                        'Student'
                    )
                    ->where(
                        'student_id',
                        $data['student_id']
                    )
                    ->where(
                        'id',
                        '!=',
                        $member->id
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'student_id' =>
                        'This student is already registered as a library member.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Staff Duplicate
        |--------------------------------------------------------------------------
        */

        if (
            $data['member_type'] === 'Staff' &&
            !empty($data['staff_id'])
        ) {

            $exists =
                LibraryMember::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'member_type',
                        'Staff'
                    )
                    ->where(
                        'staff_id',
                        $data['staff_id']
                    )
                    ->where(
                        'id',
                        '!=',
                        $member->id
                    )
                    ->exists();


            if ($exists) {

                throw ValidationException::withMessages([
                    'staff_id' =>
                        'This staff member is already registered in the library.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        $data['updated_by'] =
            auth()->id();


        $member->update(
            $data
        );


        return $member->fresh([
            'student',
            'staff',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id
    ): bool {

        $member =
            $this->find(
                $id
            );


        /*
        |--------------------------------------------------------------------------
        | Future Issue Protection
        |--------------------------------------------------------------------------
        |
        | Once Book Issue is implemented, check for active issues here.
        |
        */

        if (
            method_exists(
                $member,
                'issues'
            )
        ) {

            $hasActiveIssue =
                $member->issues()
                    ->whereNull(
                        'returned_at'
                    )
                    ->exists();


            if ($hasActiveIssue) {

                throw ValidationException::withMessages([
                    'member' =>
                        'A member with an issued book cannot be deleted.',
                ]);
            }
        }


        return (bool) $member->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Change Status
    |--------------------------------------------------------------------------
    */

    public function changeStatus(
        int $id,
        bool $status
    ): LibraryMember {

        $member =
            $this->find(
                $id
            );


        $member->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);


        return $member->fresh([
            'student',
            'staff',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Active Members
    |--------------------------------------------------------------------------
    */

    public function active()
    {
        return $this->query()
            ->with([
                'student',
                'staff',
            ])
            ->where(
                'library_members.status',
                true
            )
            ->where(function ($q) {

                $q->whereNull(
                    'library_members.expiry_date'
                )
                ->orWhereDate(
                    'library_members.expiry_date',
                    '>=',
                    now()->toDateString()
                );

            })
            ->orderBy(
                'library_members.member_no'
            )
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    public function statistics(): array
    {
        $schoolId =
            auth()->user()->school_id;


        $query =
            LibraryMember::query()
                ->where(
                    'school_id',
                    $schoolId
                );


        return [

            'total' =>
                (clone $query)->count(),

            'active' =>
                (clone $query)
                    ->where(
                        'status',
                        true
                    )
                    ->count(),

            'inactive' =>
                (clone $query)
                    ->where(
                        'status',
                        false
                    )
                    ->count(),

            'students' =>
                (clone $query)
                    ->where(
                        'member_type',
                        'Student'
                    )
                    ->count(),

            'staff' =>
                (clone $query)
                    ->where(
                        'member_type',
                        'Staff'
                    )
                    ->count(),

            'expired' =>
                (clone $query)
                    ->whereNotNull(
                        'expiry_date'
                    )
                    ->whereDate(
                        'expiry_date',
                        '<',
                        now()->toDateString()
                    )
                    ->count(),

        ];
    }
}