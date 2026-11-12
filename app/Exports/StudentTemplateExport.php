<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            [
                'ADM-2026-001',
                '1',
                'Aarav',
                'Sharma',
                'Class 1',
                'Section A',
                'Male',
                '2018-05-15',
                '2026-04-01',
                '9876543210',
                'aarav.sharma@example.com',
                'B+',
                'Rajesh Sharma',
                '9876543211',
                'Pooja Sharma',
                '123 Park Street, Kolkata',
            ],
            [
                'ADM-2026-002',
                '2',
                'Ananya',
                'Verma',
                'Class 1',
                'Section A',
                'Female',
                '2018-08-22',
                '2026-04-01',
                '9876543220',
                'ananya.verma@example.com',
                'O+',
                'Sanjay Verma',
                '9876543221',
                'Sunita Verma',
                '45 Lake Gardens, Kolkata',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Admission No (Optional - Auto generated if blank)',
            'Roll No',
            'First Name *',
            'Last Name',
            'Class Name * (e.g. Class 1)',
            'Section Name (e.g. Section A or A)',
            'Gender * (Male/Female/Other)',
            'Date of Birth (YYYY-MM-DD)',
            'Admission Date (YYYY-MM-DD)',
            'Mobile',
            'Email',
            'Blood Group (e.g. A+, B+, O+)',
            'Father Name',
            'Father Mobile',
            'Mother Name',
            'Address',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '047857'], // Forest Emerald Header for Template
                ],
            ],
        ];
    }
}
