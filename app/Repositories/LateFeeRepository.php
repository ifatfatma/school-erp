<?php

namespace App\Repositories;

use App\Models\LateFee;

class LateFeeRepository
{
    public function list(array $filters = [])
    {
        return LateFee::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->filter($filters)
            ->latest('id')
            ->paginate(
                (int) ($filters['per_page'] ?? 20)
            );
    }

    public function find(int $id): LateFee
    {
        return LateFee::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
            ->findOrFail($id);
    }

    public function create(array $data): LateFee
    {
        $data['school_id'] = auth()->user()->school_id;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return LateFee::create($data);
    }

    public function update(
        int $id,
        array $data
    ): LateFee {

        $lateFee = $this->find($id);

        $data['updated_by'] = auth()->id();

        $lateFee->update($data);

        return $lateFee->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function toggleStatus(int $id): LateFee
    {
        $lateFee = $this->find($id);

        $lateFee->update([
            'status' => !$lateFee->status,
            'updated_by' => auth()->id(),
        ]);

        return $lateFee->fresh();
    }
}