<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryCategoryController extends Controller
{
    protected function schoolId(?\Illuminate\Http\Request $request = null): ?int
    {
        return $this->resolveSchoolId($request);
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = $this->schoolId();

        $query = InventoryCategory::query()
            ->when($schoolId, fn($q, $id) => $q->where('school_id', $id));

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                );

                $q->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                );
            });
        }

        if ($request->has('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        $categories = $query
            ->latest()
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();

        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'description' => [
                    'nullable',
                    'string'
                ],

                'status' => [
                    'nullable',
                    'boolean'
                ],
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $nameExists = InventoryCategory::where(
            'school_id',
            $schoolId
        )
        ->where(
            'name',
            $request->name
        )
        ->exists();

        if ($nameExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Category already exists.'
            ], 422);
        }

        if ($request->filled('code')) {

            $codeExists = InventoryCategory::where(
                'school_id',
                $schoolId
            )
            ->where(
                'code',
                $request->code
            )
            ->exists();

            if ($codeExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Category code already exists.'
                ], 422);
            }
        }

        $category = InventoryCategory::create([

            'school_id' =>
                $schoolId,

            'name' =>
                trim($request->name),

            'code' =>
                $request->code,

            'description' =>
                $request->description,

            'status' =>
                $request->has('status')
                    ? $request->boolean('status')
                    : true,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Inventory category created successfully.',
            'data' => $category
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $category = InventoryCategory::where(
            'school_id',
            $this->schoolId()
        )
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {
        $schoolId = $this->schoolId();

        $category = InventoryCategory::where(
            'school_id',
            $schoolId
        )
        ->findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'code' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'description' => [
                    'nullable',
                    'string'
                ],

                'status' => [
                    'nullable',
                    'boolean'
                ],
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $nameExists = InventoryCategory::where(
            'school_id',
            $schoolId
        )
        ->where(
            'name',
            $request->name
        )
        ->where(
            'id',
            '!=',
            $category->id
        )
        ->exists();

        if ($nameExists) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Category already exists.'
            ], 422);
        }

        if ($request->filled('code')) {

            $codeExists = InventoryCategory::where(
                'school_id',
                $schoolId
            )
            ->where(
                'code',
                $request->code
            )
            ->where(
                'id',
                '!=',
                $category->id
            )
            ->exists();

            if ($codeExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Category code already exists.'
                ], 422);
            }
        }

        $category->update([

            'name' =>
                trim($request->name),

            'code' =>
                $request->code,

            'description' =>
                $request->description,

            'status' =>
                $request->has('status')
                    ? $request->boolean('status')
                    : $category->status,
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Inventory category updated successfully.',
            'data' => $category
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $category = InventoryCategory::where(
            'school_id',
            $this->schoolId()
        )
        ->findOrFail($id);

        if ($category->items()->exists()) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Cannot delete this category because inventory items are linked to it.'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Inventory category deleted successfully.'
        ]);
    }
}