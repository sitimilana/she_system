<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AkademikController extends Controller
{
    public function index()
    {
        // Dummy data sesuai kebutuhan gambar desain Anda
        $totalKaryawan = 40;
        $hadirHariIni = 35;

        $rekapCuti = [
            (object)['nama' => 'Budi Santoso', 'tgl_mulai' => '2026-03-10', 'tgl_selesai' => '2026-03-12', 'status' => 'Pending'],
            (object)['nama' => 'Siti Aminah', 'tgl_mulai' => '2026-03-15', 'tgl_selesai' => '2026-03-16', 'status' => 'Disetujui'],
        ];

        $rekapAbsensi = [
            'Hadir' => 35,
            'Tidak Hadir' => 1,
            'Sakit' => 2,
            'Izin' => 1,
            'Cuti' => 1
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