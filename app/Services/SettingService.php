<?php

namespace App\Services;

use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SettingService
{
    public function __construct(
        protected SettingRepository $repository
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Get Setting
    |--------------------------------------------------------------------------
    */

    public function get(
        string $group,
        string $key,
        mixed $default = null
    ): mixed {
        return $this->repository->get(
            $group,
            $key,
            $default
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Group
    |--------------------------------------------------------------------------
    */

    public function getGroup(
        string $group
    ): array {
        return $this->repository->getGroup(
            $group
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Group Models
    |--------------------------------------------------------------------------
    */

    public function getGroupModels(
        string $group
    ) {
        return $this->repository->getGroupModels(
            $group
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get All
    |--------------------------------------------------------------------------
    */

    public function all()
    {
        return $this->repository->all();
    }


    /*
    |--------------------------------------------------------------------------
    | Set Setting
    |--------------------------------------------------------------------------
    */

    public function set(
        string $group,
        string $key,
        mixed $value,
        string $type = 'string',
        ?string $description = null
    ) {
        $this->validateType($type);

        return $this->repository->set(
            $group,
            $key,
            $value,
            $type,
            $description
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Set Multiple
    |--------------------------------------------------------------------------
    */

    public function setMultiple(
        string $group,
        array $settings
    ): void {

        if (empty($group)) {
            throw ValidationException::withMessages([
                'group' =>
                    'Setting group is required.',
            ]);
        }


        DB::transaction(function () use (
            $group,
            $settings
        ) {

            foreach ($settings as $key => $data) {

                if (is_array($data)) {

                    $type =
                        $data['type']
                        ?? 'string';

                    $value =
                        $data['value']
                        ?? null;

                    $description =
                        $data['description']
                        ?? null;

                } else {

                    $type =
                        $this->detectType(
                            $data
                        );

                    $value =
                        $data;

                    $description =
                        null;
                }


                $this->validateType(
                    $type
                );


                $this->validateKey(
                    $key
                );


                $this->repository->set(
                    $group,
                    $key,
                    $value,
                    $type,
                    $description
                );
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Save Group
    |--------------------------------------------------------------------------
    */

    public function saveGroup(
        string $group,
        array $settings
    ): array {

        $this->setMultiple(
            $group,
            $settings
        );


        return $this->getGroup(
            $group
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Setting
    |--------------------------------------------------------------------------
    */

    public function delete(
        string $group,
        string $key
    ): bool {

        return $this->repository->delete(
            $group,
            $key
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Group
    |--------------------------------------------------------------------------
    */

    public function deleteGroup(
        string $group
    ): int {

        return $this->repository->deleteGroup(
            $group
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Default Settings
    |--------------------------------------------------------------------------
    */

    public function createDefaults(): void
    {
        $defaults = [

            /*
            |--------------------------------------------------------------------------
            | General
            |--------------------------------------------------------------------------
            */

            'general' => [

                'currency' => [
                    'value' => 'INR',
                    'type' => 'string',
                    'description' =>
                        'Default currency.',
                ],

                'currency_symbol' => [
                    'value' => '₹',
                    'type' => 'string',
                    'description' =>
                        'Currency symbol.',
                ],

                'date_format' => [
                    'value' => 'd-m-Y',
                    'type' => 'string',
                    'description' =>
                        'Default date format.',
                ],

                'time_format' => [
                    'value' => 'h:i A',
                    'type' => 'string',
                    'description' =>
                        'Default time format.',
                ],

            ],


            /*
            |--------------------------------------------------------------------------
            | Library
            |--------------------------------------------------------------------------
            */

            'library' => [

                'overdue_fine_per_day' => [
                    'value' => 5,
                    'type' => 'decimal',
                    'description' =>
                        'Fine charged per overdue day.',
                ],

                'max_fine_amount' => [
                    'value' => 500,
                    'type' => 'decimal',
                    'description' =>
                        'Maximum library fine amount.',
                ],

                'renewal_limit' => [
                    'value' => 2,
                    'type' => 'integer',
                    'description' =>
                        'Maximum number of renewals.',
                ],

                'issue_days' => [
                    'value' => 14,
                    'type' => 'integer',
                    'description' =>
                        'Default number of days for book issue.',
                ],

            ],


            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */

            'attendance' => [

                'late_after' => [
                    'value' => '09:15',
                    'type' => 'string',
                    'description' =>
                        'Time after which student is marked late.',
                ],

                'working_days' => [
                    'value' => 6,
                    'type' => 'integer',
                    'description' =>
                        'Number of working days per week.',
                ],

            ],


            /*
            |--------------------------------------------------------------------------
            | Fees
            |--------------------------------------------------------------------------
            */

            'fees' => [

                'late_fee_per_day' => [
                    'value' => 10,
                    'type' => 'decimal',
                    'description' =>
                        'Late fee per day.',
                ],

                'invoice_prefix' => [
                    'value' => 'INV',
                    'type' => 'string',
                    'description' =>
                        'Fee invoice prefix.',
                ],

            ],


            /*
            |--------------------------------------------------------------------------
            | Notifications
            |--------------------------------------------------------------------------
            */

            'notification' => [

                'email_enabled' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' =>
                        'Enable email notifications.',
                ],

                'sms_enabled' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' =>
                        'Enable SMS notifications.',
                ],

            ],

        ];


        foreach ($defaults as $group => $settings) {

            $this->setMultiple(
                $group,
                $settings
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Type
    |--------------------------------------------------------------------------
    */

    protected function validateType(
        string $type
    ): void {

        $allowed = [

            'string',
            'text',
            'integer',
            'decimal',
            'boolean',
            'json',

        ];


        if (
            !in_array(
                $type,
                $allowed,
                true
            )
        ) {

            throw ValidationException::withMessages([
                'type' =>
                    'Invalid setting type.',
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Key
    |--------------------------------------------------------------------------
    */

    protected function validateKey(
        string $key
    ): void {

        if (
            trim($key) === ''
        ) {

            throw ValidationException::withMessages([
                'key' =>
                    'Setting key cannot be empty.',
            ]);
        }


        if (
            strlen($key) > 255
        ) {

            throw ValidationException::withMessages([
                'key' =>
                    'Setting key cannot exceed 255 characters.',
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Detect Type
    |--------------------------------------------------------------------------
    */

    protected function detectType(
        mixed $value
    ): string {

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value)) {
            return 'integer';
        }

        if (is_float($value)) {
            return 'decimal';
        }

        if (is_array($value)) {
            return 'json';
        }

        return 'string';
    }
}