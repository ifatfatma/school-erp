<?php

namespace App\Repositories;

use App\Models\Discount;

class DiscountRepository
{
    public function list(array $filters = [])
    {
        return Discount::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->filter($filters)

            ->latest('id')

            ->paginate(
                (int) (
                    $filters['per_page']
                    ?? 20
                )
            );
    }


    public function find(
        int $id
    ): Discount {

        return Discount::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->findOrFail($id);
    }


    public function create(
        array $data
    ): Discount {

        $data['school_id'] =
            auth()->user()->school_id;

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