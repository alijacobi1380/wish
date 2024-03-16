<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsersController;
use App\Models\User;
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

Route::prefix('v1')->group(function () {

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::delete('/logout', [UsersController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/login', [UsersController::class, 'login']);
    Route::post('/register', [UsersController::class, 'register']);



    Route::name('admin.')->prefix('admin')->middleware(['Admin', 'auth:sanctum'])->group(function () {
        Route::get('getusers', [AdminController::class, 'Getusers'])->name('getusers');
    });
});
