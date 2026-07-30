<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::updateOrCreate(
            ['code' => 'SCH001'],
            [
                'name' => 'Demo School',
                'email' => 'info@school.com',
                'phone' => '9999999999',
                'address' => 'Demo Address',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'country' => 'India',
                'postal_code' => '700001',
                'status' => 1,
            ]
        );
    }
}