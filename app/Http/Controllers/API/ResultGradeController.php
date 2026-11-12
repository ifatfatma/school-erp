<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResultGrade;
use App\Models\School;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResultGradeController extends Controller
{
    public function index(Request $request)
    {
        $isSuperAdmin = $this->isSuperAdmin($request);
        $schoolId = $this->resolveSchoolId($request);

        $query = ResultGrade::query()->with('school:id,name,code');

        if ($request->input('school_id') === 'all') {
            // Super admin chose All Schools
        } elseif ($schoolId !== null) {
            $query->where('school_id', $schoolId);
        } elseif (!$isSuperAdmin && auth()->user()?->school_id) {
            $query->where('school_id', auth()->user()->school_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $paginated = $query
            ->orderByDesc('min_percentage')
            ->paginate($request->get('per_page', 100));

        if ($isSuperAdmin) {
            $data = $paginated->toArray();
            $data['all_schools'] = School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']);
            return response()->json($data);
        }

        return response()->json($paginated);
    }

    public function store(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        if (!$schoolId) {
            $schoolId = School::first()?->id ?? 1;
        }

        $validated = $this->validateGrade($request, null, $schoolId);
        $validated['school_id'] = $schoolId;
        $validated['created_by'] = auth()->id();

        $grade = ResultGrade::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Result grade created successfully.',
            'data' => $grade,
        ], 201);
    }

    public function show(ResultGrade $resultGrade)
    {
        $this->checkSchool($resultGrade);

        return response()->json([
            'success' => true,
            'data' => $resultGrade,
        ]);
    }

    public function update(
        Request $request,
        ResultGrade $resultGrade
    ) {
        $this->checkSchool($resultGrade);

        $validated = $this->validateGrade(
            $request,
            $resultGrade->id,
            $resultGrade->school_id
        );

        $validated['updated_by'] = auth()->id();

        $resultGrade->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Result grade updated successfully.',
            'data' => $resultGrade->fresh(),
        ]);
    }

    public function destroy(ResultGrade $resultGrade)
    {
        $this->checkSchool($resultGrade);

        $resultGrade->delete();

        return response()->json([
            'success' => true,
            'message' => 'Result grade deleted successfully.',
        ]);
    }

    public function active(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $query = ResultGrade::query()
            ->where('status', true)
            ->orderByDesc('min_percentage');

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $grades = $query->get();

        return response()->json([
            'success' => true,
            'data' => $grades,
        ]);
    }

    private function validateGrade(
        Request $request,
        ?int $id = null,
        ?int $schoolId = null
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('result_grades', 'name')
                    ->where(function ($query) use ($schoolId) {
                        if ($schoolId) {
                            $query->where('school_id', $schoolId);
                        }
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

    private function checkSchool(ResultGrade $resultGrade): void
    {
        $user = auth()->user();
        if ($user && $this->isSuperAdmin(request())) {
            return;
        }

        abort_if(
            $resultGrade->school_id != $user?->school_id,
            404
        );
    }
}
