<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TokoController;
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

// Authentication Route
Route::middleware(['auth', 'json'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth');
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::prefix('setting')->group(function () {
    Route::get('', [SettingController::class, 'index']);
});

Route::middleware(['auth', 'verified', 'json'])->group(function () {
    Route::prefix('setting')->middleware('can:setting')->group(function () {
        Route::post('', [SettingController::class, 'update']);
    });

    Route::prefix('master')->group(function () {
        Route::middleware('can:master-user')->group(function () {
            Route::post('users', [UserController::class, 'index']);
            Route::get('users', [UserController::class, 'get']);
            Route::get('users/{id}', [UserController::class, 'getByid']);
            Route::post('users/store', [UserController::class, 'store']);
            Route::apiResource('users', UserController::class)
                ->except(['index', 'store'])->scoped(['user' => 'uuid']);
        });

        Route::middleware('can:master-role')->group(function () {
            Route::get('roles', [RoleController::class, 'get'])->withoutMiddleware('can:master-role');
            Route::post('roles', [RoleController::class, 'index']);
            Route::post('roles/store', [RoleController::class, 'store']);
            Route::apiResource('roles', RoleController::class)
                ->except(['index', 'store']);
        });

        Route::middleware('can:master-pesanan')->group(function () {
            Route::post('pesanan', [PesananController::class, 'index']);
        });

        Route::middleware('can:master-toko')->group(function () {
            Route::post('/toko', [TokoController::class, 'index']);
            Route::get('/toko/get', [TokoController::class, 'show']); 
            Route::post('/toko/store', [TokoController::class, 'add']);
            Route::get('/toko/edit/{id}', [TokoController::class, 'edit']); 
        });
    });
    
});  
