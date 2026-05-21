<?php

namespace App\Http\Controllers;

use App\Models\PengaturanKantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanLokasiController extends Controller
{
    /**
     * SHOW FORM EDIT PENGATURAN LOKASI
     */
    public function edit()
    {
        $pengaturan = PengaturanKantor::latest('id_pengaturan')->first();

        // Jika belum ada pengaturan, buat default
        if (!$pengaturan) {
            $pengaturan = PengaturanKantor::create([
                'latitude' => -7.7509239,
                'longitude' => 111.9946412,
                'radius' => 100 // Dalam METER, bukan km!
            ]);
        }

        return view('pimpinan.pengaturan_lokasi', compact('pengaturan'));
    }

    /**
     * SIMPAN/UPDATE PENGATURAN LOKASI
     * Solusi: Admin bisa update dari web form, Android langsung dapat update via API
     */
    public function update(Request $request)
    {
        // ========================================================
        // VALIDASI INPUT
        // ========================================================
        $validated = $request->validate([
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90', // Latitude harus antara -90 sampai 90
                'regex:/^-?\d+(\.\d{1,7})?$/' // Max 7 decimal places
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180', // Longitude harus antara -180 sampai 180
                'regex:/^-?\d+(\.\d{1,7})?$/' // Max 7 decimal places
            ],
            'radius' => [
                'required',
                'numeric',
                'min:1', // Minimum 1 meter
                'max:10000', // Maximum 10 km (10000 meter)
                'regex:/^\d+(\.\d{1,3})?$/' // Angka dengan max 3 decimal places
            ]
        ], [
            'latitude.required' => 'Latitude tidak boleh kosong',
            'latitude.numeric' => 'Latitude harus berupa angka',
            'latitude.between' => 'Latitude harus antara -90 dan 90',
            'longitude.required' => 'Longitude tidak boleh kosong',
            'longitude.numeric' => 'Longitude harus berupa angka',
            'longitude.between' => 'Longitude harus antara -180 dan 180',
            'radius.required' => 'Radius tidak boleh kosong',
            'radius.numeric' => 'Radius harus berupa angka',
            'radius.min' => 'Radius minimal 1 meter',
            'radius.max' => 'Radius maximal 10000 meter (10 km)',
        ]);

        try {
            // ========================================================
            // AMBIL ATAU BUAT PENGATURAN
            // ========================================================
            $pengaturan = PengaturanKantor::latest('id_pengaturan')->first();

            if (!$pengaturan) {
                // Jika belum ada, buat baru
                $pengaturan = PengaturanKantor::create([
                    'latitude' => (double) $validated['latitude'],
                    'longitude' => (double) $validated['longitude'],
                    'radius' => (double) $validated['radius'] // DISIMPAN DALAM METER!
                ]);
            } else {
                // Jika sudah ada, update
                $pengaturan->update([
                    'latitude' => (double) $validated['latitude'],
                    'longitude' => (double) $validated['longitude'],
                    'radius' => (double) $validated['radius'] // DISIMPAN DALAM METER!
                ]);
            }

            // ========================================================
            // CLEAR CACHE (OPSIONAL - Jika Anda pakai cache)
            // ========================================================
            // \Illuminate\Support\Facades\Cache::forget('pengaturan_kantor');

            return redirect()
                ->route('pimpinan.pengaturan-lokasi')
                ->with('success', 'Pengaturan lokasi kantor berhasil disimpan! ✓');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API: GET PENGATURAN (Untuk Android)
     * Endpoint yang sudah ada di ConfigPresensiController
     */
    public function getConfig()
    {
        $config = PengaturanKantor::latest('id_pengaturan')->first();

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi kantor belum diatur oleh admin',
                'data' => null
            ], 404);
        }

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

        // Pastikan radius SELALU > 0
        $maxRadius = (double) $config->radius;
        if ($maxRadius <= 0) {
            $maxRadius = 100; // Default 100 meter jika ada kesalahan
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi berhasil dimuat',
            'data' => [
                'office_lat' => (double) $config->latitude,
                'office_lon' => (double) $config->longitude,
                'max_radius' => $maxRadius,
                'is_libur' => $statusLibur,
                'pesan_libur' => $pesanLibur
            ]
        ]);
    }
}

