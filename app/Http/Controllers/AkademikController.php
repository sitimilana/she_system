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

        // Karyawan hadir hari ini (asumsi: ada jam masuk)
        $hadirHariIni = \App\Models\Absensi::where('tanggal', $today)
                            ->whereNotNull('jam_masuk')
                            ->count();

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
        
        $hadir = $dataAbsensiHariIni->whereIn('status', ['hadir', 'terlambat'])->count();
        $sakit = $dataAbsensiHariIni->where('status', 'sakit')->count();
        $izin = $dataAbsensiHariIni->where('status', 'izin')->count();
        
        // Jumlah karyawan yang sedang cuti hari ini
        $cutiToday = \App\Models\Cuti::whereIn('status', ['Disetujui', 'approved'])
                            ->whereDate('tanggal_mulai', '<=', $today)
                            ->whereDate('tanggal_selesai', '>=', $today)
                            ->count();

        // Tidak hadir = sisa dari total (jika kurang dari 0 anggap 0)
        $tidakHadir = max(0, $totalKaryawan - ($hadir + $sakit + $izin + $cutiToday));

        $rekapAbsensi = [
            'Hadir' => $hadir,
            'Tidak Hadir' => $tidakHadir,
            'Sakit' => $sakit,
            'Izin' => $izin,
            'Cuti' => $cutiToday
        ];

        return view('akademik.beranda', compact('totalKaryawan', 'hadirHariIni', 'rekapCuti', 'rekapAbsensi'));
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