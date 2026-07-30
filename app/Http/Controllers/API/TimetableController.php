<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimetableRequest;
use App\Http\Requests\UpdateTimetableRequest;
use App\Http\Resources\TimetableResource;
use App\Services\TimetableService;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    protected TimetableService $service;

    public function __construct(TimetableService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $timetables = $this->service->paginate($request->all());

        return TimetableResource::collection($timetables);
    }

    public function store(StoreTimetableRequest $request)
    {
        $timetable = $this->service->store($request->validated());

        return new TimetableResource($timetable);
    }

    public function show(int $id)
    {
        $timetable = $this->service->find($id);

        abort_if(!$timetable, 404, 'Timetable not found.');

        return new TimetableResource($timetable);
    }

    public function update(UpdateTimetableRequest $request, int $id)
    {
        $timetable = $this->service->update(
            $id,
            $request->validated()
        );

        return new TimetableResource($timetable);
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Timetable deleted successfully.'
        ]);
    }
}