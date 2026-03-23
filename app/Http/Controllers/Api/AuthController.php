<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {

            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $credentials['email'])->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // ✅ Check role
            if (! in_array($user->role, [Role::ADMIN, Role::STAFF])) {
                return response()->json([
                    'status' => false,
                    'code' => 403,
                    'message' => 'Access denied',
                ], 403);
            }

            // ✅ Check if suspended (IMPORTANT)
            if (isset($user->status) && $user->status == 0) {
                return response()->json([
                    'status' => false,
                    'code' => 403,
                    'message' => 'Account suspended',
                ], 403);
            }

            // ✅ Revoke old tokens (optional but recommended)
            $user->tokens()->delete();

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ], 200);

        } catch (\Throwable $e) {

            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        try {

            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'status' => true,
                    'code' => 200,
                    'message' => __($status),
                ], 200);
            }

            return response()->json([
                'status' => false,
                'code' => 400,
                'message' => __($status),
            ], 400);

        } catch (\Throwable $e) {

            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {

            $request->validate([
                'email' => 'required|email|exists:users,email',
                'token' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {

                    $user->password = Hash::make($password);
                    $user->save();

                    // ✅ Revoke all tokens after reset (security)
                    $user->tokens()->delete();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'status' => true,
                    'code' => 200,
                    'message' => __($status),
                ], 200);
            }

            return response()->json([
                'status' => false,
                'code' => 400,
                'message' => __($status),
            ], 400);

        } catch (\Throwable $e) {

            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
