<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Cuti;
use Carbon\Carbon;

class AutoAlphaCommand extends Command
{
    // Nama perintah yang dipanggil nanti
    protected $signature = 'absensi:auto-alpha';
    protected $description = 'Mencatat karyawan mangkir sebagai Alpha & mencatat status Cuti ke riwayat absensi';

    public function handle()
    {
        $today = now()->toDateString();
        
        // Ambil semua karyawan aktif yang BELUM ADA di tabel absensi hari ini
        $karyawanMangkir = Karyawan::where('status_karyawan', 'aktif')
            ->whereDoesntHave('absensi', function($query) use ($today) {
                $query->where('tanggal', $today);
            })->get();

        $jumlahAlpha = 0;
        $jumlahCuti = 0;

        foreach ($karyawanMangkir as $karyawan) {
            
            // Cek apakah karyawan ini sah sedang cuti hari ini
            $cutiHariIni = Cuti::where('id_karyawan', $karyawan->id_karyawan)
                              ->whereIn('status', ['disetujui_hrd', 'disetujui_kabag', 'approved', 'Disetujui'])
                              ->where('tanggal_mulai', '<=', $today)
                              ->where('tanggal_selesai', '>=', $today)
                              ->first();

            if ($cutiHariIni) {
                // Karyawan sedang Cuti -> Masukkan ke riwayat absensi sebagai CUTI
                Absensi::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal'     => $today,
                    'status'      => 'cuti',
                    'keterangan'  => 'Sistem: Sedang cuti (' . $cutiHariIni->jenis_cuti . ')'
                ]);
                $jumlahCuti++;
            } else {
                // Karyawan tidak cuti dan tidak absen -> Masukkan sebagai ALPHA
                Absensi::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal'     => $today,
                    'status'      => 'alpha',
                    'keterangan'  => 'Sistem: Tidak absen hingga jam 17:00'
                ]);
                $jumlahAlpha++;
            }
        }

        $this->info("Rekap selesai: $jumlahAlpha Alpha dicatat, $jumlahCuti Cuti dicatat ke riwayat.");
    }
}