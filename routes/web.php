<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KepalaBagianController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PimpinanCutiController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\PengaturanLokasiController;
use App\Http\Controllers\PenggajianController;

/* LOGIN */
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

# PIMPINAN DASHBOARD
Route::middleware(['auth'])->group(function () {
    Route::get('/pimpinan', [PimpinanController::class, 'index'])->name('pimpinan.dashboard');
    #Persetujuan Karyawan Baru
    Route::get('/pimpinan/karyawan-pending', [PimpinanController::class, 'karyawanPending'])->name('pimpinan.karyawan_pending');
    Route::post('/pimpinan/karyawan-pending/{id}/approve', [PimpinanController::class, 'approveKaryawan'])->name('pimpinan.karyawan_approve');
    Route::delete('/pimpinan/karyawan-pending/{id}/reject', [PimpinanController::class, 'rejectKaryawan'])->name('pimpinan.rejectKaryawan');
    #Cuti
    Route::get('/pimpinan/cuti', [PimpinanCutiController::class, 'indexPimpinan'])->name('pimpinan.cuti');
    Route::post('/pimpinan/cuti/{id}/approve', [PimpinanCutiController::class, 'approve'])->name('pimpinan.cuti.approve');
    Route::post('/pimpinan/cuti/{id}/reject', [PimpinanCutiController::class, 'reject'])->name('pimpinan.cuti.reject');
    
    #Gaji
    Route::get('/pimpinan/gaji', [PenggajianController::class, 'gaji'])
        ->name('pimpinan.gaji');

    Route::get('/pimpinan/gaji/create', [PenggajianController::class, 'createGaji'])
        ->name('pimpinan.gaji.create');

    Route::post('/pimpinan/gaji', [PenggajianController::class, 'storeGaji'])
        ->name('pimpinan.gaji.store');

    Route::get('/pimpinan/gaji/{id}/edit', [PenggajianController::class, 'editGaji'])
        ->name('pimpinan.gaji.edit');

    Route::put('/pimpinan/gaji/{id}', [PenggajianController::class, 'updateGaji'])
        ->name('pimpinan.gaji.update');

    Route::patch('/pimpinan/gaji/{id}/finalize', [PenggajianController::class, 'finalizeGaji'])
        ->name('pimpinan.gaji.finalize');

    Route::delete('/pimpinan/gaji/{id}', [PenggajianController::class, 'destroyGaji'])
        ->name('pimpinan.gaji.destroy');

    Route::get('/pimpinan/karyawan/{id}/finansial',[PenggajianController::class, 'getKaryawanFinansial'])
        ->name('pimpinan.karyawan.finansial');
        
    # Reward
    Route::get('/pimpinan/reward', [RewardController::class, 'index'])
        ->name('pimpinan.reward');

    Route::post('/pimpinan/reward/store', [RewardController::class, 'store'])
        ->name('pimpinan.reward.store');
    # Pengaturan Lokasi Presensi
    Route::get('/pimpinan/pengaturan-lokasi', [PengaturanLokasiController::class, 'edit'])
        ->name('pimpinan.pengaturan-lokasi');
        
    Route::put('/pimpinan/pengaturan-lokasi', [PengaturanLokasiController::class, 'update'])
        ->name('pimpinan.pengaturan-lokasi.update');

    Route::delete('/pimpinan/karyawan-pending/{id}/reject', [PimpinanController::class, 'rejectKaryawan'])
        ->name('pimpinan.rejectKaryawan');

    Route::put('/pimpinan/karyawan-pending/{id}/approve', [PimpinanController::class, 'approveKaryawan'])
        ->name('pimpinan.approveKaryawan');


    //Lihat Hari Libur
    Route::get('/pimpinan/hari-libur', [PimpinanController::class, 'hariLibur'])->name('pimpinan.hari_libur');
});

