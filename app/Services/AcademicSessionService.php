<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Repositories\Academic\AcademicSessionRepository;

class AcademicSessionService
{
    public function __construct(
        protected AcademicSessionRepository $repository
    ) {}

    public function list($request)
    {
        return $this->repository->paginate($request);
    }

    public function store(array $data)
    {
        $data['is_current'] = filter_var($data['is_current'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $targetSchoolId = $data['school_id'] 
            ?? app(\App\Services\TenantManager::class)->getTenantId() 
            ?? auth()->user()?->school_id;

        if ($data['is_current'] && $targetSchoolId) {
            AcademicSession::where('school_id', $targetSchoolId)->update([
                'is_current' => false,
            ]);
        }

        return $this->repository->create($data);
    }

    public function update(AcademicSession $session, array $data)
    {
        $data['is_current'] = filter_var($data['is_current'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $targetSchoolId = $session->school_id;

        if ($data['is_current'] && $targetSchoolId) {
            AcademicSession::where('school_id', $targetSchoolId)
                ->where('id', '!=', $session->id)
                ->update([
                    'is_current' => false,
                ]);
        }

        return $this->repository->update($session, $data);
    }

    public function delete(AcademicSession $session)
    {
        return $this->repository->delete($session);
    }
}