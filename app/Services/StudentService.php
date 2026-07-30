<?php

namespace App\Services;

use App\Repositories\StudentRepository;

class StudentService extends BaseService
{
    public function __construct(StudentRepository $repository)
    {
        parent::__construct($repository);
    }

    public function activeStudents()
    {
        return $this->repository->active();
    }
}