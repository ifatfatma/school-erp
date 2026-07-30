<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::findByName('Super Admin', 'sanctum');

        // Give all permissions to Super Admin
        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'sanctum')->get()
        );
    }
}