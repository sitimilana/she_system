<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KepalaBagianController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\KaryawanController;

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
    Route::post('/pimpinan/gaji', [PimpinanController::class, 'storeGaji'])->name('pimpinan.gaji.store');
    Route::get('/pimpinan/gaji/{id}/edit', [PimpinanController::class, 'editGaji'])->name('pimpinan.gaji.edit');
    Route::put('/pimpinan/gaji/{id}', [PimpinanController::class, 'updateGaji'])->name('pimpinan.gaji.update');
    Route::patch('/pimpinan/gaji/{id}/finalize', [PimpinanController::class, 'finalizeGaji'])->name('pimpinan.gaji.finalize');
    Route::delete('/pimpinan/gaji/{id}', [PimpinanController::class, 'destroyGaji'])->name('pimpinan.gaji.destroy');
    # Reward
    Route::get('/pimpinan/reward', [PimpinanController::class, 'reward'])->name('pimpinan.reward');
});

# KEPALA BAGIAN DASHBOARD
Route::middleware(['auth'])->group(function () {
    Route::get('/kepala-bagian', [KepalaBagianController::class, 'index'])->name('kabag.dashboard');
    Route::get('/kepala-bagian/karyawan', [KepalaBagianController::class, 'karyawan'])->name('kabag.karyawan');
    Route::get('/kepala-bagian/penilaian', [KepalaBagianController::class, 'penilaian'])->name('kabag.penilaian');
    #Gaji
    Route::get('/kepala-bagian/gaji', [KepalaBagianController::class, 'gaji'])->name('kabag.gaji');
    Route::get('/kepala-bagian/gaji/create', [KepalaBagianController::class, 'createGaji'])->name('kabag.gaji.create');
    Route::post('/kepala-bagian/gaji', [KepalaBagianController::class, 'storeGaji'])->name('kabag.gaji.store');
    Route::get('/kepala-bagian/gaji/{id}/edit', [KepalaBagianController::class, 'editGaji'])->name('kabag.gaji.edit');
    Route::put('/kepala-bagian/gaji/{id}', [KepalaBagianController::class, 'updateGaji'])->name('kabag.gaji.update');
    Route::delete('/kepala-bagian/gaji/{id}', [KepalaBagianController::class, 'destroyGaji'])->name('kabag.gaji.destroy');
});

# AKADEMIK DASHBOARD
Route::middleware(['auth', 'role:akademik'])->group(function () {
    Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik.beranda');
    Route::get('/akademik/absensi', [AkademikController::class, 'absensi'])->name('akademik.absensi');
    Route::get('/akademik/cuti', [AkademikController::class, 'cuti'])->name('akademik.cuti');
    Route::get('/akademik/karyawan', [AkademikController::class, 'karyawan'])->name('akademik.karyawan');
    
});

# KARYAWAN DASHBOARD
Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.dashboard');
    Route::get('/karyawan/slip-gaji', [KaryawanController::class, 'slipGaji'])->name('karyawan.slip-gaji');
    Route::get('/karyawan/slip-gaji/{id}', [KaryawanController::class, 'slipGajiDetail'])->name('karyawan.slip-gaji.show');
});


/* Default */
Route::get('/', function () {
    return redirect('/login');
});
