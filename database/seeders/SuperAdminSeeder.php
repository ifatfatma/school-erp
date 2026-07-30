<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'school_id' => 1,
                'name' => 'Super Admin',
                'username' => 'admin',
                'password' => Hash::make('12345678'),
                'user_type' => 'super_admin',
                'status' => 1,
            ]
        );

        $user->assignRole('Super Admin');
    }
}
