<?php

namespace App\Repositories;

use App\Models\ExpenseCategory;

class ExpenseCategoryRepository
{
    public function list(
        array $filters = []
    ) {
        return ExpenseCategory::query()

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
    ): ExpenseCategory {

        return ExpenseCategory::query()

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->findOrFail($id);
    }


    public function create(
        array $data
    ): ExpenseCategory {

        $data['school_id'] =
            auth()->user()->school_id;

        $data['created_by'] =
            auth()->id();

        $data['updated_by'] =
            auth()->id();

        return ExpenseCategory::create(
            $data
        );
    }


    public function update(
        int $id,
        array $data
    ): ExpenseCategory {

        $category =
            $this->find($id);

        $data['updated_by'] =
            auth()->id();

        $category->update(
            $data
        );

        return $category->fresh();
    }


    public function delete(
        int $id
    ): bool {

        return $this
            ->find($id)
            ->delete();
    }


    public function toggleStatus(
        int $id
    ): ExpenseCategory {

        $category =
            $this->find($id);

        $category->update([

            'status' =>
                !$category->status,

            'updated_by' =>
                auth()->id(),

        ]);

        return $category->fresh();
    }
}