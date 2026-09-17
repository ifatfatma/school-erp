<?php

namespace App\Http\Controllers\Api\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Throwable;

class PermissionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Permission::query()
            ->where('guard_name', 'sanctum')
            ->orderBy('name');


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(
                'name',
                'like',
                '%' . $search . '%'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage = (int) (
            $request->per_page ?? 20
        );

        if ($perPage < 1) {
            $perPage = 20;
        }

        if ($perPage > 100) {
            $perPage = 100;
        }


        $permissions =
            $query->paginate(
                $perPage
            );


        return response()->json([

            'success' => true,

            'message' =>
                'Permissions retrieved successfully.',

            'data' => $permissions,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

            ],
            [

                'name.required' =>
                    'Permission name is required.',

            ]
        );


        if ($validator->fails()) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validation failed.',

                'errors' =>
                    $validator->errors(),

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE NAME
        |--------------------------------------------------------------------------
        */

        $name = trim(
            $request->name
        );


        /*
        |--------------------------------------------------------------------------
        | CHECK DUPLICATE
        |--------------------------------------------------------------------------
        */

        $exists = Permission::query()
            ->where('name', $name)
            ->where('guard_name', 'sanctum')
            ->exists();


        if ($exists) {

            return response()->json([

                'success' => false,

                'message' =>
                    'This permission already exists.',

                'errors' => [

                    'name' => [
                        'This permission already exists.'
                    ]

                ],

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        try {

            $permission =
                Permission::create([

                    'name' =>
                        $name,

                    'guard_name' =>
                        'sanctum',

                ]);


            return response()->json([

                'success' => true,

                'message' =>
                    'Permission created successfully.',

                'data' =>
                    $permission,

            ], 201);

        } catch (Throwable $e) {

            report($e);


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to create permission.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $permission =
            Permission::query()
                ->where(
                    'guard_name',
                    'sanctum'
                )
                ->find($id);


        if (!$permission) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Permission not found.',

            ], 404);
        }


        return response()->json([

            'success' => true,

            'data' =>
                $permission,

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

        $permission =
            Permission::query()
                ->where(
                    'guard_name',
                    'sanctum'
                )
                ->find($id);


        if (!$permission) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Permission not found.',

            ], 404);
        }


        $validator = Validator::make(
            $request->all(),
            [

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

            ]
        );


        if ($validator->fails()) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validation failed.',

                'errors' =>
                    $validator->errors(),

            ], 422);
        }


        $name = trim(
            $request->name
        );


        /*
        |--------------------------------------------------------------------------
        | DUPLICATE CHECK
        |--------------------------------------------------------------------------
        */

        $exists = Permission::query()
            ->where('name', $name)
            ->where(
                'guard_name',
                'sanctum'
            )
            ->where(
                'id',
                '!=',
                $permission->id
            )
            ->exists();


        if ($exists) {

            return response()->json([

                'success' => false,

                'message' =>
                    'This permission already exists.',

                'errors' => [

                    'name' => [
                        'This permission already exists.'
                    ]

                ],

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        try {

            $permission->update([

                'name' =>
                    $name,

            ]);


            /*
            |--------------------------------------------------------------------------
            | CLEAR SPATIE CACHE
            |--------------------------------------------------------------------------
            */

            app(
                \Spatie\Permission\PermissionRegistrar::class
            )->forgetCachedPermissions();


            return response()->json([

                'success' => true,

                'message' =>
                    'Permission updated successfully.',

                'data' =>
                    $permission->fresh(),

            ]);

        } catch (Throwable $e) {

            report($e);


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to update permission.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $permission =
            Permission::query()
                ->where(
                    'guard_name',
                    'sanctum'
                )
                ->find($id);


        if (!$permission) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Permission not found.',

            ], 404);
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | CHECK WHETHER USED BY ROLES
            |--------------------------------------------------------------------------
            */

            $rolesCount =
                $permission
                    ->roles()
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | Don't delete permissions currently
            | assigned to roles.
            |--------------------------------------------------------------------------
            */

            if ($rolesCount > 0) {

                return response()->json([

                    'success' => false,

                    'message' =>
                        'This permission is assigned to ' .
                        $rolesCount .
                        ' role(s). Remove it from those roles before deleting.',

                ], 422);
            }


            $permission->delete();


            /*
            |--------------------------------------------------------------------------
            | CLEAR CACHE
            |--------------------------------------------------------------------------
            */

            app(
                \Spatie\Permission\PermissionRegistrar::class
            )->forgetCachedPermissions();


            return response()->json([

                'success' => true,

                'message' =>
                    'Permission deleted successfully.',

            ]);

        } catch (Throwable $e) {

            report($e);


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to delete permission.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    |
    | Useful for Role → Permissions assignment.
    |
    */

    public function options()
    {
        $permissions =
            Permission::query()
                ->where(
                    'guard_name',
                    'sanctum'
                )
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'guard_name',
                ]);


        return response()->json([

            'success' => true,

            'data' => [

                'permissions' =>
                    $permissions,

            ],

        ]);
    }
}