<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login',[AuthController::class,'login']);
Route::post('/forgot-password',[AuthController::class,'forgotPassword']);
Route::middleware('auth:sanctum')->group(function(){
Route::post('/reset-password',[AuthController::class,'resetPassword']);

 Route::post('/users', [AdminController::class, 'createStaff']);
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::get('/users/{id}', [AdminController::class, 'getStaff']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteStaff']);

});
