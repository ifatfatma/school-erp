<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PortalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $student = Student::find(1);
        if ($student) {
            $studentUser = User::firstOrCreate(
                ['email' => 'student@school.com'],
                [
                    'school_id' => 1,
                    'name' => $student->full_name,
                    'username' => 'a01',
                    'phone' => $student->mobile,
                    'user_type' => 'student',
                    'password' => Hash::make('12345678'),
                    'status' => 1,
                ]
            );
            $studentUser->assignRole('Student');
            $student->update(['user_id' => $studentUser->id]);

            $guardian = Guardian::firstOrCreate(
                ['email' => 'parent@school.com'],
                [
                    'school_id' => 1,
                    'first_name' => 'Nurullah',
                    'last_name' => 'Khan',
                    'mobile' => '9831203456',
                    'relation' => 'Father',
                    'status' => 1,
                ]
            );

            $parentUser = User::firstOrCreate(
                ['email' => 'parent@school.com'],
                [
                    'school_id' => 1,
                    'name' => 'Nurullah Khan (Parent)',
                    'username' => 'parent',
                    'phone' => '9831203456',
                    'user_type' => 'parent',
                    'password' => Hash::make('12345678'),
                    'status' => 1,
                ]
            );
            $parentUser->assignRole('Parent');
            $guardian->update(['user_id' => $parentUser->id]);

            if (!$student->guardians()->where('guardian_id', $guardian->id)->exists()) {
                $student->guardians()->attach($guardian->id, ['is_primary' => 1]);
            }
        }
    }
}
