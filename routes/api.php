<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\TerimaController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ReferensiLayananController;
use App\Http\Controllers\RequestLayananController;
use App\Http\Controllers\UserpageController;
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
            Route::get('users/get', [UserController::class, 'get']);
            Route::post('users/store', [UserController::class, 'store']);
            Route::get('users/edit/{id}', [UserController::class, 'edit']);
            Route::put('users/update/{id}', [UserController::class, 'update']);
            Route::delete('users/destroy/{id}', [UserController::class, 'destroy']);
            Route::post('users/terima', [MitraController::class, 'terima']);
            Route::post('terima', [UserController::class, 'indexTerima']);
            Route::post('users/tolak', [MitraController::class, 'tolak']);
        });

        Route::middleware('can:master-role')->group(function () {
            Route::get('roles/get', [RoleController::class, 'get']);
            Route::post('roles', [RoleController::class, 'index']);
            Route::post('roles/show', [RoleController::class, 'show']);
            Route::post('roles/store', [RoleController::class, 'store']);
            Route::apiResource('roles', RoleController::class)
                ->except(['index', 'store']);
        });

        Route::middleware('can:master-pesanan')->group(function () {
            Route::post('pesanan', [PesananController::class, 'index']);
            Route::post('pesanan/store', [PesananController::class, 'store']);
        });

        Route::middleware('can:master-toko')->group(function () {
            Route::post('/toko', [TokoController::class, 'index']);
            Route::get('/toko/get', [TokoController::class, 'show']);
            Route::post('/toko/store', [TokoController::class, 'add']);
            Route::get('/toko/edit/{id}', [TokoController::class, 'edit']);
            Route::put('/toko/update{id}', [TokoController::class, 'update']);
            Route::delete('/toko/destroy/{id}', [TokoController::class, 'destroy']);
        });

        Route::middleware('can:master-layanan')->group(function () {
            Route::post('/layanan', [LayananController::class, 'index']);
            Route::get('/layanan/get', [LayananController::class, 'get']);
            Route::post('/layanan/store', [LayananController::class, 'store']);
            Route::get('/layanan/edit/{id}', [LayananController::class, 'edit']);
            Route::put('/layanan/update/{id}', [LayananController::class, 'update']);
            Route::delete('/layanan/destroy/{id}', [LayananController::class, 'destroy']);
        });

        Route::middleware('can:master-promo')->group(function () {
            Route::post('/promo', [PromoController::class, 'index']);
            Route::get('/promo/get', [PromoController::class, 'get']);
            Route::post('/promo/store', [PromoController::class, 'store']);
            Route::get('/promo/edit/{id}', [PromoController::class, 'edit']);
            Route::put('/promo/update/{id}', [PromoController::class, 'update']);
            Route::delete('/promo/destroy/{id}', [PromoController::class, 'destroy']);
        });

        Route::middleware('can:master-terima')->group(function () {
            Route::post('', [TerimaController::class, 'index']);
            Route::post('/store', [TerimaController::class, 'store']);
        });

        Route::middleware('can:master-referensi-layanan')->group(function () {
            Route::post('referensi/layanan', [ReferensiLayananController::class, 'index']);
            Route::get('/referensi/layanan/get', [ReferensiLayananController::class, 'get']);
            Route::post('/referensi/layanan/store', [ReferensiLayananController::class, 'store']);
            Route::post('/referensi/layanan/tolak', [ReferensiLayananController::class, 'tolak']);
            Route::post('/referensi/layanan/terima', [ReferensiLayananController::class, 'terima']);
            Route::get('/referensi/layanan/edit/{id}', [ReferensiLayananController::class, 'edit']);
            Route::put('/referensi/layanan/update/{id}', [ReferensiLayananController::class, 'update']);
            Route::delete('/referensi/layanan/destroy/{id}', [ReferensiLayananController::class, 'destroy']);
        });
        Route::middleware('can:master-request-layanan')->group(function () {
            Route::post('request/layanan', [RequestLayananController::class, 'index']);
            Route::get('/request/layanan/get', [RequestLayananController::class, 'get']);
            Route::post('/request/layanan/store', [RequestLayananController::class, 'store']);
            Route::get('/request/layanan/edit/{id}', [RequestLayananController::class, 'edit']);
            Route::put('/request/layanan/update/{id}', [RequestLayananController::class, 'update']);
            Route::delete('/request/layanan/destroy/{id}', [RequestLayananController::class, 'destroy']);
        });
    });
});

Route::prefix('userpage')->group(function () {
    Route::post('/store', [UserpageController::class, 'store']);
    Route::get('/toko/get', [UserpageController::class, 'show']);
    Route::get('/toko/shiw/{uuid}', [UserpageController::class, 'shiw']);
    Route::get('/toko/shaw/{userId}', [UserpageController::class, 'shaw']);
    Route::get('/toko/ahay/{uuid}', [UserpageController::class, 'ahay']);
});

Route::prefix('pesanan')->group(function () {
    Route::post('', [PesananController::class, 'index']);
    Route::post('/store', [PesananController::class, 'store']);
});