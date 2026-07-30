<?php

namespace App\Repositories;

use App\Models\Student;

class StudentRepository extends BaseRepository
{
    public function __construct(Student $student)
    {
        parent::__construct($student);
    }

    public function active()
    {
        return $this->query()
            ->where('status', 1)
            ->get();
    }
}