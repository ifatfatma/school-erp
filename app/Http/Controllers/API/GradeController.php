<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::query()
            ->where('school_id', auth()->user()->school_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $query
                ->orderByDesc('min_percentage')
                ->paginate($request->get('per_page', 15))
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateGrade($request);

        $validated['school_id'] = auth()->user()->school_id;
        $validated['created_by'] = auth()->id();

        $grade = Grade::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Grade created successfully.',
            'data' => $grade,
        ], 201);
    }

    public function show(Grade $grade)
    {
        $this->checkSchool($grade);

        return response()->json([
            'success' => true,
            'data' => $grade,
        ]);
    }

    public function update(Request $request, Grade $grade)
    {
        $this->checkSchool($grade);

        $validated = $this->validateGrade(
            $request,
            $grade->id
        );

        $validated['updated_by'] = auth()->id();

        $grade->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Grade updated successfully.',
            'data' => $grade->fresh(),
        ]);
    }

    public function destroy(Grade $grade)
    {
        $this->checkSchool($grade);

        $grade->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grade deleted successfully.',
        ]);
    }

    public function active()
    {
        $grades = Grade::query()
            ->where('school_id', auth()->user()->school_id)
            ->where('status', true)
            ->orderByDesc('min_percentage')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $grades,
        ]);
    }

    private function validateGrade(
        Request $request,
        ?int $id = null
    ): array {
        return $request->validate([

            'name' => [
                'required',
                'string',
                'max:20',

                Rule::unique('grades', 'name')
                    ->where(function ($query) {
                        $query->where(
                            'school_id',
                            auth()->user()->school_id
                        );
                    })
                    ->ignore($id),
            ],

            'min_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'max_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'gte:min_percentage',
            ],

            'grade_point' => [
                'required',
                'numeric',
                'min:0',
                'max:20',
            ],

            'is_pass' => [
                'required',
                'boolean',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);
    }

    private function checkSchool(Grade $grade): void
    {
        abort_if(
            $grade->school_id != auth()->user()->school_id,
            404
        );
    }
}