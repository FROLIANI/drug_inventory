<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {

            // ✅ Validate input
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // ✅ Find user
            $user = User::where('email', $credentials['email'])->first();

            // ✅ Check password
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // ✅ Check role (ADMIN or STAFF allowed)
            if (!in_array($user->role, [Role::ADMIN, Role::STAFF])) {
                return response()->json([
                    'status' => false,
                    'code' => 403,
                    'message' => 'Access denied',
                ], 403);
            }

            // ✅ Generate token
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ], 200);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(Request $request):JsonResponse
    {
        try{
            $request->validate([
                'email'=>'required|email|exists:users,email',
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if($status === Password::RESET_LINK_SENT){
                return response->json([
                    'status'=>true,
                    'code'=>200,
                    'message'=> __($status),
                ],200);
            }

            return response()->json([
                'status'=>false,
                'code'=>400,
                'message'=> __($status),
            ],400);

        }

        catch(\Throwable $e){
            return response()->json([
                'status'=>false,
                'code'=>500,
                'message'=>'Something went wrong',
                'error'=>$e->getMessage()
            ],500);
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
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => __($status),
            ]);
        }

        return response()->json([
            'status' => false,
            'code' => 400,
            'message' => __($status),
        ], 400);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'code' => 500,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
