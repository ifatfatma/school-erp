<?php

namespace App\Repositories\Hostel;

use App\Models\HostelBuilding;
use App\Models\HostelRoom;

class HostelRoomRepository
{
    protected function schoolId(): int
    {
        return (int) auth('sanctum')
            ->user()
            ->school_id;
    }


    public function getAll(array $filters = [])
    {
        $query = HostelRoom::query()
            ->with([
                'building:id,name,code'
            ])
            ->where(
                'school_id',
                $this->schoolId()
            );


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {

            $search = trim(
                $filters['search']
            );

            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'room_no',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'room_type',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'building',
                        function ($buildingQuery) use ($search) {

                            $buildingQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Building
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['hostel_building_id'])) {

            $query->where(
                'hostel_building_id',
                $filters['hostel_building_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['status']) &&
            $filters['status'] !== ''
        ) {

            $query->where(
                'status',
                (int) $filters['status']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = min(
            max(
                (int) ($filters['per_page'] ?? 20),
                1
            ),
            100
        );


        return $query
            ->orderBy('hostel_building_id')
            ->orderBy('floor_no')
            ->orderBy('room_no')
            ->paginate($perPage);
    }


    public function find(int $id): ?HostelRoom
    {
        return HostelRoom::query()
            ->with([
                'building:id,name,code'
            ])
            ->where(
                'school_id',
                $this->schoolId()
            )
            ->find($id);
    }


    public function getBuildings()
    {
        return HostelBuilding::query()
            ->where(
                'school_id',
                $this->schoolId()
            )
            ->where('status', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
                'total_floors',
            ]);
    }


    public function create(array $data): HostelRoom
    {
        $data['school_id'] =
            $this->schoolId();

        return HostelRoom::create($data);
    }


    public function update(
        HostelRoom $room,
        array $data
    ): HostelRoom {

        $room->update($data);

        return $room->fresh([
            'building:id,name,code'
        ]);
    }


    public function delete(
        HostelRoom $room
    ): bool {

        return (bool) $room->delete();
    }
}