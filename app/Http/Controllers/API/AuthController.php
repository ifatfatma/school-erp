<?php

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


}
