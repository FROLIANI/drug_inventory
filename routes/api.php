<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::post('/users', [AdminController::class, 'createStaff']);
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::get('/users/{id}', [AdminController::class, 'getStaff']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteStaff']);

    Route::post('/drugs', [DrugController::class, 'addDrug']);
    Route::get('/drugs', [DrugController::class, 'listDrugs']);
    Route::get('/drugs/{id}', [DrugController::class, 'viewDrug']);
    Route::put('/drugs/{id}', [DrugController::class, 'editDrug']);
    Route::patch('/drugs/{id}/suspend', [DrugController::class, 'suspendDrug']);
    Route::delete('/drugs/{id}', [DrugController::class, 'deleteDrug']);
    Route::post('/drugs/{id}/adjust-stock', [DrugController::class, 'adjustStock']);

});
