<?php

namespace App\Services;

use App\Models\TeacherAttendance;
use App\Models\LeaveRequest;
use App\Repositories\Academic\TeacherAttendanceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherAttendanceService
{
    protected TeacherAttendanceRepository $repository;

    public function __construct(TeacherAttendanceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $exists = TeacherAttendance::where('teacher_id', $data['teacher_id'])
                ->whereDate('attendance_date', $data['attendance_date'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'attendance_date' => [
                        'Attendance already exists for this teacher.'
                    ]
                ]);
            }

            $leave = LeaveRequest::where('teacher_id', $data['teacher_id'])
                ->where('status', 'Approved')
                ->whereDate('from_date', '<=', $data['attendance_date'])
                ->whereDate('to_date', '>=', $data['attendance_date'])
                ->exists();

            if ($leave) {
                $data['status'] = 'Leave';
            }

            $data['school_id'] = Auth::user()->school_id;
            $data['created_by'] = Auth::id();

            return $this->repository->create($data);
        });
    }

    public function update(TeacherAttendance $attendance, array $data)
    {
        return DB::transaction(function () use ($attendance, $data) {

            $exists = TeacherAttendance::where('teacher_id', $data['teacher_id'])
                ->whereDate('attendance_date', $data['attendance_date'])
                ->where('id', '!=', $attendance->id)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'attendance_date' => [
                        'Attendance already exists for this teacher.'
                    ]
                ]);
            }

            $leave = LeaveRequest::where('teacher_id', $data['teacher_id'])
                ->where('status', 'Approved')
                ->whereDate('from_date', '<=', $data['attendance_date'])
                ->whereDate('to_date', '>=', $data['attendance_date'])
                ->exists();

            if ($leave) {
                $data['status'] = 'Leave';
            }

            $data['updated_by'] = Auth::id();

            return $this->repository->update($attendance, $data);
        });
    }

    public function delete(TeacherAttendance $attendance)
    {
        return DB::transaction(function () use ($attendance) {
            return $this->repository->delete($attendance);
        });
    }
}