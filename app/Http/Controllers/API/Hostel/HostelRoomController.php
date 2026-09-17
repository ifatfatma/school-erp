<?php

namespace App\Http\Controllers\Api\Hostel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hostel\HostelRoomRequest;
use App\Http\Resources\Hostel\HostelRoomResource;
use App\Services\Hostel\HostelRoomService;
use Illuminate\Http\Request;

class HostelRoomController extends Controller
{
    public function __construct(
        protected HostelRoomService $service
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $rooms = $this->service->getAll([
            'search' =>
                $request->input('search'),

            'hostel_building_id' =>
                $request->input(
                    'hostel_building_id'
                ),

            'status' =>
                $request->input('status'),

            'per_page' =>
                $request->input(
                    'per_page',
                    20
                ),
        ]);


        return HostelRoomResource::collection(
            $rooms
        )->additional([
            'success' => true,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Building Dropdown
    |--------------------------------------------------------------------------
    */

    public function buildings()
    {
        return response()->json([
            'success' => true,
            'data' =>
                $this->service
                    ->getBuildings(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        HostelRoomRequest $request
    ) {

        $room = $this->service->create(
            $request->validated()
        );


        $room->load(
            'building:id,name,code'
        );


        return response()->json([
            'success' => true,

            'message' =>
                'Hostel room created successfully.',

            'data' =>
                new HostelRoomResource($room),
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(int $id)
    {
        $room =
            $this->service->find($id);


        if (!$room) {

            return response()->json([
                'success' => false,

                'message' =>
                    'Hostel room not found.',
            ], 404);
        }


        return response()->json([
            'success' => true,

            'data' =>
                new HostelRoomResource($room),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        HostelRoomRequest $request,
        int $id
    ) {

        $room =
            $this->service->find($id);


        if (!$room) {

            return response()->json([
                'success' => false,

                'message' =>
                    'Hostel room not found.',
            ], 404);
        }


        $room = $this->service->update(
            $room,
            $request->validated()
        );


        return response()->json([
            'success' => true,

            'message' =>
                'Hostel room updated successfully.',

            'data' =>
                new HostelRoomResource($room),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(int $id)
    {
        $room =
            $this->service->find($id);


        if (!$room) {

            return response()->json([
                'success' => false,

                'message' =>
                    'Hostel room not found.',
            ], 404);
        }


        $room = $this->service->update(
            $room,
            [
                'status' =>
                    !$room->status,
            ]
        );


        return response()->json([
            'success' => true,

            'message' =>
                'Room status updated successfully.',

            'data' =>
                new HostelRoomResource($room),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(int $id)
    {
        $room =
            $this->service->find($id);


        if (!$room) {

            return response()->json([
                'success' => false,

                'message' =>
                    'Hostel room not found.',
            ], 404);
        }


        $this->service->delete($room);


        return response()->json([
            'success' => true,

            'message' =>
                'Hostel room deleted successfully.',
        ]);
    }
}