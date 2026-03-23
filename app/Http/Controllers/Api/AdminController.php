<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function createStaff(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $staff = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => Role::STAFF
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'code' => 201,
                'message' => 'Staff created successfully',
                'staff' => $staff,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function listUsers(): JsonResponse
    {
        try {
            $users = User::where('role', Role::STAFF)->latest()->get();

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Staff data retrieved successfully',
                'data' => $users
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

    public function getStaff($id): JsonResponse
    {
        try {
            $user = User::where('id', $id)
                        ->where('role', Role::STAFF)
                        ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Staff not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'user' => $user,
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

    public function deleteStaff($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::find($id);

            if (!$user || $user->role != Role::STAFF) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Staff not found'
                ], 404);
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Staff deleted successfully'
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
