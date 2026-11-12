<?php

namespace App\Http\Controllers\Api\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = User::query()
            ->with([
                'roles:id,name,guard_name',
                'school:id,name,code',
            ]);

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                (int) $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SCHOOL
        |--------------------------------------------------------------------------
        |
        | If the authenticated user has school_id,
        | restrict users to the same school.
        | Super Admin can filter by school or see all schools.
        |
        */

        $authUser = $request->user();
        $isSuperAdmin = $authUser && (
            $authUser->user_type === 'super_admin' || 
            (method_exists($authUser, 'hasRole') && ($authUser->hasRole('Super Admin') || $authUser->hasRole('super_admin'))) ||
            ($authUser->roles && $authUser->roles->contains('name', 'Super Admin'))
        );

        if (!$isSuperAdmin && !empty($authUser->school_id)) {
            $query->where('school_id', $authUser->school_id);
        } else if ($isSuperAdmin && $request->filled('school_id')) {
            if ($request->school_id !== 'all' && $request->school_id !== '0') {
                $query->where('school_id', $request->school_id);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->orderBy('name')
            ->paginate(
                $request->integer(
                    'per_page',
                    20
                )
            );

        $allSchools = $isSuperAdmin 
            ? \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']) 
            : [];

        return response()->json([
            'success' => true,
            'data' => $users,
            'all_schools' => $allSchools,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    */

    public function options(Request $request)
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

        $authUser = $request->user();
        $isSuperAdmin = $authUser && (
            $authUser->user_type === 'super_admin' || 
            (method_exists($authUser, 'hasRole') && ($authUser->hasRole('Super Admin') || $authUser->hasRole('super_admin'))) ||
            ($authUser->roles && $authUser->roles->contains('name', 'Super Admin'))
        );

        $schools = $isSuperAdmin 
            ? \App\Models\School::where('status', true)->orderBy('name')->get(['id', 'name', 'code']) 
            : [];

        return response()->json([
            'success' => true,

            'data' => [
                'roles' => $roles,
                'schools' => $schools,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        $id
    ) {

        $query = User::query()
            ->with([
                'roles:id,name,guard_name',
                'school:id,name,code',
            ]);

        /*
        |--------------------------------------------------------------------------
        | SCHOOL RESTRICTION
        |--------------------------------------------------------------------------
        */

        $authUser = $request->user();

        if (
            $authUser &&
            !empty($authUser->school_id)
        ) {

            $query->where(
                'school_id',
                $authUser->school_id
            );
        }

        $user = $query->find($id);

        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
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

            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'role_id' => [
                'nullable',
                'integer',
                'exists:roles,id',
            ],

        ]
    );


    if ($validator->fails()) {

        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | AUTH USER
    |--------------------------------------------------------------------------
    */

    $authUser = $request->user();


    /*
    |--------------------------------------------------------------------------
    | SCHOOL ID
    |--------------------------------------------------------------------------
    */

    $schoolId = null;

    if ($authUser) {

        $schoolId =
            $authUser->school_id ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | GET ROLE
    |--------------------------------------------------------------------------
    */

    $role = null;

    if ($request->filled('role_id')) {

        $role = Role::query()
            ->where('guard_name', 'sanctum')
            ->where('id', $request->role_id)
            ->first();

    }


    /*
    |--------------------------------------------------------------------------
    | DETERMINE USER TYPE
    |--------------------------------------------------------------------------
    */

    $userType = 'staff';


    if ($role) {

        $roleName = strtolower(
            trim($role->name)
        );


        switch ($roleName) {

            case 'super admin':
            case 'superadmin':
            case 'super_admin':

                $userType = 'super_admin';

                break;


            case 'school admin':
            case 'schooladmin':
            case 'school_admin':

                $userType = 'school_admin';

                break;


            case 'parent':

                $userType = 'parent';

                break;


            case 'student':

                $userType = 'student';

                break;


            default:

                $userType = 'staff';

                break;

        }
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */

    $user = DB::transaction(
        function () use (
            $request,
            $schoolId,
            $userType,
            $role
        ) {

            $user = User::create([

                'name' =>
                    trim($request->name),

                'username' =>
                    trim($request->username),

                'email' =>
                    strtolower(
                        trim($request->email)
                    ),

                'password' =>
                    Hash::make(
                        $request->password
                    ),

                'school_id' =>
                    $schoolId,

                /*
                |--------------------------------------------------------------------------
                | FIX
                |--------------------------------------------------------------------------
                */

                'user_type' =>
                    $userType,

                'status' =>
                    $request->has('status')
                        ? (int) $request->status
                        : 1,

            ]);


            /*
            |--------------------------------------------------------------------------
            | ASSIGN ROLE
            |--------------------------------------------------------------------------
            */

            if ($role) {

                $user->assignRole(
                    $role
                );

            }


            return $user;

        }
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD ROLE
    |--------------------------------------------------------------------------
    */

    $user->load(
        'roles:id,name,guard_name'
    );


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,

        'message' =>
            'User created successfully.',

        'data' =>
            $user,

    ], 201);
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

        $authUser =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------------------
        */

        $query = User::query();


        if (
            $authUser &&
            !empty($authUser->school_id)
        ) {

            $query->where(
                'school_id',
                $authUser->school_id
            );
        }


        $user = $query->find($id);


        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
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

                'username' => [

                    'required',

                    'string',

                    'max:255',

                    Rule::unique(
                        'users',
                        'username'
                    )->ignore(
                        $user->id
                    ),

                ],

                'email' => [

                    'required',

                    'email',

                    'max:255',

                    Rule::unique(
                        'users',
                        'email'
                    )->ignore(
                        $user->id
                    ),

                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:6',
                    'confirmed',
                ],

                'status' => [
                    'nullable',
                    'boolean',
                ],

                'role_id' => [
                    'nullable',
                    'integer',
                    'exists:roles,id',
                ],

            ]
        );


        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $request,
                $user
            ) {

                $data = [

                    'name' =>
                    trim($request->name),

                    'username' =>
                    trim(
                        $request->username
                    ),

                    'email' =>
                    strtolower(
                        trim(
                            $request->email
                        )
                    ),

                    'status' =>
                    $request->has('status')
                        ? (int) $request->status
                        : $user->status,

                ];


                /*
                |--------------------------------------------------------------------------
                | PASSWORD
                |--------------------------------------------------------------------------
                */

                if (
                    $request->filled(
                        'password'
                    )
                ) {

                    $data['password'] =
                        Hash::make(
                            $request->password
                        );
                }


                $user->update(
                    $data
                );


                /*
                |--------------------------------------------------------------------------
                | ROLE
                |--------------------------------------------------------------------------
                */

                if (
                    $request->has(
                        'role_id'
                    )
                ) {

                    if (
                        $request->filled(
                            'role_id'
                        )
                    ) {

                        $role =
                            Role::query()
                            ->where(
                                'guard_name',
                                'sanctum'
                            )
                            ->find(
                                $request->role_id
                            );

                        if ($role) {

                            $user->syncRoles([
                                $role
                            ]);
                        }
                    } else {

                        $user->syncRoles([]);
                    }
                }
            }
        );


        $user->load(
            'roles:id,name,guard_name'
        );


        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user,
        ]);
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

        $authUser =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------------------
        */

        $query = User::query();


        if (
            $authUser &&
            !empty($authUser->school_id)
        ) {

            $query->where(
                'school_id',
                $authUser->school_id
            );
        }


        $user = $query->find($id);


        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | PROTECT CURRENT USER
        |--------------------------------------------------------------------------
        */

        if (
            $authUser &&
            (int) $authUser->id ===
            (int) $user->id
        ) {

            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | PROTECT SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Admin',
                'sanctum'
            )
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Super Admin user cannot be deleted.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        $user->delete();


        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GET USER ROLES
    |--------------------------------------------------------------------------
    */

    public function roles(
        Request $request,
        $id
    ) {

        $authUser =
            $request->user();


        $query = User::query();


        if (
            $authUser &&
            !empty($authUser->school_id)
        ) {

            $query->where(
                'school_id',
                $authUser->school_id
            );
        }


        $user = $query->find($id);


        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }


        $roles = $user
            ->roles()
            ->where(
                'guard_name',
                'sanctum'
            )
            ->get([
                'id',
                'name',
                'guard_name',
            ]);


        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'roles' => $roles,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE USER ROLES
    |--------------------------------------------------------------------------
    */

    public function updateRoles(
        Request $request,
        $id
    ) {

        $authUser =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $query = User::query();


        if (
            $authUser &&
            !empty($authUser->school_id)
        ) {

            $query->where(
                'school_id',
                $authUser->school_id
            );
        }


        $user = $query->find($id);


        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
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

                'roles' => [
                    'nullable',
                    'array',
                ],

                'roles.*' => [
                    'integer',
                    'exists:roles,id',
                ],

            ]
        );


        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | GET ROLES
        |--------------------------------------------------------------------------
        */

        $roleIds =
            $request->input(
                'roles',
                []
            );


        $roles = Role::query()
            ->where(
                'guard_name',
                'sanctum'
            )
            ->whereIn(
                'id',
                $roleIds
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN PROTECTION
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Admin',
                'sanctum'
            ) &&
            !$roles->contains(
                'name',
                'Super Admin'
            )
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Super Admin role cannot be removed.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | SYNC
        |--------------------------------------------------------------------------
        */

        $user->syncRoles(
            $roles
        );


        $user->load(
            'roles:id,name,guard_name'
        );


        return response()->json([
            'success' => true,
            'message' => 'User roles updated successfully.',
            'data' => [
                'user' => $user,
                'roles' => $user->roles,
            ],
        ]);
    }
}
