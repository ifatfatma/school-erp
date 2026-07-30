<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder);

        $subjects = $query->paginate(
            $request->get('per_page', 10)
        );

        return response()->json($subjects);
    }

    public function store(StoreSubjectRequest $request)
    {
        DB::beginTransaction();

        try {

            $subject = Subject::create([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'description' => $request->description,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Subject created successfully.',
                'data' => $subject
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create subject.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Subject $subject)
    {
        return response()->json($subject);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        DB::beginTransaction();

        try {

            $subject->update([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'description' => $request->description,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Subject updated successfully.',
                'data' => $subject->fresh()
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update subject.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Subject $subject)
    {
        DB::beginTransaction();

        try {

            $subject->delete();

            DB::commit();

            return response()->json([
                'message' => 'Subject deleted successfully.'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete subject.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}