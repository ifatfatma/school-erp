<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingRepository
{
    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    protected function query()
    {
        return Setting::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Single Setting
    |--------------------------------------------------------------------------
    */

    public function get(
        string $group,
        string $key,
        mixed $default = null
    ): mixed {

        $setting = $this->query()
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        if (!$setting) {
            return $default;
        }

        return $this->castValue(
            $setting->value,
            $setting->type
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Find Setting
    |--------------------------------------------------------------------------
    */

    public function find(
        string $group,
        string $key
    ): ?Setting {

        return $this->query()
            ->where('group', $group)
            ->where('key', $key)
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Set Single Setting
    |--------------------------------------------------------------------------
    */

    public function set(
        string $group,
        string $key,
        mixed $value,
        string $type = 'string',
        ?string $description = null
    ): Setting {

        $schoolId =
            auth()->user()->school_id;

        $storedValue =
            $this->prepareValue(
                $value,
                $type
            );

        $setting = Setting::query()
            ->where('school_id', $schoolId)
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        if ($setting) {

            $setting->update([

                'value' =>
                    $storedValue,

                'type' =>
                    $type,

                'description' =>
                    $description
                    ?? $setting->description,

                'updated_by' =>
                    auth()->id(),

            ]);

        } else {

            $setting = Setting::create([

                'school_id' =>
                    $schoolId,

                'group' =>
                    $group,

                'key' =>
                    $key,

                'value' =>
                    $storedValue,

                'type' =>
                    $type,

                'description' =>
                    $description,

                'created_by' =>
                    auth()->id(),

                'updated_by' =>
                    auth()->id(),

            ]);
        }

        return $setting;
    }


    /*
    |--------------------------------------------------------------------------
    | Set Multiple Settings
    |--------------------------------------------------------------------------
    */

    public function setMultiple(
        string $group,
        array $settings
    ): void {

        DB::transaction(function () use (
            $group,
            $settings
        ) {

            foreach ($settings as $key => $data) {

                if (is_array($data)) {

                    $value =
                        $data['value']
                        ?? null;

                    $type =
                        $data['type']
                        ?? 'string';

                    $description =
                        $data['description']
                        ?? null;

                } else {

                    $value =
                        $data;

                    $type =
                        $this->detectType(
                            $value
                        );

                    $description =
                        null;
                }

                $this->set(
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
    | Get Group
    |--------------------------------------------------------------------------
    */

    public function getGroup(
        string $group
    ): array {

        $settings = $this->query()
            ->where('group', $group)
            ->get();


        $result = [];


        foreach ($settings as $setting) {

            $result[
                $setting->key
            ] = $this->castValue(
                $setting->value,
                $setting->type
            );
        }


        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Group Models
    |--------------------------------------------------------------------------
    */

    public function getGroupModels(
        string $group
    ) {

        return $this->query()
            ->where('group', $group)
            ->orderBy('key')
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Get All Settings
    |--------------------------------------------------------------------------
    */

    public function all()
    {
        return $this->query()
            ->orderBy('group')
            ->orderBy('key')
            ->get();
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

        $setting = $this->find(
            $group,
            $key
        );

        if (!$setting) {
            return false;
        }

        return (bool) $setting->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Group
    |--------------------------------------------------------------------------
    */

    public function deleteGroup(
        string $group
    ): int {

        return $this->query()
            ->where('group', $group)
            ->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Cast Value
    |--------------------------------------------------------------------------
    */

    protected function castValue(
        mixed $value,
        string $type
    ): mixed {

        if ($value === null) {
            return null;
        }


        return match ($type) {

            'integer' =>
                (int) $value,

            'decimal' =>
                (float) $value,

            'boolean' =>
                filter_var(
                    $value,
                    FILTER_VALIDATE_BOOLEAN
                ),

            'json' =>
                is_array($value)
                    ? $value
                    : json_decode(
                        $value,
                        true
                    ),

            default =>
                $value,

        };
    }


    /*
    |--------------------------------------------------------------------------
    | Prepare Value
    |--------------------------------------------------------------------------
    */

    protected function prepareValue(
        mixed $value,
        string $type
    ): ?string {

        if ($value === null) {
            return null;
        }


        return match ($type) {

            'boolean' =>
                $value ? '1' : '0',

            'json' =>
                json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE
                ),

            default =>
                (string) $value,

        };
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