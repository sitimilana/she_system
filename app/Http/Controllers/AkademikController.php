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
        // Dummy data menyesuaikan struktur database (Join tabel karyawan dan absensi)
        $dataAbsensi = [
            (object)[
                'nama_lengkap' => 'Budi Santoso', // Didapat dari join tabel karyawan
                'tanggal' => '2026-03-10',
                'jam_masuk' => '07:45:00',
                'jam_pulang' => '17:05:00',
                'latitude_masuk' => '-8.064512',
                'longitude_masuk' => '111.714523',
                'latitude_pulang' => '-8.064512',
                'longitude_pulang' => '111.714523',
                'foto_masuk' => 'https://via.placeholder.com/150', // Dummy url foto
                'foto_pulang' => 'https://via.placeholder.com/150',
                'status' => 'hadir'
            ],
            (object)[
                'nama_lengkap' => 'Siti Aminah',
                'tanggal' => '2026-03-10',
                'jam_masuk' => '08:15:00',
                'jam_pulang' => '17:00:00',
                'latitude_masuk' => '-8.065111',
                'longitude_masuk' => '111.715222',
                'latitude_pulang' => '-8.065111',
                'longitude_pulang' => '111.715222',
                'foto_masuk' => 'https://via.placeholder.com/150',
                'foto_pulang' => 'https://via.placeholder.com/150',
                'status' => 'terlambat'
            ],
            (object)[
                'nama_lengkap' => 'Wahyu',
                'tanggal' => '2026-03-10',
                'jam_masuk' => null,
                'jam_pulang' => null,
                'latitude_masuk' => null,
                'longitude_masuk' => null,
                'latitude_pulang' => null,
                'longitude_pulang' => null,
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'alfa'
            ],
        ];

        return view('akademik.riwayat_absensi', compact('dataAbsensi'));
    }
    public function cuti()
    {
        // Dummy data menyesuaikan struktur tabel cuti yang di-join dengan tabel karyawan
        $dataCuti = [
            (object)[
                'nama_karyawan' => 'Budi Santoso',
                'tanggal_pengajuan' => '2026-03-05',
                'tanggal_mulai' => '2026-03-10',
                'tanggal_selesai' => '2026-03-12',
                'alasan' => 'Menghadiri acara pernikahan keluarga di luar kota.',
                'berkas_bukti' => 'https://via.placeholder.com/150', // Anggap ini URL gambar/PDF
                'status' => 'menunggu'
            ],
            (object)[
                'nama_karyawan' => 'Siti Aminah',
                'tanggal_pengajuan' => '2026-02-20',
                'tanggal_mulai' => '2026-02-25',
                'tanggal_selesai' => '2026-02-27',
                'alasan' => 'Sakit tipes, rawat inap di RS. Surat dokter terlampir.',
                'berkas_bukti' => 'https://via.placeholder.com/150',
                'status' => 'disetujui'
            ],
            (object)[
                'nama_karyawan' => 'Wahyu',
                'tanggal_pengajuan' => '2026-03-01',
                'tanggal_mulai' => '2026-03-05',
                'tanggal_selesai' => '2026-03-05',
                'alasan' => 'Ada urusan pribadi mendadak.',
                'berkas_bukti' => null, // Kadang karyawan lupa melampirkan bukti
                'status' => 'ditolak'
            ],
        ];

        return view('akademik.riwayat_cuti', compact('dataCuti'));
    }
    public function karyawan()
    {
        // Dummy data karyawan untuk ditampilkan di Akademik (Sama dengan data Kabag)
        $dataKaryawan = [
            (object)[
                'id' => 1, 
                'nama' => 'Budi Santoso', 
                'jabatan' => 'Staff Operasional', 
                'kontak' => '08123456789', 
                'alamat' => 'Jl. Merdeka No. 10', 
                'status_kerja' => 'Aktif'
            ],
            (object)[
                'id' => 2, 
                'nama' => 'Siti Aminah', 
                'jabatan' => 'Staff Admin', 
                'kontak' => '08987654321', 
                'alamat' => 'Jl. Sudirman No. 5', 
                'status_kerja' => 'Cuti'
            ],
        ];

        return view('akademik.manajemen_karyawan', compact('dataKaryawan'));
    }
}