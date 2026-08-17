<?php

namespace App\Services;

use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\Staff;
use App\Repositories\LibraryMemberRepository;
use Illuminate\Validation\ValidationException;

class LibraryMemberService
{
    public function __construct(
        protected LibraryMemberRepository $repository
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

        $this->validateMemberPerson(
            $data
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
        $data = $this->prepareData(
            $data
        );

        $this->validateData(
            $data
        );

        $this->validateMemberPerson(
            $data
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
        bool $status
    ) {
        return $this->repository->changeStatus(
            $id,
            $status
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Active Members
    |--------------------------------------------------------------------------
    */

    public function active()
    {
        return $this->repository->active();
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

        /*
        |--------------------------------------------------------------------------
        | Member Number
        |--------------------------------------------------------------------------
        */

        if (isset($data['member_no'])) {

            $data['member_no'] =
                strtoupper(
                    trim(
                        $data['member_no']
                    )
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Member Type
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['member_type'])
        ) {

            $data['member_type'] =
                ucfirst(
                    strtolower(
                        trim(
                            $data['member_type']
                        )
                    )
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Phone
        |--------------------------------------------------------------------------
        */

        if (isset($data['phone'])) {

            $data['phone'] =
                trim(
                    $data['phone']
                );

            if (
                $data['phone'] === ''
            ) {
                $data['phone'] = null;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        if (isset($data['email'])) {

            $data['email'] =
                strtolower(
                    trim(
                        $data['email']
                    )
                );

            if (
                $data['email'] === ''
            ) {
                $data['email'] = null;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Student / Staff
        |--------------------------------------------------------------------------
        */

        if (
            $data['member_type'] === 'Student'
        ) {

            $data['staff_id'] = null;

        } elseif (
            $data['member_type'] === 'Staff'
        ) {

            $data['student_id'] = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Dates
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['membership_date']) &&
            $data['membership_date'] === ''
        ) {
            $data['membership_date'] =
                null;
        }


        if (
            isset($data['expiry_date']) &&
            $data['expiry_date'] === ''
        ) {
            $data['expiry_date'] =
                null;
        }


        /*
        |--------------------------------------------------------------------------
        | Issue Limit
        |--------------------------------------------------------------------------
        */

        if (
            !isset($data['issue_limit']) ||
            $data['issue_limit'] === ''
        ) {
            $data['issue_limit'] = 3;
        }


        /*
        |--------------------------------------------------------------------------
        | Notes
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['status'])
        ) {

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


        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Data
    |--------------------------------------------------------------------------
    */

    protected function validateData(
        array $data
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Member Number
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['member_no']
            )
        ) {

            throw ValidationException::withMessages([
                'member_no' =>
                    'Member number is required.',
            ]);
        }


        if (
            mb_strlen(
                $data['member_no']
            ) > 255
        ) {

            throw ValidationException::withMessages([
                'member_no' =>
                    'Member number cannot exceed 255 characters.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Member Type
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['member_type']
            )
        ) {

            throw ValidationException::withMessages([
                'member_type' =>
                    'Member type is required.',
            ]);
        }


        if (
            !in_array(
                $data['member_type'],
                [
                    'Student',
                    'Staff',
                ],
                true
            )
        ) {

            throw ValidationException::withMessages([
                'member_type' =>
                    'Invalid member type.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Person
        |--------------------------------------------------------------------------
        */

        if (
            $data['member_type'] === 'Student' &&
            empty($data['student_id'])
        ) {

            throw ValidationException::withMessages([
                'student_id' =>
                    'Please select a student.',
            ]);
        }


        if (
            $data['member_type'] === 'Staff' &&
            empty($data['staff_id'])
        ) {

            throw ValidationException::withMessages([
                'staff_id' =>
                    'Please select a staff member.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Phone
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['phone']) &&
            mb_strlen(
                $data['phone']
            ) > 255
        ) {

            throw ValidationException::withMessages([
                'phone' =>
                    'Phone number cannot exceed 255 characters.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        if (
            !empty($data['email']) &&
            !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw ValidationException::withMessages([
                'email' =>
                    'Please enter a valid email address.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Membership Date
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $data['membership_date']
            )
        ) {

            throw ValidationException::withMessages([
                'membership_date' =>
                    'Membership date is required.',
            ]);
        }


        if (
            !strtotime(
                $data['membership_date']
            )
        ) {

            throw ValidationException::withMessages([
                'membership_date' =>
                    'Please enter a valid membership date.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Expiry Date
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $data['expiry_date']
            )
        ) {

            if (
                !strtotime(
                    $data['expiry_date']
                )
            ) {

                throw ValidationException::withMessages([
                    'expiry_date' =>
                        'Please enter a valid expiry date.',
                ]);
            }


            if (
                strtotime(
                    $data['expiry_date']
                ) <
                strtotime(
                    $data['membership_date']
                )
            ) {

                throw ValidationException::withMessages([
                    'expiry_date' =>
                        'Expiry date cannot be before membership date.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Issue Limit
        |--------------------------------------------------------------------------
        */

        if (
            !isset(
                $data['issue_limit']
            ) ||
            !is_numeric(
                $data['issue_limit']
            )
        ) {

            throw ValidationException::withMessages([
                'issue_limit' =>
                    'Issue limit is required.',
            ]);
        }


        if (
            (int) $data['issue_limit'] < 1
        ) {

            throw ValidationException::withMessages([
                'issue_limit' =>
                    'Issue limit must be at least 1.',
            ]);
        }


        if (
            (int) $data['issue_limit'] > 100
        ) {

            throw ValidationException::withMessages([
                'issue_limit' =>
                    'Issue limit cannot exceed 100.',
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Student / Staff
    |--------------------------------------------------------------------------
    */

    protected function validateMemberPerson(
        array $data
    ): void {

        $schoolId =
            auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        if (
            $data['member_type'] === 'Student'
        ) {

            $studentExists =
                Student::query()
                    ->where(
                        'id',
                        $data['student_id']
                    )
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->exists();


            if (!$studentExists) {

                throw ValidationException::withMessages([
                    'student_id' =>
                        'Selected student does not belong to this school.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */

        if (
            $data['member_type'] === 'Staff'
        ) {

            $staffExists =
                Staff::query()
                    ->where(
                        'id',
                        $data['staff_id']
                    )
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->exists();


            if (!$staffExists) {

                throw ValidationException::withMessages([
                    'staff_id' =>
                        'Selected staff member does not belong to this school.',
                ]);
            }
        }
    }
}