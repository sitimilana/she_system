<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Penilaian;

class KepalaBagianController extends Controller
{
    public function index()
    {
        // jumlah karyawan
        $jumlahKaryawan = Karyawan::count();

        // data penilaian terbaru
        $penilaian = Penilaian::latest()->take(2)->get();

        // data karyawan
        $karyawan = Karyawan::latest()->take(5)->get();

        return view('kepala_bagian.home', compact(
            'jumlahKaryawan',
            'penilaian',
            'karyawan'
        ));
    }
    public function karyawan()
    {
        // Dummy data karyawan di divisi Kepala Bagian tersebut
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

        return view('kepala_bagian.kelola_karyawan', compact('dataKaryawan'));
    }

    public function penilaian()
    {
        // Dummy data karyawan di divisi Kepala Bagian (untuk dropdown)
        $karyawan = [
            (object)['id' => 1, 'nama' => 'Budi Santoso'],
            (object)['id' => 2, 'nama' => 'Siti Aminah'],
        ];

        return view('kepala_bagian.penilaian_kinerja', compact('karyawan'));
    }
}