<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\ConfigPresensiController;
use App\Http\Controllers\Api\PengajuanController;
use App\Http\Controllers\Api\ApiPenilaianController;
use App\Http\Controllers\Api\SlipGajiController;
use App\Http\Controllers\RewardController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::get('/config-presensi', [ConfigPresensiController::class, '__invoke']); 
Route::post('/absensi', [AbsensiController::class, 'store']);
Route::post('/absensi/riwayat', [AbsensiController::class, 'riwayatAbsensi']);
Route::get('/config-presensi', [AbsensiController::class, 'getConfigPresensi']);

Route::middleware('auth:sanctum')->group(function () {
    // Cuti
    Route::get('/cuti/sisa', [PengajuanController::class, 'sisaCuti']);
    Route::get('/cuti', [PengajuanController::class, 'index']);
    Route::post('/cuti', [PengajuanController::class, 'store']);
    Route::put('/cuti/{id}', [PengajuanController::class, 'update']);
    Route::delete('/cuti/{id}', [PengajuanController::class, 'destroy']);
    
    // Penilaian
   Route::get('/penilaian', [ApiPenilaianController::class, 'index']);
   Route::get('/penilaian/dashboard', [ApiPenilaianController::class, 'dashboard']);
    Route::get('/penilaian/detail', [ApiPenilaianController::class, 'getPenilaian']);
    
    // Gaji (Di ApiService dipanggil 'gaji')
    Route::get('/gaji', [SlipGajiController::class, 'index']);
    Route::get('/gaji/{id}', [SlipGajiController::class, 'show']);

    //reward 
    Route::get('/rewards', [RewardController::class, 'getRewards']);

    
});