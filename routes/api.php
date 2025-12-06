<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\CategoryController;


Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);


Route::group(['middleware' => ['jwt.auth']], function () {
  
    Route::post('user-logout', [AuthController::class, 'logout']);
   
    // Category APIs
    Route::get('category-list', [CategoryController::class, 'index']);
    Route::post('category-create', [CategoryController::class, 'store']);
    Route::get('category-detail/{id}', [CategoryController::class, 'show']);
    Route::post('category-update/{id}', [CategoryController::class, 'update']);
    Route::delete('category-delete/{id}', [CategoryController::class, 'destroy']);

    // Task APIs
    Route::get('task-list', [TaskController::class, 'index']);
    Route::post('task-create', [TaskController::class, 'store']);
    Route::get('task-detail/{id}', [TaskController::class, 'show']);
    Route::post('task-update/{id}', [TaskController::class, 'update']);
    Route::delete('task-delete/{id}', [TaskController::class, 'destroy']);
});
