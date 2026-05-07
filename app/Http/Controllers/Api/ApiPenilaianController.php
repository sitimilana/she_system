<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penilaian;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;

class ApiPenilaianController extends Controller
{
    public function index()
    {
        // 1. Cek token user yang sedang login di HP
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized / Sesi telah habis'
            ], 401);
        }

        // 2. Cari ID Karyawan yang terhubung dengan akun login tersebut
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Profil Karyawan tidak ditemukan.',
                'data' => []
            ], 404);
        }

        // 3. Ambil seluruh riwayat penilaian, urutkan dari tahun & bulan terbaru
        $riwayatPenilaian = Penilaian::where('id_karyawan', $karyawan->id_karyawan)
                                     ->orderBy('tahun', 'desc')
                                     ->orderBy('bulan', 'desc')
                                     ->get();

        // 4. Jika belum pernah dinilai oleh Kepala Bagian
        if ($riwayatPenilaian->isEmpty()) {
            return response()->json([
                'success' => true, // Sukses diakses, hanya saja datanya memang kosong
                'message' => 'Belum ada riwayat penilaian kinerja dari Kepala Bagian.',
                'data' => []
            ], 200);
        }

        // 5. Mapping data agar formatnya sesuai (angka murni) dengan yang diminta Retrofit Android
        $data = $riwayatPenilaian->map(function ($item) {
            return [
                'disiplin'       => (int) $item->disiplin,
                'produktivitas'  => (int) $item->produktivitas,
                'tanggung_jawab' => (int) $item->tanggung_jawab,
                'sikap_kerja'    => (int) $item->sikap_kerja,
                'loyalitas'      => (int) $item->loyalitas,
                'total_skor'     => (int) $item->total_skor, // Hasil konversi ke skala 1-100
                'bulan'          => (int) $item->bulan,
                'tahun'          => (int) $item->tahun,
            ];
        });

        // 6. Kirim JSON ke Aplikasi Android
        return response()->json([
            'success' => true,
            'message' => 'Riwayat penilaian berhasil diambil.',
            'data'    => $data
        ], 200);
    }
}