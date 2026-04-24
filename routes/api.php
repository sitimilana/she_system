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
// Tambahkan RewardController jika Anda sudah membuatnya
// use App\Http\Controllers\Api\RewardController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================================
// 1. ZONA BEBAS (Tidak butuh Token Sanctum dari Android)
// Sesuai ApiService: login, config-presensi, absensi, absensi/riwayat
// ==========================================================
Route::post('/login', [AuthController::class, 'login']);
Route::get('/config-presensi', [ConfigPresensiController::class, '__invoke']); // atau method-nya jika bukan invokeable

Route::post('/absensi', [AbsensiController::class, 'store']);
Route::post('/absensi/riwayat', [AbsensiController::class, 'riwayatAbsensi']);


// ==========================================================
// 2. ZONA AMAN (Wajib bawa @Header("Authorization") dari Android)
// ==========================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Cuti
    Route::get('/cuti', [PengajuanController::class, 'index']);
    Route::post('/cuti', [PengajuanController::class, 'store']);
    
    // Penilaian
    Route::get('/penilaian', [ApiPenilaianController::class, 'getPenilaian']);
    
    // Gaji (Di ApiService dipanggil 'gaji')
    Route::get('/gaji', [SlipGajiController::class, 'index']);
    
    // Reward (Sesuaikan dengan nama controller Anda)
    // Route::get('/rewards', [RewardController::class, 'index']);
});