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
                'code' => 200,
                'message' => 'Staff created successfully',
                'staff' => $staff,
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

   public function listUsers(): JsonResponse
{
    try {

        $users = User::where('role', Role::STAFF)->get();

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

public function getStaff($id):JsonResponse
{
    try{

    $user = User::with('role',Role::STAFF)->get();
    if($user){
        return response()->json([
            'status'=>false,
            'code'=> 100,
            'message'=>'user not found'
        ],100);
    }

    return response()->json([
        'status'=>true,
        'code'=>200,
        'message'=>'Data retrived successful',
        'user'=> $user,
    ],100);

    }
    catch(\Throwable $e){
        Log::error($e);

        return response()->json([
            'status'=>false,
            'code'=>100,
            'message'=>'something went wrong',
            'error' => $e->getMessage(),
        ],100);
    }
}

public function deleteStaff($id):JsonResponse
{
    try{

    $user = User::find($id);


        if (!$user || $user->role != Role::STAFF) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Staff not found'
            ], 404);
        }

         $user->delete();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Staff deleted successfully'
        ], 200);



    }
     catch(\Throwable $e){
        Log::error($e);

        return response()->json([
            'status'=>false,
            'code'=>100,
            'message'=>'something went wrong',
            'error' => $e->getMessage(),
        ],100);
    }
}

}
