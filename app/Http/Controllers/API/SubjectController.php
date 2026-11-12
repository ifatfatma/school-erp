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
        $user = $request->user();
        $isSuperAdmin = $user && (
            $user->user_type === 'super_admin' || 
            (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))) ||
            ($user->roles && $user->roles->contains('name', 'Super Admin'))
        );

        $query = Subject::query()->with('school:id,name,code');

        if ($request->filled('school_id') && $request->school_id !== 'all') {
            $query->where('school_id', $request->school_id);
        }

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

        if ($isSuperAdmin) {
            $data = $subjects->toArray();
            $data['all_schools'] = \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']);
            return response()->json($data);
        }

        return response()->json($subjects);
    }

    public function store(StoreSubjectRequest $request)
    {
        $tenantManager = app(\App\Services\TenantManager::class);
        $schoolId = $request->input('school_id') 
            ?? $tenantManager->getTenantId() 
            ?? $request->user()?->school_id;

        if (!$schoolId) {
            $schoolId = \App\Models\School::first()?->id ?? 1;
        }

        DB::beginTransaction();

        try {

            $subject = Subject::create([
                'school_id' => $schoolId,
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'description' => $request->description,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
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
                'success' => true,
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


        public function active()
{
    return response()->json([
        'success' => true,
        'data' => Subject::where('status', 1)
            ->orderBy('name')
            ->get()
    ]);
}
}