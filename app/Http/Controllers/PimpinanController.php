<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PimpinanController extends Controller
{
    public function index()
    {
        // 1. Metrik Utama
        $totalKaryawan = 40; 
        $karyawanCutiHariIni = 3; 

        // 2. Metrik Finansial (Contoh: Rp 150.000.000 diubah formatnya)
        $totalBebanGaji = number_format(150000000, 0, ',', '.'); 

        // 3. Operasional (Data kosong dulu agar masuk ke blok @empty)
        $cutiTerbaru = []; 

        // 4. Kinerja/Reward (Data kosong dulu agar masuk ke blok @empty)
        $topKaryawan = []; 

        return view('pimpinan.dashboard', compact(
            'totalKaryawan',
            'karyawanCutiHariIni',
            'totalBebanGaji',
            'cutiTerbaru',
            'topKaryawan'
        ));
    }
    public function cuti()
    {
        // Dummy data sementara sebelum menggunakan Database
        $dataCuti = [
            [
                'id' => 1,
                'nama' => 'Ahmad Rida',
                'jabatan' => 'Staff Keuangan',
                'tgl_mulai' => '2026-03-10',
                'tgl_selesai' => '2026-03-12',
                'jenis' => 'Cuti Tahunan',
                'status' => 'Pending'
            ],
            [
                'id' => 2,
                'nama' => 'Siti Aminah',
                'jabatan' => 'Staff IT',
                'tgl_mulai' => '2026-03-05',
                'tgl_selesai' => '2026-03-06',
                'jenis' => 'Cuti Sakit',
                'status' => 'Disetujui'
            ],
        ];

        return view('pimpinan.manajemen_cuti', compact('dataCuti'));
    }
    public function gaji()
    {
        // Dummy data untuk tabel gaji
        $dataGaji = [
            [
                'nama' => 'Ahmad Rida',
                'periode' => 'Maret 2026',
                'penerimaan' => 5500000,
                'potongan' => 150000,
                'gaji_bersih' => 5350000,
                'status' => 'Final'
            ],
            [
                'nama' => 'Siti Aminah',
                'periode' => 'Maret 2026',
                'penerimaan' => 4800000,
                'potongan' => 0,
                'gaji_bersih' => 4800000,
                'status' => 'Draft'
            ]
        ];

        return view('pimpinan.manajemen_gaji', compact('dataGaji'));
    }
    public function createGaji()
    {
        // Dummy daftar karyawan untuk dropdown
        $karyawan = [
            (object)['id' => 1, 'nama' => 'Ahmad Rida'],
            (object)['id' => 2, 'nama' => 'Siti Aminah'],
            (object)['id' => 3, 'nama' => 'Budi Santoso'],
        ];

        return view('pimpinan.form_gaji', compact('karyawan'));
    }
    public function reward()
    {
        // Data untuk 3 Kartu Top Performer di atas tabel
        $topKandidat = [
            (object)['nama' => 'Ahmad Rida', 'jabatan' => 'Staff Keuangan', 'skor' => 96],
            (object)['nama' => 'Siti Aminah', 'jabatan' => 'Staff IT', 'skor' => 92],
            (object)['nama' => 'Budi Santoso', 'jabatan' => 'Marketing', 'skor' => 89],
        ];

        // Data untuk isi tabel evaluasi
        $daftarReward = [
            (object)['id' => 1, 'nama' => 'Ahmad Rida', 'jabatan' => 'Staff Keuangan', 'skor' => 96, 'jenis_reward' => 'Bonus Rp 1.000.000', 'status' => 'Menunggu'],
            (object)['id' => 2, 'nama' => 'Siti Aminah', 'jabatan' => 'Staff IT', 'skor' => 92, 'jenis_reward' => 'Voucher Belanja', 'status' => 'Menunggu'],
            (object)['id' => 3, 'nama' => 'Budi Santoso', 'jabatan' => 'Marketing', 'skor' => 89, 'jenis_reward' => 'Sertifikat', 'status' => 'Disetujui'],
        ];

        return view('pimpinan.reward', compact('topKandidat', 'daftarReward'));
    }
}