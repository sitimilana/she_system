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

        // Jika konfigurasi kantor belum ada, return error
        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi kantor belum diatur oleh admin',
                'data' => null
            ], 404);
        }

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

        // Pastikan radius/max_radius tidak bernilai 0, default ke 100 jika kosong atau 0
        $maxRadius = (double)$config->radius;
        if ($maxRadius <= 0) {
            $maxRadius = 100; // Default 100 meter jika tidak ada atau 0
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi berhasil dimuat',
            'data' => [
                'office_lat' => (double)$config->latitude,
                'office_lon' => (double)$config->longitude,
                'max_radius' => $maxRadius,
                'is_libur'   => $statusLibur,   // Kirim status libur ke Android
                'pesan_libur'=> $pesanLibur     // Kirim pesan liburnya
            ]
        ]);
    }
}