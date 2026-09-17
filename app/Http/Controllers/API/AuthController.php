<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => strtolower(
                trim($request->email)
            ),
            'password' => $request->password,
        ];


        /*
        |--------------------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------------------
        */

        $user = User::where(
            'email',
            $credentials['email']
        )->first();


        /*
        |--------------------------------------------------------------------------
        | USER NOT FOUND / PASSWORD INVALID
        |--------------------------------------------------------------------------
        */

        if (
            !$user ||
            !Hash::check(
                $credentials['password'],
                $user->password
            )
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            (int) $user->status !== 1
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact administrator.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN
        |--------------------------------------------------------------------------
        */

        Auth::login($user);


        /*
        |--------------------------------------------------------------------------
        | LOAD ROLES
        |--------------------------------------------------------------------------
        */

        $user->load([
            'roles:id,name,guard_name'
        ]);


        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $permissions =
            $user
            ->getAllPermissions()
            ->map(function ($permission) {

                return [
                    'id' =>
                    $permission->id,

                    'name' =>
                    $permission->name,

                    'guard_name' =>
                    $permission->guard_name,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | SANCTUM TOKEN
        |--------------------------------------------------------------------------
        */

        $token =
            $user
            ->createToken('admin')
            ->plainTextToken;


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'token' => $token,

            'user' => [

                'id' =>
                $user->id,

                'school_id' =>
                $user->school_id,

                'name' =>
                $user->name,

                'username' =>
                $user->username,

                'email' =>
                $user->email,

                'phone' =>
                $user->phone,

                'user_type' =>
                $user->user_type,

                'email_verified_at' =>
                $user->email_verified_at,

                'status' =>
                (bool) $user->status,

                'created_at' =>
                $user->created_at,

                'updated_at' =>
                $user->updated_at,

                'roles' =>
                $user->roles,

                'permissions' =>
                $permissions,

            ],

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        /*
    |--------------------------------------------------------------------------
    | Delete Current Sanctum Token
    |--------------------------------------------------------------------------
    */

        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    public function profile(Request $request)
    {
        try {

            $user = $request->user();

            if (!$user) {

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }


            /*
        |--------------------------------------------------------------------------
        | LOAD ROLES
        |--------------------------------------------------------------------------
        */

            $user->load([
                'roles:id,name,guard_name'
            ]);


            /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */

            $permissions = [];

            try {

                $permissions = $user
                    ->getAllPermissions()
                    ->map(function ($permission) {

                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'guard_name' => $permission->guard_name,
                        ];
                    })
                    ->values()
                    ->toArray();
            } catch (\Throwable $e) {

                \Log::error(
                    'Profile permission error: ' .
                        $e->getMessage()
                );

                $permissions = [];
            }


            /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

            return response()->json([

                'success' => true,

                'user' => [

                    'id' =>
                    $user->id,

                    'school_id' =>
                    $user->school_id,

                    'name' =>
                    $user->name,

                    'username' =>
                    $user->username,

                    'email' =>
                    $user->email,

                    'phone' =>
                    $user->phone,

                    'user_type' =>
                    $user->user_type,

                    'email_verified_at' =>
                    $user->email_verified_at,

                    'status' =>
                    (bool) $user->status,

                    'created_at' =>
                    $user->created_at,

                    'updated_at' =>
                    $user->updated_at,

                    'roles' =>
                    $user->roles,

                    'permissions' =>
                    $permissions,

                ],

            ]);
        } catch (\Throwable $e) {

            \Log::error(
                'Profile API Error: ' .
                    $e->getMessage(),
                [
                    'user_id' =>
                    optional($request->user())->id,

                    'trace' =>
                    $e->getTraceAsString(),
                ]
            );


            return response()->json([

                'success' => false,

                'message' =>
                'Unable to load profile.',

                /*
            |--------------------------------------------------------------------------
            | TEMPORARY DEBUG
            |--------------------------------------------------------------------------
            */

                'error' =>
                config('app.debug')
                    ? $e->getMessage()
                    : null,

            ], 500);
        }
    }
}
/*
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $user = Auth::user();

        if (!$user->hasAnyRole(['Super Admin', 'School Admin'])) {

            Auth::logout();

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $token = $user->createToken('admin')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }


public function profile(Request $request)
{
    $user = $request->user();

    return response()->json([
        'user' => [
            ...$user->toArray(),
            'permissions' => $user->getAllPermissions()->values(),
        ]
    ]);
}


}*/
