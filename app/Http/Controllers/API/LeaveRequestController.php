<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    protected $service;

    public function __construct(LeaveRequestService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing.
     */
    public function index(Request $request)
    {
        $leaveRequests = $this->service->paginate($request->all());

        return LeaveRequestResource::collection($leaveRequests);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreLeaveRequest $request)
    {
        $leaveRequest = $this->service->store($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Leave request created successfully.',
            'data' => new LeaveRequestResource($leaveRequest)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $leaveRequest = $this->service->find($id);

        return response()->json([
            'success' => true,
            'data' => new LeaveRequestResource($leaveRequest)
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateLeaveRequest $request, $id)
    {
        $leaveRequest = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave request updated successfully.',
            'data' => new LeaveRequestResource($leaveRequest)
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Leave request deleted successfully.'
        ]);
    }

    /**
     * Approve Leave Request
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $leaveRequest = $this->service->approve(
            $id,
            $request->remarks
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave request approved successfully.',
            'data' => new LeaveRequestResource($leaveRequest)
        ]);
    }

    /**
     * Reject Leave Request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $leaveRequest = $this->service->reject(
            $id,
            $request->remarks
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave request rejected successfully.',
            'data' => new LeaveRequestResource($leaveRequest)
        ]);
    }
}