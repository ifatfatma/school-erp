<?php

namespace App\Repositories\Hostel;

use App\Models\HostelBuilding;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HostelBuildingRepository
{
    /**
     * Get current school ID.
     */
    protected function getSchoolId(?int $schoolId = null): int
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        /*
        |--------------------------------------------------------------------------
        | Explicit school ID
        |--------------------------------------------------------------------------
        */
        if (!empty($schoolId)) {
            return (int) $schoolId;
        }

        /*
        |--------------------------------------------------------------------------
        | Logged-in user's school
        |--------------------------------------------------------------------------
        */
        if (!empty($user->school_id)) {
            return (int) $user->school_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin fallback
        |--------------------------------------------------------------------------
        */
        if (
            ($user->user_type ?? '') === 'super_admin' ||
            strtolower((string) ($user->user_type ?? '')) === 'super admin'
        ) {
            $school = \App\Models\School::query()
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if ($school) {
                return (int) $school->id;
            }
        }

        abort(
            422,
            'School could not be determined for the authenticated user.'
        );
    }


    /**
     * Get all buildings.
     *
     * IMPORTANT:
     * This method is kept because your existing
     * HostelBuildingService/Controller calls getAll().
     */
    public function getAll(
        array $filters = [],
        ?int $schoolId = null
    ): LengthAwarePaginator {

        return $this->paginate(
            $filters,
            $schoolId
        );
    }


    /**
     * Get paginated buildings.
     */
    public function paginate(
        array $filters = [],
        ?int $schoolId = null
    ): LengthAwarePaginator {

        $schoolId =
            $this->getSchoolId(
                $schoolId
            );


        /*
        |--------------------------------------------------------------------------
        | Per Page
        |--------------------------------------------------------------------------
        */

        $perPage = (int) (
            $filters['per_page'] ?? 20
        );

        $perPage = max(
            1,
            min(
                $perPage,
                100
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = HostelBuilding::query()
            ->where(
                'school_id',
                $schoolId
            );


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['search']) &&
            trim($filters['search']) !== ''
        ) {

            $search =
                trim(
                    $filters['search']
                );


            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'code',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'building_type',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'warden_name',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'warden_phone',
                        'like',
                        '%' . $search . '%'
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'status',
                $filters
            ) &&
            $filters['status'] !== '' &&
            $filters['status'] !== null
        ) {

            $status =
                $filters['status'];


            if (
                $status === '1' ||
                $status === 1 ||
                $status === true ||
                $status === 'true'
            ) {

                $status = 1;

            } elseif (
                $status === '0' ||
                $status === 0 ||
                $status === false ||
                $status === 'false'
            ) {

                $status = 0;

            } else {

                $status = null;

            }


            if ($status !== null) {

                $query->where(
                    'status',
                    $status
                );

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Latest buildings first
        |--------------------------------------------------------------------------
        */

        $query->orderByDesc('id');


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        return $query->paginate(
            $perPage
        );
    }


    /**
     * Find building.
     */
    public function find(
        int $id,
        ?int $schoolId = null
    ): HostelBuilding {

        $schoolId =
            $this->getSchoolId(
                $schoolId
            );


        return HostelBuilding::query()
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $id
            )
            ->firstOrFail();
    }


    /**
     * Create building.
     */
    public function create(
        array $data,
        ?int $schoolId = null
    ): HostelBuilding {

        $schoolId =
            $this->getSchoolId(
                $schoolId
            );


        /*
        |--------------------------------------------------------------------------
        | Always force school_id from authenticated context.
        |--------------------------------------------------------------------------
        */

        $data['school_id'] =
            $schoolId;


        return HostelBuilding::create(
            $data
        );
    }


    /**
     * Update building.
     */
    public function update(
        HostelBuilding $building,
        array $data
    ): HostelBuilding {

        /*
        |--------------------------------------------------------------------------
        | Never allow school_id to be changed.
        |--------------------------------------------------------------------------
        */

        unset(
            $data['school_id']
        );


        $building->update(
            $data
        );


        return $building->fresh();
    }


    /**
     * Delete building.
     */
    public function delete(
        HostelBuilding $building
    ): bool {

        return (bool) $building->delete();
    }


    /**
     * Update building status.
     */
    public function updateStatus(
        HostelBuilding $building,
        bool $status
    ): HostelBuilding {

        $building->update([
            'status' => $status
        ]);


        return $building->fresh();
    }
}