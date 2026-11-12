<?php

namespace App\Services;

use App\Models\Guardian;
use App\Repositories\GuardianRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class GuardianService
{
    protected GuardianRepository $repository;

    public function __construct(GuardianRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Guardian List
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    /**
     * Find Guardian
     */
    public function find(int $id): Guardian
    {
        return $this->repository->find($id);
    }

    /**
     * Create Guardian
     */
    public function store(array $data): Guardian
    {
        $targetSchoolId = !empty($data['school_id']) 
            ? (int) $data['school_id'] 
            : (app(\App\Services\TenantManager::class)->getTenantId() ?: (auth()->user()?->school_id ?: 1));
        $data['school_id'] = $targetSchoolId;

        if (!empty($data['photo']) && ($data['photo'] instanceof \Illuminate\Http\UploadedFile)) {
            $data['photo'] = $data['photo']->store(
                'guardians',
                'public'
            );
        } else {
            unset($data['photo']);
        }
        return $this->repository->create($data);
    }

    /**
     * Update Guardian
     */
    public function update(
        Guardian $guardian,
        array $data
    ): Guardian {
        $data['school_id'] = $guardian->school_id ?: (!empty($data['school_id']) ? (int) $data['school_id'] : (auth()->user()?->school_id ?: 1));

        if (!empty($data['photo']) && ($data['photo'] instanceof \Illuminate\Http\UploadedFile)) {

            if (
                $guardian->photo &&
                Storage::disk('public')->exists($guardian->photo)
            ) {
                Storage::disk('public')->delete($guardian->photo);
            }

            $data['photo'] = $data['photo']->store(
                'guardians',
                'public'
            );
        } else {
            unset($data['photo']);
        }

        return $this->repository->update(
            $guardian,
            $data
        );
    }

    /**
     * Delete Guardian
     */
    public function delete(Guardian $guardian): bool
    {
        return $this->repository->delete($guardian);
    }

    /**
     * Attach Student
     */
    public function attachStudent(
        Guardian $guardian,
        int $studentId,
        bool $primary = false
    ): void {

        $this->repository->attachStudent(
            $guardian,
            $studentId,
            $primary
        );
    }

    /**
     * Detach Student
     */
    public function detachStudent(
        Guardian $guardian,
        int $studentId
    ): void {

        $this->repository->detachStudent(
            $guardian,
            $studentId
        );
    }

    /**
     * Update Primary Student
     */
    public function updatePrimaryStudent(
        Guardian $guardian,
        int $studentId
    ): void {

        $this->repository->updatePrimaryStudent(
            $guardian,
            $studentId
        );
    }
}
