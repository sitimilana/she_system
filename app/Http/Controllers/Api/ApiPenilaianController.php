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

    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $karyawan = Karyawan::where('id_user', $user->id_user)->first();
        if (!$karyawan) return response()->json(['success' => false, 'message' => 'Profil Karyawan tidak ditemukan.'], 404);

        $bulanSekarang = now()->month;
        $tahunSekarang = now()->year;

        // 1. Cek Skor Tertinggi (Rank 1) di Perusahaan pada bulan ini
        $topScoreBulanIni = Penilaian::where('bulan', $bulanSekarang)
                                     ->where('tahun', $tahunSekarang)
                                     ->max('total_skor');

        // 2. Ambil Penilaian milik Karyawan ini pada bulan ini
        $penilaianSayaBulanIni = Penilaian::where('id_karyawan', $karyawan->id_karyawan)
                                          ->where('bulan', $bulanSekarang)
                                          ->where('tahun', $tahunSekarang)
                                          ->first();

        // 3. Tentukan apakah Karyawan ini adalah Penerima Reward (Top 1)
        $isTopPerformer = false;
        if ($topScoreBulanIni !== null && $penilaianSayaBulanIni !== null) {
            // (Opsional: Jika ada tie-breaker, logika disamakan dengan controller pimpinan)
            if ($penilaianSayaBulanIni->total_skor == $topScoreBulanIni) {
                // Sederhananya, jika nilai dia adalah nilai tertinggi, dia dapat reward.
                $isTopPerformer = true;
            }
        }

        // 4. Ambil 6 bulan terakhir untuk Data Chart Kinerja
        $riwayat6Bulan = Penilaian::where('id_karyawan', $karyawan->id_karyawan)
                                  ->orderBy('tahun', 'desc')
                                  ->orderBy('bulan', 'desc')
                                  ->take(6)
                                  ->get()
                                  ->reverse(); // Balik urutan agar dari bulan terlama ke terbaru (untuk chart)

        $chartData = $riwayat6Bulan->map(function ($item) {
            $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return [
                'label' => $namaBulan[$item->bulan - 1] . ' ' . substr($item->tahun, -2), // Contoh: "Mei 26"
                'skor'  => (int) $item->total_skor
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard reward berhasil diambil',
            'data'    => [
                'is_top_performer_bulan_ini' => $isTopPerformer,
                'skor_bulan_ini'             => $penilaianSayaBulanIni ? (int) $penilaianSayaBulanIni->total_skor : 0,
                'chart_data'                 => $chartData
            ]
        ], 200);
    }
}