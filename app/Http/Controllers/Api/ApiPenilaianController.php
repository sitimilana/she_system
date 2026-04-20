<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PenilaianKinerja; // Pastikan model ini sesuai dengan nama model Anda
use Illuminate\Http\Request;

class ApiPenilaianController extends Controller
{
    /**
     * GET /api/penilaian
     * Mengambil data penilaian berdasarkan bulan & tahun yang dipilih
     */
    public function getPenilaian(Request $request)
    {
        $user = $request->user();
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Ambil filter bulan & tahun dari request Android (atau gunakan bulan saat ini sebagai default)
        $bulan = $request->query('bulan', now()->month);
        $tahun = $request->query('tahun', now()->year);

        // Asumsi tabel Anda memiliki kolom 'bulan' dan 'tahun', atau Anda bisa mem-filter dari 'tanggal_penilaian'
        $penilaian = PenilaianKinerja::where('id_karyawan', $karyawan->id_karyawan)
            ->whereMonth('tanggal_penilaian', $bulan)
            ->whereYear('tanggal_penilaian', $tahun)
            ->first();

        if (!$penilaian) {
            return response()->json([
                'success' => true, 
                'message' => 'Belum ada penilaian untuk bulan ini.',
                'data' => null
            ], 200); // 200 OK karena aplikasinya tidak error, hanya datanya saja yang belum ada
        }

        // Asumsi kolom-kolom ini ada di tabel PenilaianKinerja Anda
        return response()->json([
            'success' => true,
            'data' => [
                'disiplin' => $penilaian->disiplin ?? 0,
                'produktivitas' => $penilaian->produktivitas ?? 0,
                'tanggung_jawab' => $penilaian->tanggung_jawab ?? 0,
                'sikap_kerja' => $penilaian->sikap_kerja ?? 0,
                'loyalitas' => $penilaian->loyalitas ?? 0,
                'total_skor' => $penilaian->total_skor ?? 0,
                'bulan_tahun' => \Carbon\Carbon::parse($penilaian->tanggal_penilaian)->translatedFormat('F Y')
            ]
        ], 200);
    }
}