<?php

namespace App\Services\Hostel;

use App\Models\HostelRoom;
use App\Repositories\Hostel\HostelRoomRepository;
use Illuminate\Support\Facades\DB;

class HostelRoomService
{
    public function __construct(
        protected HostelRoomRepository $repository
    ) {
    }


    public function getAll(array $filters = [])
    {
        return $this->repository
            ->getAll($filters);
    }


    public function getBuildings()
    {
        return $this->repository
            ->getBuildings();
    }


    public function find(int $id): ?HostelRoom
    {
        return $this->repository
            ->find($id);
    }


    public function create(array $data): HostelRoom
    {
        return DB::transaction(
            function () use ($data) {

                return $this->repository
                    ->create($data);
            }
        );
    }


    public function update(
        HostelRoom $room,
        array $data
    ): HostelRoom {

        return DB::transaction(
            function () use ($room, $data) {

                return $this->repository
                    ->update(
                        $room,
                        $data
                    );
            }
        );
    }


    public function delete(
        HostelRoom $room
    ): bool {

        return DB::transaction(
            function () use ($room) {

                return $this->repository
                    ->delete($room);
            }
        );
    }
}