<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengaturanKantor;

class ConfigPresensiController extends Controller
{
    public function __invoke()
    {
        $config = PengaturanKantor::latest('id_pengaturan')->first();

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi presensi belum diatur.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi berhasil dimuat',
            'data' => [
                'officeLat' => (float)$config->latitude,
                'officeLon' => (float)$config->longitude,
                'maxRadius' => (int)$config->radius,
            ]
        ]);
    }
}