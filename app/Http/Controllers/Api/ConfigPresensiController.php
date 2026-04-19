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
                'message' => 'Konfigurasi presensi belum diatur.',
            ], 404);
        }

        return response()->json([
            'latitude' => (float)$config->latitude,
            'longitude' => (float)$config->longitude,
            'radius' => (int)$config->radius,
        ]);
    }
}
