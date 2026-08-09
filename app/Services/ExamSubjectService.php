<?php

namespace App\Services;

use App\Models\ExamSubject;
use App\Repositories\ExamSubjectRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExamSubjectService
{
    protected ExamSubjectRepository $repository;

    public function __construct(ExamSubjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Paginated List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Find
     */
    public function find(int $id): ExamSubject
    {
        return $this->repository->find($id);
    }

    /**
     * Active List
     */
    public function active()
    {
        return $this->repository->active();
    }

    /**
     * Subjects By Exam & Class
     */
    public function byExamAndClass(
        int $examId,
        int $classId
    ) {
        return $this->repository->byExamAndClass(
            $examId,
            $classId
        );
    }

    /**
     * Store
     */
    public function store(array $data): ExamSubject
    {
        $this->validateData($data);

        $data['school_id'] = Auth::user()->school_id;
        $data['created_by'] = Auth::id();

        return $this->repository->create($data);
    }

    /**
     * Update
     */
    public function update(
        ExamSubject $examSubject,
        array $data
    ): ExamSubject {

        $this->validateData(
            $data,
            $examSubject->id
        );

        $data['updated_by'] = Auth::id();

        return $this->repository->update(
            $examSubject,
            $data
        );
    }

    /**
     * Delete
     */
    public function delete(
        ExamSubject $examSubject
    ): bool {

        return $this->repository->delete(
            $examSubject
        );
    }

    /**
     * Validation
     */
    protected function validateData(
        array $data,
        ?int $ignoreId = null
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Duplicate Subject
        |--------------------------------------------------------------------------
        */

        if (
            $this->repository->exists(
                $data['exam_id'],
                $data['class_id'],
                $data['subject_id'],
                $ignoreId
            )
        ) {

            throw ValidationException::withMessages([
                'subject_id' => [
                    'Subject already exists for this Exam and Class.'
                ]
            ]);

        }


        $componentTotal =
        $data['theory_marks'] +
        $data['practical_marks'] +
        $data['viva_marks'] +
        $data['project_marks'];

    if ($componentTotal > $data['full_marks']) {
        throw ValidationException::withMessages([
                'full_marks' => [
                    'Invalid full marks'
                ]
            ]);
    }

        /*
        |--------------------------------------------------------------------------
        | Pass Marks
        |--------------------------------------------------------------------------
        */

        if (
            $data['pass_marks'] >
            $data['full_marks']
        ) {

            throw ValidationException::withMessages([
                'pass_marks' => [
                    'Pass Marks cannot be greater than Full Marks.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Component Total
        |--------------------------------------------------------------------------
        */

        $total =
            (float) $data['theory_marks'] +
            (float) $data['practical_marks'] +
            (float) $data['viva_marks'] +
            (float) $data['project_marks'];

        if (
            round($total, 2) !=
            round((float)$data['full_marks'], 2)
        ) {

            throw ValidationException::withMessages([
                'full_marks' => [
                    'Theory + Practical + Viva + Project marks must equal Full Marks.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Negative Values
        |--------------------------------------------------------------------------
        */

        foreach ([
            'full_marks',
            'pass_marks',
            'theory_marks',
            'practical_marks',
            'viva_marks',
            'project_marks'
        ] as $field) {

            if (
                isset($data[$field]) &&
                $data[$field] < 0
            ) {

                throw ValidationException::withMessages([
                    $field => [
                        'Marks cannot be negative.'
                    ]
                ]);

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Display Order
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['display_order']) &&
            $data['display_order'] < 1
        ) {

            throw ValidationException::withMessages([
                'display_order' => [
                    'Display Order must be greater than zero.'
                ]
            ]);

        }
    }
}