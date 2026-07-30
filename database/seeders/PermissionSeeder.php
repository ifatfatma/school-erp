<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',

            'school.view',
            'school.create',
            'school.update',
            'school.delete',

            'student.view',
            'student.create',
            'student.update',
            'student.delete',

            'teacher.view',
            'teacher.create',
            'teacher.update',
            'teacher.delete',

            'class.view',
            'class.create',
            'class.update',
            'class.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }
    }
}