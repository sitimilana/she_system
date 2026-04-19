<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SlipGajiController;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AbsensiController;

use App\Http\Controllers\Api\CutiController;
use App\Http\Controllers\Api\ConfigPresensiController;

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

/* MOBILE - Slip Gaji Karyawan */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/slip-gaji', [SlipGajiController::class, 'index'])->name('api.slip-gaji.index');
    Route::get('/slip-gaji/{id}', [SlipGajiController::class, 'show'])->name('api.slip-gaji.show');
});


/* MOBILE - Bebas Akses (Belum punya token) */
Route::post('/login', [AuthController::class, 'login']);

/* MOBILE - Harus Pakai Token Sanctum */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/slip-gaji', [SlipGajiController::class, 'index'])->name('api.slip-gaji.index');
    Route::get('/slip-gaji/{id}', [SlipGajiController::class, 'show'])->name('api.slip-gaji.show');
});

Route::post('/absensi', [AbsensiController::class, 'store']);

/* MOBILE - Manajemen Cuti Karyawan */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cuti/sisa-cuti', [CutiController::class, 'sisaCuti'])->name('api.cuti.sisa-cuti');
    Route::get('/cuti', [CutiController::class, 'index'])->name('api.cuti.index');
    Route::post('/cuti', [CutiController::class, 'store'])->name('api.cuti.store');
});

Route::get('/config-presensi', ConfigPresensiController::class)->name('api.config-presensi');

