<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KepalaBagianController;
use App\Http\Controllers\AkademikController;

/* LOGIN */
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

# PIMPINAN DASHBOARD
Route::middleware(['auth'])->group(function () {
    Route::get('/pimpinan', [PimpinanController::class, 'index'])->name('pimpinan.dashboard');
    #Role
    Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    Route::post('/role', [RoleController::class, 'store'])->name('role.store');
    Route::delete('/role/{id}', [RoleController::class, 'destroy'])->name('role.destroy');
    #Cuti
    Route::get('/pimpinan/cuti', [PimpinanController::class, 'cuti'])->name('pimpinan.cuti');
    #Gaji
    Route::get('/pimpinan/gaji', [PimpinanController::class, 'gaji'])->name('pimpinan.gaji');
    Route::get('/pimpinan/gaji/create', [PimpinanController::class, 'createGaji'])->name('pimpinan.gaji.create');
    # Reward
    Route::get('/pimpinan/reward', [PimpinanController::class, 'reward'])->name('pimpinan.reward');
});

# KEPALA BAGIAN DASHBOARD
Route::middleware(['auth'])->group(function () {
    Route::get('/kepala-bagian', [KepalaBagianController::class, 'index'])->name('kabag.dashboard');
    Route::get('/kepala-bagian/karyawan', [KepalaBagianController::class, 'karyawan'])->name('kabag.karyawan');
    Route::get('/kepala-bagian/penilaian', [KepalaBagianController::class, 'penilaian'])->name('kabag.penilaian');
});

# AKADEMIK DASHBOARD
Route::middleware(['auth', 'role:akademik'])->group(function () {
    Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik.beranda');
    Route::get('/akademik/absensi', [AkademikController::class, 'absensi'])->name('akademik.absensi');
    Route::get('/akademik/cuti', [AkademikController::class, 'cuti'])->name('akademik.cuti');
    Route::get('/akademik/karyawan', [AkademikController::class, 'karyawan'])->name('akademik.karyawan');
    
});


/* Default */
Route::get('/', function () {
    return redirect('/login');
});