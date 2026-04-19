<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengaturanKantor;

class ConfigPresensiController extends Controller
{
    public function __invoke()
    {
        $config = PengaturanKantor::latest('id_pengaturan')->first();

        return response()->json([
            'latitude' => (float)($config->latitude ?? 0),
            'longitude' => (float)($config->longitude ?? 0),
            'radius' => (int)($config->radius ?? 100),
        ]);
    }
}