# KEPALA BAGIAN DASHBOARD
Route::middleware(['auth'])->group(function () {
    Route::get('/kepala-bagian', [KepalaBagianController::class, 'index'])
        ->name('kabag.dashboard');

    Route::get('/kepala-bagian/karyawan', [KepalaBagianController::class, 'karyawan'])
        ->name('kabag.karyawan');

    Route::get('/kepala-bagian/penilaian', [KepalaBagianController::class, 'penilaian'])
        ->name('kabag.penilaian');

    Route::post('/kepala-bagian/penilaian', [KepalaBagianController::class, 'storePenilaian'])
        ->name('kabag.penilaian.store');

    Route::get('/kepala-bagian/penilaian/{id}', [KepalaBagianController::class, 'showPenilaian'])
        ->name('kabag.penilaian.show');

    Route::put('/kepala-bagian/penilaian/{id}', [KepalaBagianController::class, 'updatePenilaian'])
        ->name('kabag.penilaian.update');

    Route::delete('/kepala-bagian/penilaian/{id}', [KepalaBagianController::class, 'destroyPenilaian'])
        ->name('kabag.penilaian.destroy');

    #Kelola Karyawan
    Route::post('/kepala-bagian/karyawan', [KepalaBagianController::class, 'store'])
        ->name('kabag.karyawan.store_baru');

    Route::post('/kabag/karyawan/store', [App\Http\Controllers\KepalaBagianController::class, 'store'])
        ->name('kabag.karyawan.store');
    
    Route::put('/kabag/karyawan/{id}', [App\Http\Controllers\KepalaBagianController::class, 'updateKaryawan'])
        ->name('kabag.karyawan.update');

    Route::delete('/kabag/karyawan/{id}', [App\Http\Controllers\KepalaBagianController::class, 'destroyKaryawan'])
    ->name('kabag.karyawan.destroy');

    #Cuti
    Route::get('/kepala-bagian/cuti', [KepalaBagianController::class, 'cuti'])
        ->name('kabag.cuti');

    Route::patch('/kepala-bagian/cuti/{id}/approve', [KepalaBagianController::class, 'approveCuti'])
        ->name('kabag.cuti.approve');

    Route::patch('/kepala-bagian/cuti/{id}/reject', [KepalaBagianController::class, 'rejectCuti'])
        ->name('kabag.cuti.reject');

    # Cetak Karyawan
    Route::get('/kabag/karyawan/cetak', [KepalaBagianController::class, 'cetakKaryawan'])
        ->name('kabag.karyawan.cetak');

    # Reset Password Karyawan
    Route::post('/kabag/karyawan/{id}/reset-password', [App\Http\Controllers\KepalaBagianController::class, 'resetPassword'])
        ->name('kabag.karyawan.reset_password');

    // Riwayat Absensi & Cuti (Kepala Bagian)
    Route::get('/kabag/riwayat-absensi', [App\Http\Controllers\KepalaBagianController::class, 'riwayatAbsensi'])->name('kabag.riwayat_absensi');
    
    Route::get('/kabag/riwayat-cuti', [App\Http\Controllers\KepalaBagianController::class, 'riwayatCuti'])->name('kabag.riwayat_cuti');

});

# AKADEMIK DASHBOARD
Route::middleware(['auth', 'role:akademik'])->group(function () {
    Route::get('/akademik', [AkademikController::class, 'index'])
        ->name('akademik.beranda');

    Route::get('/akademik/absensi', [AkademikController::class, 'absensi'])
        ->name('akademik.absensi');

    Route::get('/akademik/cuti', [AkademikController::class, 'cuti'])
        ->name('akademik.cuti');

    Route::get('/akademik/karyawan', [AkademikController::class, 'karyawan'])
        ->name('akademik.karyawan');

    Route::get('/akademik/absensi/cetak', [AkademikController::class, 'cetakAbsensi'])
        ->name('akademik.cetak.absensi');

    Route::get('/akademik/cuti/cetak', [AkademikController::class, 'cetakCuti'])
        ->name('akademik.cetak.cuti');

    Route::get('/akademik/karyawan', [App\Http\Controllers\AkademikController::class, 'karyawan'])
        ->name('akademik.karyawan');

    Route::get('/akademik/karyawan/cetak', [App\Http\Controllers\AkademikController::class, 'cetakKaryawan'])
    ->name('akademik.cetak_karyawan');

    // Hari Libur
    Route::get('/akademik/hari-libur', [AkademikController::class, 'hariLibur'])->name('akademik.hari_libur');
    Route::post('/akademik/hari-libur', [AkademikController::class, 'storeHariLibur'])->name('akademik.store_hari_libur');
    Route::delete('/akademik/hari-libur/{id}', [AkademikController::class, 'destroyHariLibur'])->name('akademik.destroy_hari_libur');
    
});

# KARYAWAN DASHBOARD
Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan', [KaryawanController::class, 'index'])
        ->name('karyawan.dashboard');

    Route::get('/karyawan/slip-gaji', [KaryawanController::class, 'slipGaji'])
        ->name('karyawan.slip-gaji');

    Route::get('/karyawan/slip-gaji/{id}', [KaryawanController::class, 'slipGajiDetail'])
        ->name('karyawan.slip-gaji.show');
});


/* Default */
Route::get('/', function () {
    return redirect('/login');
});
