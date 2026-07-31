<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Requests\UpdateLeaveTypeRequest;
use App\Http\Resources\LeaveTypeResource;
use App\Services\LeaveTypeService;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    protected LeaveTypeService $service;

    public function __construct(LeaveTypeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing.
     */
    public function index(Request $request)
    {
        $leaveTypes = $this->service->paginate($request->all());

        return LeaveTypeResource::collection($leaveTypes);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreLeaveTypeRequest $request)
    {
        $leaveType = $this->service->store($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Leave Type created successfully.',
            'data' => new LeaveTypeResource($leaveType),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $leaveType = $this->service->find($id);

        return new LeaveTypeResource($leaveType);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateLeaveTypeRequest $request, int $id)
    {
        $leaveType = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave Type updated successfully.',
            'data' => new LeaveTypeResource($leaveType),
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Leave Type deleted successfully.',
        ]);
    }
}