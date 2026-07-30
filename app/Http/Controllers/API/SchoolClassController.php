<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolClassController extends Controller
{
    /**
     * Display a listing.
     */
    public function index(Request $request)
    {
        $query = SchoolClass::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }
       
        // Status Filter
        if (isset($request->status) && $request->has('status')) {
            $status = filter_var($request->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($status !== null) {
                $query->where('status', $status ? 1 : 0);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $classes = $query->paginate(
            $request->get('per_page', 10)
        );
        Log::info($classes);

        return response()->json($classes);
    }

    /**
     * Store a newly created class.
     */
    public function store(StoreSchoolClassRequest $request)
    {

        DB::beginTransaction();

        try {

            $class = SchoolClass::create([
                'name'         => $request->name,
                'numeric_name' => $request->numeric_name,
                'remarks'      => $request->remarks,
                'status'       => $request->status,
                'created_by'   => auth()->id(),
                'school_id'    => auth()->user()->school_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Class created successfully.',
                'data'    => $class
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified class.
     */
    public function show(SchoolClass $class)
    {
        return response()->json([
            'success' => true,
            'data' => $class
        ]);
    }

    /**
     * Update the specified class.
     */
    public function update(UpdateSchoolClassRequest $request, SchoolClass $class)
    {
        DB::beginTransaction();

        try {

            $class->update([
                'name'         => $request->name,
                'numeric_name' => $request->numeric_name,
                'remarks'      => $request->remarks,
                'status'       => $request->status,
                'updated_by'   => auth()->id(),
                'school_id'    => auth()->user()->school_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Class updated successfully.',
                'data'    => $class
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified class.
     */
    public function destroy(SchoolClass $class)
    {
        DB::beginTransaction();

        try {

            $class->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Class deleted successfully.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
