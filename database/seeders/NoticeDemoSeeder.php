<?php

namespace Database\Seeders;

use App\Models\Notice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NoticeDemoSeeder extends Seeder
{
    public function run(): void
    {
        $notices = [
            [
                'school_id' => 1,
                'title' => 'Admissions Open for Academic Session 2026-2027',
                'slug' => 'admissions-open-2026-27',
                'content' => 'Online registrations for Nursery to Class 11 are officially underway. Prospective parents can submit their applications online through the website admission portal.',
                'publish_date' => now()->toDateString(),
                'target_audience' => 'public',
                'is_pinned' => true,
                'status' => true,
            ],
            [
                'school_id' => 1,
                'title' => 'Annual Science & Tech Fair on 15th December',
                'slug' => 'annual-science-fair',
                'content' => 'Students from Class 6 to 12 will showcase working robotics models, solar inventions, and software demonstrations in the Main Auditorium.',
                'publish_date' => now()->toDateString(),
                'target_audience' => 'all',
                'is_pinned' => false,
                'status' => true,
            ],
            [
                'school_id' => 1,
                'title' => 'Winter Vacation Schedule & Office Working Hours',
                'slug' => 'winter-vacation-schedule',
                'content' => 'The school will remain closed for students from 24th December to 2nd January. The administrative office will function between 10:00 AM and 2:00 PM for admissions.',
                'publish_date' => now()->subDays(2)->toDateString(),
                'target_audience' => 'all',
                'is_pinned' => false,
                'status' => true,
            ],
        ];

        foreach ($notices as $n) {
            Notice::firstOrCreate(['slug' => $n['slug']], $n);
        }
    }
}
