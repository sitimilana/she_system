<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AkademikController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        
        // Total seluruh karyawan
        $totalKaryawan = \App\Models\Karyawan::count();

        // 5 data cuti terbaru
        $rekapCuti = \App\Models\Cuti::with('karyawan')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get()
                            ->map(function ($cuti) {
                                return (object)[
                                    'nama' => $cuti->karyawan->nama ?? 'Tidak diketahui',
                                    'tgl_mulai' => $cuti->tanggal_mulai,
                                    'tgl_selesai' => $cuti->tanggal_selesai,
                                    'status' => $cuti->status
                                ];
                            });

        // Rekap untuk grafik absensi hari ini
        $dataAbsensiHariIni = \App\Models\Absensi::where('tanggal', $today)->get();
        
        // Karyawan hadir hari ini (gabungan dari status 'hadir' dan 'terlambat')
        $hadir = $dataAbsensiHariIni->whereIn('status', ['hadir', 'terlambat'])->count();
        $sakit = $dataAbsensiHariIni->where('status', 'sakit')->count();
        $izin = $dataAbsensiHariIni->where('status', 'izin')->count();
        
        // Jumlah karyawan yang sedang cuti hari ini 
        // (Kita ambil dari tabel Cuti agar lebih akurat jika Cron Job belum berjalan)
        $cutiToday = \App\Models\Cuti::whereIn('status', ['Disetujui', 'approved', 'disetujui_hrd'])
                            ->whereDate('tanggal_mulai', '<=', $today)
                            ->whereDate('tanggal_selesai', '>=', $today)
                            ->count();

        // ---------------------------------------------------------
        // PERUBAHAN: Menghitung Tidak Hadir (Alpha)
        // ---------------------------------------------------------
        // Kita hitung yang statusnya 'alpha' di tabel absensi hari ini
        $alphaTercatat = $dataAbsensiHariIni->where('status', 'alpha')->count();

        // Logika tampilan:
        // Jika Cron Job (jam 17:00) SUDAH berjalan, $alphaTercatat akan memiliki angka pasti.
        // Jika Cron Job BELUM berjalan (masih pagi/siang), kita gunakan rumus perkiraan seperti sebelumnya
        // agar dashboard tetap menampilkan sisa orang yang belum absen.
        $waktuSekarang = now()->toTimeString();
        if ($waktuSekarang >= '17:00:00') {
            $tidakHadir = $alphaTercatat;
        } else {
            $tidakHadir = max(0, $totalKaryawan - ($hadir + $sakit + $izin + $cutiToday));
        }

        $rekapAbsensi = [
            'Hadir' => $hadir,
            'Tidak Hadir' => $tidakHadir,
            'Sakit' => $sakit,
            'Izin' => $izin,
            'Cuti' => $cutiToday
        ];

        // Variabel $hadirHariIni saya arahkan ke $hadir agar konsisten
        return view('akademik.beranda', compact('totalKaryawan', 'hadir', 'rekapCuti', 'rekapAbsensi'));
    }
    public function absensi()
    {
        $dataAbsensi = \App\Models\Absensi::with(['karyawan.user'])->orderBy('tanggal', 'desc')->get();

        return view('akademik.riwayat_absensi', compact('dataAbsensi'));
    }
    public function cuti()
    {
        $dataCuti = \App\Models\Cuti::with(['karyawan.user'])->orderBy('tanggal_pengajuan', 'desc')->get();

        return view('akademik.riwayat_cuti', compact('dataCuti'));
    }
    public function karyawan()
    {
        // Mengambil data karyawan dari database (tabel users yang memiliki role karyawan)
        $dataKaryawan = \App\Models\User::with(['karyawan', 'role'])
            ->whereHas('role', function ($query) {
                $query->where('nama_role', 'Karyawan')->orWhere('nama_role', 'karyawan');
            })->get();

        return view('akademik.manajemen_karyawan', compact('dataKaryawan'));
    }
}