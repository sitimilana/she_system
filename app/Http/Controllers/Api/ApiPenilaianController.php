<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penilaian;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;
use App\Models\Reward;
use Carbon\Carbon;

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

        // 1. Skor tertinggi perusahaan bulan ini
        $topScoreBulanIni = Penilaian::where('bulan', $bulanSekarang)
                                    ->where('tahun', $tahunSekarang)
                                    ->max('total_skor');

        // 2. Penilaian milik karyawan untuk bulan ini
        $penilaianSayaBulanIni = Penilaian::where('id_karyawan', $karyawan->id_karyawan)
                                        ->where('bulan', $bulanSekarang)
                                        ->where('tahun', $tahunSekarang)
                                        ->first();

        $isTopPerformer = false;
        if ($topScoreBulanIni !== null && $penilaianSayaBulanIni !== null) {
            if ($penilaianSayaBulanIni->total_skor == $topScoreBulanIni) {
                $isTopPerformer = true;
            }
        }

        // 3. Chart (6 bulan terakhir)
        $riwayat6Bulan = Penilaian::where('id_karyawan', $karyawan->id_karyawan)
                                ->orderBy('tahun', 'desc')
                                ->orderBy('bulan', 'desc')
                                ->take(6)
                                ->get()
                                ->reverse();

        $chartData = $riwayat6Bulan->map(function ($item) {
            $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return [
                'label' => $namaBulan[$item->bulan - 1] . ' ' . substr($item->tahun, -2),
                'skor'  => (int) $item->total_skor
            ];
        })->values();

        // 4. Semua reward user (history) diambil dari Tabel Reward yang betul
        $allRewards = Reward::with('penilaian')
                        ->where('id_karyawan', $karyawan->id_karyawan)
                        ->orderBy('tanggal_reward', 'desc')
                        ->get();

        $rewardHistory = $allRewards->map(function ($item) use ($karyawan, $user) {
            // Memastikan format tarikh menjadi YYYY-MM-DD
            $tanggalPemberian = Carbon::parse($item->tanggal_reward)->toDateString();
            $expiredAt = Carbon::parse($tanggalPemberian)->addDays(7)->toDateString();

            // Tarik data skor, bulan, dan tahun dari jadual relasi Penilaian
            $skor = $item->penilaian ? (int) $item->penilaian->total_skor : 0;
            $bulan = $item->penilaian ? (int) $item->penilaian->bulan : (int) Carbon::parse($tanggalPemberian)->month;
            $tahun = $item->penilaian ? (int) $item->penilaian->tahun : (int) Carbon::parse($tanggalPemberian)->year;

            return [
                'id'         => (int) $item->id_reward,
                'nama'       => $karyawan->nama ?? $user->name ?? 'Karyawan',
                'skor'       => $skor,
                'alasan'     => $item->keterangan ?? "Apresiasi kinerja bulan ke-" . $bulan,
                'tanggal'    => $tanggalPemberian,
                'expired_at' => $expiredAt,
                'bulan'      => $bulan,
                'tahun'      => $tahun
            ];
        })->values();

        // 5. recent_rewards: reward dengan tarikh pemberian dalam 7 hari terakhir
        $recentRewards = $rewardHistory->filter(function ($r) {
            try {
                return Carbon::parse($r['tanggal'])->greaterThanOrEqualTo(Carbon::now()->subDays(7));
            } catch (\Exception $ex) {
                return false;
            }
        })->values();

        $latestRecentReward = null;
        if ($recentRewards->isNotEmpty()) {
            // Susun dan ambil yang paling baru
            $latestRecentReward = $recentRewards->sortByDesc('tanggal')->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard reward berhasil diambil',
            'data'    => [
                'is_top_performer_bulan_ini' => $isTopPerformer,
                'skor_bulan_ini'             => $penilaianSayaBulanIni ? (int) $penilaianSayaBulanIni->total_skor : 0,
                'chart_data'                 => $chartData,
                'reward_history'             => $rewardHistory,
                'recent_rewards'             => $recentRewards,
                'latest_recent_reward'       => $latestRecentReward
            ]
        ], 200);
    }
}