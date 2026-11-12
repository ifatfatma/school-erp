<?php

namespace App\Exports;

use App\Models\Student;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Student::with(['school:id,name,code', 'class:id,name', 'section:id,name']);

        if (!empty($this->filters['school_id'])) {
            if ($this->filters['school_id'] === 'all' || $this->filters['school_id'] === '0') {
                $query->withoutGlobalScope(\App\Scopes\SchoolScope::class);
            } else {
                $query->withoutGlobalScope(\App\Scopes\SchoolScope::class)->where('school_id', $this->filters['school_id']);
            }
        }

        if (!empty($this->filters['class_id'])) {
            $query->where('class_id', $this->filters['class_id']);
        }

        if (!empty($this->filters['section_id'])) {
            $query->where('section_id', $this->filters['section_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['search'])) {
            $search = trim($this->filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('class_id')->orderBy('admission_no')->get();
    }

    public function headings(): array
    {
        return [
            'School',
            'School Code',
            'Admission No',
            'Roll No',
            'First Name',
            'Last Name',
            'Class',
            'Section',
            'Gender',
            'Date of Birth',
            'Admission Date',
            'Mobile',
            'Email',
            'Blood Group',
            'Father Name',
            'Father Mobile',
            'Mother Name',
            'Address',
            'Status',
        ];
    }

    public function map($student): array
    {
        return [
            $student->school ? $student->school->name : '',
            $student->school ? $student->school->code : '',
            $student->admission_no,
            $student->roll_no,
            $student->first_name,
            $student->last_name,
            $student->class ? $student->class->name : '',
            $student->section ? $student->section->name : '',
            $student->gender,
            $student->dob ? Carbon::parse($student->dob)->format('Y-m-d') : '',
            $student->admission_date ? Carbon::parse($student->admission_date)->format('Y-m-d') : '',
            $student->mobile,
            $student->email,
            $student->blood_group,
            $student->father_name,
            $student->father_mobile,
            $student->mother_name,
            $student->address,
            $student->status ? 'Active' : 'Inactive',
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
                    'startColor' => ['rgb' => '1E40AF'], // Primary Blue Header
                ],
            ],
        ];
    }
}
