<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider.
|
*/

Route::get('/health', function () {
    return response()->json(['status' => 'PHP API is running']);
});

// User API routes
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::post('/user', [UserController::class, 'store']);
Route::post('/test', function () {
    return response()->json(['message' => 'Test endpoint reached']);
});