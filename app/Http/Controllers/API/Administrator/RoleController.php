<?php

namespace App\Http\Controllers\Api\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class RoleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CHECK SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    protected function isSuperAdmin(?\Illuminate\Http\Request $request = null): bool
    {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Check user_type
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(
                trim(
                    (string) $user->user_type
                )
            ) === 'super_admin'
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Check role
        |--------------------------------------------------------------------------
        */

        try {
            return $user->hasRole('Super Admin');
        } catch (Throwable $e) {
            return false;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Role::query()
            ->where(
                'guard_name',
                'sanctum'
            )
            ->withCount('permissions')
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
        | PER PAGE
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


        /*
        |--------------------------------------------------------------------------
        | PAGINATE
        |--------------------------------------------------------------------------
        */

        $roles = $query->paginate(
            $perPage
        );


        /*
        |--------------------------------------------------------------------------
        | PROTECTED FLAG
        |--------------------------------------------------------------------------
        */

        $roles
            ->getCollection()
            ->transform(function ($role) {

                $role->protected =
                    $this->isProtectedRole(
                        $role->name
                    );

                return $role;
            });


        return response()->json([

            'success' => true,

            'message' =>
                'Roles retrieved successfully.',

            'data' => $roles,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    */

    public function options()
    {
        $roles = Role::query()
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

            'data' => $roles,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $role = Role::query()
            ->where(
                'guard_name',
                'sanctum'
            )
            ->with([
                'permissions:id,name,guard_name'
            ])
            ->find($id);


        if (!$role) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Role not found.',

            ], 404);
        }


        return response()->json([

            'success' => true,

            'data' => $role,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN ONLY
        |--------------------------------------------------------------------------
        */

        if (!$this->isSuperAdmin($request)) {

            return response()->json([

                'success' => false,

                'message' =>
                    'You do not have permission to create roles.',

            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validator = Validator::make(
            $request->all(),
            [

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'permission_ids' => [
                    'nullable',
                    'array',
                ],

                'permission_ids.*' => [
                    'integer',
                    'exists:permissions,id',
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
        | DUPLICATE ROLE
        |--------------------------------------------------------------------------
        */

        $exists = Role::query()
            ->where(
                'name',
                $name
            )
            ->where(
                'guard_name',
                'sanctum'
            )
            ->exists();


        if ($exists) {

            return response()->json([

                'success' => false,

                'message' =>
                    'This role already exists.',

                'errors' => [

                    'name' => [
                        'This role already exists.'
                    ],

                ],

            ], 422);
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | CREATE ROLE
            |--------------------------------------------------------------------------
            */

            $role = Role::create([

                'name' =>
                    $name,

                'guard_name' =>
                    'sanctum',

            ]);


            /*
            |--------------------------------------------------------------------------
            | ASSIGN PERMISSIONS
            |--------------------------------------------------------------------------
            */

            $permissionIds =
                is_array(
                    $request->permission_ids
                )
                    ? array_values(
                        array_unique(
                            array_map(
                                'intval',
                                $request->permission_ids
                            )
                        )
                    )
                    : [];


            if (!empty($permissionIds)) {

                $permissions =
                    Permission::query()
                        ->where(
                            'guard_name',
                            'sanctum'
                        )
                        ->whereIn(
                            'id',
                            $permissionIds
                        )
                        ->get();


                $role->syncPermissions(
                    $permissions
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CLEAR CACHE
            |--------------------------------------------------------------------------
            */

            app(
                PermissionRegistrar::class
            )->forgetCachedPermissions();


            /*
            |--------------------------------------------------------------------------
            | LOAD
            |--------------------------------------------------------------------------
            */

            $role->load([
                'permissions:id,name,guard_name'
            ]);


            return response()->json([

                'success' => true,

                'message' =>
                    'Role created successfully.',

                'data' =>
                    $role,

            ], 201);


        } catch (Throwable $e) {

            report($e);


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to create role.',

                'error' =>
                    config('app.debug')
                        ? $e->getMessage()
                        : null,

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ROLE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN ONLY
        |--------------------------------------------------------------------------
        */

        if (!$this->isSuperAdmin($request)) {

            return response()->json([

                'success' => false,

                'message' =>
                    'You do not have permission to update roles.',

            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | FIND ROLE
        |--------------------------------------------------------------------------
        */

        $role = Role::query()
            ->where(
                'guard_name',
                'sanctum'
            )
            ->find($id);


        if (!$role) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Role not found.',

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validator = Validator::make(
            $request->all(),
            [

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'permission_ids' => [
                    'nullable',
                    'array',
                ],

                'permission_ids.*' => [
                    'integer',
                    'exists:permissions,id',
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


        /*
        |--------------------------------------------------------------------------
        | ROLE NAME
        |--------------------------------------------------------------------------
        */

        $oldName =
            trim(
                (string) $role->name
            );


        $newName =
            trim(
                (string) $request->name
            );


        /*
        |--------------------------------------------------------------------------
        | PROTECTED ROLE NAME
        |--------------------------------------------------------------------------
        |
        | Super Admin and School Admin cannot be renamed.
        |
        */

        if (
            $this->isProtectedRole(
                $oldName
            )
        ) {

            $newName = $oldName;
        }


        /*
        |--------------------------------------------------------------------------
        | DUPLICATE NAME
        |--------------------------------------------------------------------------
        */

        $exists = Role::query()
            ->where(
                'name',
                $newName
            )
            ->where(
                'guard_name',
                'sanctum'
            )
            ->where(
                'id',
                '!=',
                $role->id
            )
            ->exists();


        if ($exists) {

            return response()->json([

                'success' => false,

                'message' =>
                    'This role already exists.',

                'errors' => [

                    'name' => [
                        'This role already exists.'
                    ],

                ],

            ], 422);
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | UPDATE ROLE NAME
            |--------------------------------------------------------------------------
            */

            if ($role->name !== $newName) {

                $role->name =
                    $newName;

                $role->save();
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE PERMISSIONS
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | Super Admin permissions CAN be modified.
            |
            */

            $permissionIds =
                is_array(
                    $request->permission_ids
                )
                    ? array_values(
                        array_unique(
                            array_map(
                                'intval',
                                $request->permission_ids
                            )
                        )
                    )
                    : [];


            $permissions =
                Permission::query()
                    ->where(
                        'guard_name',
                        'sanctum'
                    )
                    ->whereIn(
                        'id',
                        $permissionIds
                    )
                    ->get();


            $role->syncPermissions(
                $permissions
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR CACHE
            |--------------------------------------------------------------------------
            */

            app(
                PermissionRegistrar::class
            )->forgetCachedPermissions();


            /*
            |--------------------------------------------------------------------------
            | LOAD UPDATED ROLE
            |--------------------------------------------------------------------------
            */

            $role->load([
                'permissions:id,name,guard_name'
            ]);


            return response()->json([

                'success' => true,

                'message' =>
                    'Role updated successfully.',

                'data' =>
                    $role,

            ]);


        } catch (Throwable $e) {

            report($e);


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to update role.',

                'error' =>
                    config('app.debug')
                        ? $e->getMessage()
                        : null,

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN ONLY
        |--------------------------------------------------------------------------
        */

        if (!$this->isSuperAdmin($request)) {

            return response()->json([

                'success' => false,

                'message' =>
                    'You do not have permission to delete roles.',

            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | FIND ROLE
        |--------------------------------------------------------------------------
        */

        $role = Role::query()
            ->where(
                'guard_name',
                'sanctum'
            )
            ->find($id);


        if (!$role) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Role not found.',

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | PROTECTED ROLES
        |--------------------------------------------------------------------------
        */

        if (
            $this->isProtectedRole(
                $role->name
            )
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'This role is protected and cannot be deleted.',

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | USERS ASSIGNED
        |--------------------------------------------------------------------------
        */

        try {

            $usersCount =
                $role->users()->count();

        } catch (Throwable $e) {

            $usersCount = 0;
        }


        if ($usersCount > 0) {

            return response()->json([

                'success' => false,

                'message' =>
                    'This role is assigned to ' .
                    $usersCount .
                    ' user(s). Remove the role from those users before deleting.',

            ], 422);
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | REMOVE PERMISSIONS
            |--------------------------------------------------------------------------
            */

            $role->syncPermissions([]);


            /*
            |--------------------------------------------------------------------------
            | DELETE
            |--------------------------------------------------------------------------
            */

            $role->delete();


            /*
            |--------------------------------------------------------------------------
            | CLEAR CACHE
            |--------------------------------------------------------------------------
            */

            app(
                PermissionRegistrar::class
            )->forgetCachedPermissions();


            return response()->json([

                'success' => true,

                'message' =>
                    'Role deleted successfully.',

            ]);


        } catch (Throwable $e) {

            report($e);


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to delete role.',

                'error' =>
                    config('app.debug')
                        ? $e->getMessage()
                        : null,

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GET ROLE PERMISSIONS
    |--------------------------------------------------------------------------
    */

    public function permissions($id)
    {
        $role = Role::query()
            ->where(
                'guard_name',
                'sanctum'
            )
            ->with([
                'permissions:id,name,guard_name'
            ])
            ->find($id);


        if (!$role) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Role not found.',

            ], 404);
        }


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

                'role' =>
                    $role,

                'permissions' =>
                    $permissions,

                'selected' =>
                    $role->permissions
                        ->pluck('id')
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->values(),

            ],

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SYNC ROLE PERMISSIONS
    |--------------------------------------------------------------------------
    */

    public function syncPermissions(
        Request $request,
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN ONLY
        |--------------------------------------------------------------------------
        */

        if (!$this->isSuperAdmin($request)) {

            return response()->json([

                'success' => false,

                'message' =>
                    'You do not have permission to manage role permissions.',

            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | FIND ROLE
        |--------------------------------------------------------------------------
        */

        $role = Role::query()
            ->where(
                'guard_name',
                'sanctum'
            )
            ->find($id);


        if (!$role) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Role not found.',

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validator = Validator::make(
            $request->all(),
            [

                'permission_ids' => [
                    'nullable',
                    'array',
                ],

                'permission_ids.*' => [
                    'integer',
                    'exists:permissions,id',
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


        try {

            /*
            |--------------------------------------------------------------------------
            | PERMISSION IDS
            |--------------------------------------------------------------------------
            */

            $permissionIds =
                is_array(
                    $request->permission_ids
                )
                    ? array_values(
                        array_unique(
                            array_map(
                                'intval',
                                $request->permission_ids
                            )
                        )
                    )
                    : [];


            /*
            |--------------------------------------------------------------------------
            | GET PERMISSIONS
            |--------------------------------------------------------------------------
            */

            $permissions =
                Permission::query()
                    ->where(
                        'guard_name',
                        'sanctum'
                    )
                    ->whereIn(
                        'id',
                        $permissionIds
                    )
                    ->get();


            /*
            |--------------------------------------------------------------------------
            | SYNC
            |--------------------------------------------------------------------------
            |
            | This deliberately allows Super Admin to modify
            | its own permissions.
            |
            */

            $role->syncPermissions(
                $permissions
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR SPATIE CACHE
            |--------------------------------------------------------------------------
            */

            app(
                PermissionRegistrar::class
            )->forgetCachedPermissions();


            /*
            |--------------------------------------------------------------------------
            | LOAD
            |--------------------------------------------------------------------------
            */

            $role->load([
                'permissions:id,name,guard_name'
            ]);


            return response()->json([

                'success' => true,

                'message' =>
                    'Role permissions updated successfully.',

                'data' =>
                    $role,

            ]);


        } catch (Throwable $e) {

            report($e);


            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to update role permissions.',

                'error' =>
                    config('app.debug')
                        ? $e->getMessage()
                        : null,

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PROTECTED ROLE
    |--------------------------------------------------------------------------
    */

    private function isProtectedRole(
        ?string $name
    ): bool {

        return in_array(

            strtolower(
                trim(
                    (string) $name
                )
            ),

            [
                'super admin',
                'school admin',
            ],

            true

        );
    }
}