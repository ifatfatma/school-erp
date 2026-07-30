<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Requests\SchoolRequest;
use App\Http\Resources\SchoolResource;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = School::query();

        // Optional search
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
        }

        $schools = $query->paginate(15);
        return SchoolResource::collection($schools);
    }

    public function store(SchoolRequest $request)
    {
        $school = School::create($request->validated());
        return new SchoolResource($school);
    }

    public function show(School $school)
    {
        return new SchoolResource($school);
    }

    public function update(SchoolRequest $request, School $school)
    {
        $school->update($request->validated());
        return new SchoolResource($school);
    }

    public function destroy(School $school)
    {
        $school->delete();
        return response()->json(['message' => 'School deleted successfully']);
    }

    // Optional: restore soft-deleted
    public function restore($id)
    {
        $school = School::withTrashed()->findOrFail($id);
        $school->restore();
        return new SchoolResource($school);
    }
}