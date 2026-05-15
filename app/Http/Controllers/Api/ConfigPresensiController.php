<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengaturanKantor;
use Illuminate\Support\Facades\DB;

class ConfigPresensiController extends Controller
{
    public function __invoke()
    {
        $config = PengaturanKantor::latest('id_pengaturan')->first();

        // 1. CEK HARI LIBUR / WEEKEND
        $tanggalHariIni = now()->toDateString();
        $isLibur = DB::table('hari_libur')->where('tanggal', $tanggalHariIni)->first();
        $isWeekend = now()->isWeekend();

        $statusLibur = false;
        $pesanLibur = '';

        if ($isLibur) {
            $statusLibur = true;
            $pesanLibur = 'Hari ini adalah hari libur (' . $isLibur->keterangan . '). Presensi dinonaktifkan.';
        } elseif ($isWeekend) {
            $statusLibur = true;
            $pesanLibur = 'Presensi tidak tersedia di hari libur akhir pekan.';
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi berhasil dimuat',
            'data' => [
                'office_lat' => $config ? (double)$config->latitude : 0,
                'office_lon' => $config ? (double)$config->longitude : 0,
                'max_radius' => $config ? (double)$config->radius : 0,
                'is_libur'   => $statusLibur,   // Kirim status libur ke Android
                'pesan_libur'=> $pesanLibur     // Kirim pesan liburnya
            ]
        ]);
    }
}