<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResultGrade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResultGradeController extends Controller
{
    public function index(Request $request)
    {
        $query = ResultGrade::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            );

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );

            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        return response()->json(
            $query
                ->orderByDesc('min_percentage')
                ->paginate(
                    $request->get('per_page', 15)
                )
        );
    }

    public function store(Request $request)
    {
        $validated =
            $this->validateGrade($request);

        $validated['school_id'] =
            auth()->user()->school_id;

        $validated['created_by'] =
            auth()->id();

        $grade =
            ResultGrade::create($validated);

        return response()->json([
            'success' => true,
            'message' =>
                'Result grade created successfully.',
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

        $validated =
            $this->validateGrade(
                $request,
                $resultGrade->id
            );

        $validated['updated_by'] =
            auth()->id();

        $resultGrade->update($validated);

        return response()->json([
            'success' => true,
            'message' =>
                'Result grade updated successfully.',
            'data' => $resultGrade->fresh(),
        ]);
    }

    public function destroy(ResultGrade $resultGrade)
    {
        $this->checkSchool($resultGrade);

        $resultGrade->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Result grade deleted successfully.',
        ]);
    }

    public function active()
    {
        $grades = ResultGrade::query()
            ->where(
                'school_id',
                auth()->user()->school_id
            )
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

                Rule::unique(
                    'result_grades',
                    'name'
                )
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

    private function checkSchool(
        ResultGrade $resultGrade
    ): void {
        abort_if(
            $resultGrade->school_id !=
                auth()->user()->school_id,
            404
        );
    }
}