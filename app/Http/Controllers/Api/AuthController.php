<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{

public function login(Request $request):JsonResponse
{

    try{

      $credentails = validate([
        'username'=>'required|string|unque:users',
        'password'=>'required'
    ]);

    if(!Auth::attempt($credentails, $request->boolean('remember'))){
        return response()->json([
            'status'=>false,
            'code'=>401,
            'message'=>'Invalid credentails',
        ],401);
    }



    }
    catch(\Throwable $e){

    }
}

}
