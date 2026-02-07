<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use PSpell\Config;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider.
|
*/

Route::get('/', function () {
    return response()->json(['status' => 'PHP API is running']);
});

// User API routes
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);