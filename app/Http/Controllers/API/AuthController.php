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

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $passwordValid = Hash::check($credentials['password'], $user->password);

        // Fallback convenience for local environment: allow standard test passwords for seeded accounts
        if (!$passwordValid && app()->environment('local')) {
            $allowedDevPasswords = ['password', '12345678', 'admin123', 'admin', '123456', 'admin@123', 'admin_school1', 'admin_school2'];
            if (in_array($credentials['password'], $allowedDevPasswords)) {
                $user->password = Hash::make($credentials['password']);
                $user->save();
                $passwordValid = true;
            }
        }

        if (!$passwordValid) {
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
        | SCHOOL SCOPE CHECK (FOR SCHOOL-SPECIFIC PORTAL LOGINS)
        |--------------------------------------------------------------------------
        */
        $schoolCode = $request->input('school_code') ?? $request->header('X-School-Code');
        if ($schoolCode && !in_array($user->user_type, ['super_admin'])) {
            $targetSchool = \App\Models\School::where('code', $schoolCode)->first();
            if ($targetSchool && $user->school_id && (int)$user->school_id !== (int)$targetSchool->id) {
                return response()->json([
                    'success' => false,
                    'message' => "Your account belongs to {$user->school?->name}. Please sign in to your assigned school portal.",
                ], 403);
            }
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

        $user->load('school');
        $schoolData = null;
        if ($user->school) {
            $rawLogo = null;
            if (is_string($user->school->logo)) {
                $rawLogo = trim($user->school->logo);
            } elseif (is_array($user->school->logo)) {
                $first = reset($user->school->logo);
                $rawLogo = is_string($first) ? trim($first) : null;
            }

            $logoUrl = ($rawLogo && $rawLogo !== '[]' && $rawLogo !== 'null' && $rawLogo !== '{}')
                ? (str_starts_with($rawLogo, 'http') ? $rawLogo : asset('storage/' . ltrim($rawLogo, '/')))
                : null;

            $schoolData = [
                'id'      => $user->school->id,
                'name'    => $user->school->name,
                'code'    => $user->school->code,
                'logo'    => $logoUrl,
                'phone'   => $user->school->phone,
                'email'   => $user->school->email,
                'address' => $user->school->address,
                'subscription_plan' => $user->school->subscription_plan ?? 'starter',
                'subscription_status' => $user->school->subscription_status ?? 'active',
                'features' => $user->school->getActiveFeatures(),
            ];
        }

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

                'school' =>
                $schoolData,

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
        | LOAD ROLES & SCHOOL
        |--------------------------------------------------------------------------
        */

            $user->load([
                'roles:id,name,guard_name',
                'school'
            ]);

            $profileSchoolData = null;
            if ($user->school) {
                $rawLogo = null;
                if (is_string($user->school->logo)) {
                    $rawLogo = trim($user->school->logo);
                } elseif (is_array($user->school->logo)) {
                    $vals = array_values($user->school->logo);
                    $first = $vals[0] ?? null;
                    $rawLogo = is_string($first) ? trim($first) : null;
                }

                $logoUrl = ($rawLogo && $rawLogo !== '[]' && $rawLogo !== 'null' && $rawLogo !== '{}')
                    ? (str_starts_with($rawLogo, 'http') ? $rawLogo : asset('storage/' . ltrim($rawLogo, '/')))
                    : null;

                $profileSchoolData = [
                    'id'      => $user->school->id,
                    'name'    => $user->school->name,
                    'code'    => $user->school->code,
                    'logo'    => $logoUrl,
                    'phone'   => $user->school->phone,
                    'email'   => $user->school->email,
                    'address' => $user->school->address,
                    'subscription_plan' => $user->school->subscription_plan ?? 'starter',
                    'subscription_status' => $user->school->subscription_status ?? 'active',
                    'features' => $user->school->getActiveFeatures(),
                ];
            }


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

                    'school' =>
                    $profileSchoolData,

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

