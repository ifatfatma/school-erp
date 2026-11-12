<?php

namespace App\Repositories;

use App\Models\Discount;
use App\Services\TenantManager;

class DiscountRepository
{
    protected function resolveSchoolId(?int $filterSchoolId = null): ?int
    {
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);

        if (!$tenantManager->isSuperAdmin($user)) {
            return $user?->school_id ? (int) $user->school_id : null;
        }

        if ($filterSchoolId !== null && $filterSchoolId > 0) {
            return (int) $filterSchoolId;
        }

        $tenantId = $tenantManager->getTenantId();
        if ($tenantId) {
            return (int) $tenantId;
        }

        return $user?->school_id ? (int) $user->school_id : null;
    }

    public function list(array $filters = [])
    {
        $filterSchoolId = !empty($filters['school_id']) && $filters['school_id'] !== 'all' ? (int) $filters['school_id'] : null;
        $schoolId = $this->resolveSchoolId($filterSchoolId);

        $query = Discount::query()->with('school:id,name,code');

        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        }

        return $query
            ->filter($filters)
            ->latest('id')
            ->paginate((int) ($filters['per_page'] ?? 20));
    }


    public function find(
        int $id
    ): Discount {
        $user = auth()->user();
        $tenantManager = app(TenantManager::class);
        $isSuperAdmin = $tenantManager->isSuperAdmin($user);

        $query = Discount::query()->with('school:id,name,code');

        if (!$isSuperAdmin && $user?->school_id) {
            $query->where('school_id', $user->school_id);
        }

        return $query->findOrFail($id);
    }


    public function create(
        array $data
    ): Discount {

        $data['school_id'] = $data['school_id'] ?? $this->resolveSchoolId() ?? \App\Models\School::first()?->id ?? 1;

        $data['created_by'] =
            auth()->id();

        $data['updated_by'] =
            auth()->id();

        return Discount::create(
            $data
        );
    }


    public function update(
        int $id,
        array $data
    ): Discount {

        $discount =
            $this->find($id);

        $data['updated_by'] =
            auth()->id();

        $discount->update(
            $data
        );

        return $discount->fresh();
    }


    public function delete(
        int $id
    ): bool {

        return $this->find($id)
            ->delete();
    }


    public function toggleStatus(
        int $id
    ): Discount {

        $discount =
            $this->find($id);

        $discount->update([

            'status' =>
                !$discount->status,

            'updated_by' =>
                auth()->id(),

        ]);

        return $discount->fresh();
    }
}