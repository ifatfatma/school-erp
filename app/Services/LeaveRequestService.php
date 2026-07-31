<?php

namespace App\Services;

use App\Repositories\Academic\LeaveRequestRepository;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeaveRequestService
{
    protected $repository;

    public function __construct(LeaveRequestRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Paginated List
     */
    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters);
    }

    /**
     * All Records
     */
    public function all()
    {
        return $this->repository->all();
    }

    /**
     * Find Record
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Store
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $data['school_id'] = Auth::user()->school_id;
            $data['created_by'] = Auth::id();

            // Calculate Leave Days
            $data['total_days'] = $this->calculateDays(
                $data['from_date'],
                $data['to_date']
            );

            // Upload Attachment
            if (
                isset($data['attachment']) &&
                $data['attachment'] instanceof UploadedFile
            ) {
                $data['attachment'] = $data['attachment']
                    ->store('teacher-leaves', 'public');
            }

            return $this->repository->create($data);
        });
    }

    /**
     * Update
     */
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $leaveRequest = $this->repository->find($id);

            $data['updated_by'] = Auth::id();

            // Recalculate Days
            if (
                isset($data['from_date']) ||
                isset($data['to_date'])
            ) {
                $from = $data['from_date'] ?? $leaveRequest->from_date;
                $to   = $data['to_date'] ?? $leaveRequest->to_date;

                $data['total_days'] = $this->calculateDays($from, $to);
            }

            // Replace Attachment
            if (
                isset($data['attachment']) &&
                $data['attachment'] instanceof UploadedFile
            ) {

                if (
                    $leaveRequest->attachment &&
                    Storage::disk('public')->exists($leaveRequest->attachment)
                ) {
                    Storage::disk('public')->delete($leaveRequest->attachment);
                }

                $data['attachment'] = $data['attachment']
                    ->store('teacher-leaves', 'public');
            }

            return $this->repository->update($id, $data);
        });
    }

    /**
     * Delete
     */
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {

            $leaveRequest = $this->repository->find($id);

            if (
                $leaveRequest->attachment &&
                Storage::disk('public')->exists($leaveRequest->attachment)
            ) {
                Storage::disk('public')->delete($leaveRequest->attachment);
            }

            return $this->repository->delete($id);
        });
    }

    /**
     * Approve Leave
     */
    public function approve($id, $remarks = null)
    {
        return DB::transaction(function () use ($id, $remarks) {

            return $this->repository->update($id, [
                'status'      => 1,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'remarks'     => $remarks,
            ]);
        });
    }

    /**
     * Reject Leave
     */
    public function reject($id, $remarks = null)
    {
        return DB::transaction(function () use ($id, $remarks) {

            return $this->repository->update($id, [
                'status'      => 2,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'remarks'     => $remarks,
            ]);
        });
    }

    /**
     * Calculate Total Leave Days
     */
    protected function calculateDays($fromDate, $toDate)
    {
        $from = Carbon::parse($fromDate);
        $to   = Carbon::parse($toDate);

        return $from->diffInDays($to) + 1;
    }
}