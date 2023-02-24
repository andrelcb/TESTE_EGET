<?php

use App\Http\Controllers\Api\Auth\LoginJwtController;
use App\Http\Controllers\Api\TasksController;
use App\Http\Controllers\Api\TasksSearchController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {
    Route::post('login', [LoginJwtController::class, 'Login']);
    Route::get('logout', [LoginJwtController::class, 'Logout']);
    Route::post('register', [UserController::class, 'store']);

    Route::get('search', [TasksSearchController::class, 'index']);

    Route::middleware('auth:api')->group(function () {
        Route::name('tasks')->group(function () {
            Route::get('tasks/{id}/users', [TasksController::class, 'Users']);
            Route::post('tasks/{id}/to-assign-task', [TasksController::class, 'toAssignTask']);
            Route::post('tasks/{id}/change-status-task', [TasksController::class, 'changeStatusTask']);
            Route::resource('tasks', TasksController::class);
        });

        Route::name('users')->group(function () {
            Route::get('users/{id}/tasks', [UserController::class, 'Tasks']);
            Route::resource('users', UserController::class);
        });
    });
});
