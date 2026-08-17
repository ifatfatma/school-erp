<?php

namespace App\Repositories;

use App\Models\Publisher;
use Illuminate\Validation\ValidationException;

class PublisherRepository
{
    /**
     * Base query for current school.
     */
    protected function query()
    {
        return Publisher::query()
            ->where(
                'publishers.school_id',
                auth()->user()->school_id
            );
    }

    /**
     * List publishers.
     */
    public function list(array $filters = [])
    {
        $query = $this->query();

        if (!empty($filters['search'])) {

            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'publishers.name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'publishers.code',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'publishers.email',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'publishers.phone',
                    'like',
                    "%{$search}%"
                );

            });
        }

        if (
            isset($filters['status']) &&
            $filters['status'] !== ''
        ) {

            $query->where(
                'publishers.status',
                (bool) $filters['status']
            );
        }

        $query->orderBy(
            'publishers.name'
        );

        return $query->paginate(
            (int) (
                $filters['per_page']
                ?? 20
            )
        );
    }

    /**
     * Find publisher.
     */
    public function find(int $id): Publisher
    {
        return $this->query()
            ->findOrFail($id);
    }

    /**
     * Create publisher.
     */
    public function create(
        array $data
    ): Publisher {

        $schoolId =
            auth()->user()->school_id;

        $userId =
            auth()->id();

        if (!empty($data['code'])) {

            $exists = Publisher::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'code',
                    $data['code']
                )
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'code' =>
                        'Publisher code already exists.',
                ]);
            }
        }

        $data['school_id'] =
            $schoolId;

        $data['created_by'] =
            $userId;

        $data['updated_by'] =
            $userId;

        $data['status'] =
            $data['status'] ?? true;

        return Publisher::create(
            $data
        );
    }

    /**
     * Update publisher.
     */
    public function update(
        int $id,
        array $data
    ): Publisher {

        $publisher =
            $this->find($id);

        $schoolId =
            auth()->user()->school_id;

        if (!empty($data['code'])) {

            $exists = Publisher::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'code',
                    $data['code']
                )
                ->where(
                    'id',
                    '!=',
                    $publisher->id
                )
                ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'code' =>
                        'Publisher code already exists.',
                ]);
            }
        }

        $data['updated_by'] =
            auth()->id();

        $publisher->update(
            $data
        );

        return $publisher->fresh();
    }

    /**
     * Delete publisher.
     */
    public function delete(
        int $id
    ): bool {

        $publisher =
            $this->find($id);

        /*
         * Prevent deleting a publisher
         * when books are associated.
         */
        if (
            method_exists(
                $publisher,
                'books'
            ) &&
            $publisher->books()->exists()
        ) {

            throw ValidationException::withMessages([
                'publisher' =>
                    'This publisher cannot be deleted because books are associated with it.',
            ]);
        }

        return (bool) $publisher->delete();
    }

    /**
     * Change status.
     */
    public function changeStatus(
        int $id,
        bool $status
    ): Publisher {

        $publisher =
            $this->find($id);

        $publisher->update([

            'status' =>
                $status,

            'updated_by' =>
                auth()->id(),

        ]);

        return $publisher->fresh();
    }

    /**
     * Active publishers.
     *
     * Used in Book form.
     */
    public function active()
    {
        return $this->query()
            ->where(
                'publishers.status',
                true
            )
            ->orderBy(
                'publishers.name'
            )
            ->get([
                'id',
                'name',
                'code',
            ]);
    }
}