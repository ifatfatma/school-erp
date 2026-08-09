<?php

namespace App\Repositories;

use App\Models\Student;
use App\Models\StudentPromotion;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentPromotionRepository
{
    protected StudentPromotion $model;

    public function __construct(StudentPromotion $model)
    {
        $this->model = $model;
    }

    /**
     * Promotion List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->with([
                'student',
                'fromSession',
                'toSession',
                'fromClass',
                'toClass',
                'fromSection',
                'toSection',
            ])
            ->where('school_id', auth()->user()->school_id)
            ->filter($filters)
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find Promotion
     */
    public function find(int $id): StudentPromotion
    {
        return $this->model
            ->with([
                'student',
                'fromSession',
                'toSession',
                'fromClass',
                'toClass',
                'fromSection',
                'toSection',
            ])
            ->where('school_id', auth()->user()->school_id)
            ->findOrFail($id);
    }

    /**
     * Single Student Promotion
     */
    public function promote(array $data): StudentPromotion
    {
        return DB::transaction(function () use ($data) {

            $student = Student::where('school_id', auth()->user()->school_id)
                ->findOrFail($data['student_id']);

            $promotion = $this->model->create([

                'school_id'        => auth()->user()->school_id,

                'student_id'       => $student->id,

                'from_session_id'  => $student->academic_session_id,

                'to_session_id'    => $data['to_session_id'],

                'from_class_id'    => $student->class_id,

                'to_class_id'      => $data['to_class_id'],

                'from_section_id'  => $student->section_id,

                'to_section_id'    => $data['to_section_id'],

                'promotion_date'   => $data['promotion_date'],

                'promotion_type'   => $data['promotion_type'],

                'remarks'          => $data['remarks'] ?? null,

                'created_by'       => auth()->id(),

            ]);

            $student->update([

                'academic_session_id' => $data['to_session_id'],

                'class_id'            => $data['to_class_id'],

                'section_id'          => $data['to_section_id'],

            ]);

            return $promotion->fresh();
        });
    }

    /**
     * Bulk Promotion
     */
    public function bulkPromote(array $data): void
    {
        DB::transaction(function () use ($data) {

            $students = Student::whereIn(
                'id',
                $data['student_ids']
            )->get();

            foreach ($students as $student) {

                $this->promote([

                    'student_id'      => $student->id,

                    'to_session_id'   => $data['to_session_id'],

                    'to_class_id'     => $data['to_class_id'],

                    'to_section_id'   => $data['to_section_id'],

                    'promotion_date'  => $data['promotion_date'],

                    'promotion_type'  => $data['promotion_type'],

                    'remarks'         => $data['remarks'] ?? null,

                ]);

            }

        });
    }

    /**
     * Delete Promotion
     */
    public function delete(StudentPromotion $promotion): bool
    {
        return $promotion->delete();
    }
}