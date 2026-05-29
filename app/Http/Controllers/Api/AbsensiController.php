<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    private function getPhotoUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $path), '/');
        return asset('storage/' . $cleanPath);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function generateAlphaHarian()
    {
        $now = Carbon::now('Asia/Jakarta');
        $cutoff = $now->copy()->setTime(17, 0, 0);
        $startDate = Carbon::parse(Karyawan::where('status_karyawan', 'aktif')
            ->min(DB::raw('COALESCE(tanggal_masuk, created_at)')) ?? $now)
            ->startOfDay();

        $endDate = $now->copy()->startOfDay();

        // Jika BELUM jam 17:00, proses kemarin (tunggu sampai jam 17:00 hari ini)
        if ($now->lt($cutoff)) {
            $endDate = $now->copy()->subDay()->startOfDay();
        }
        // Jika SUDAH jam 17:00 atau lebih, proses hari ini

        if ($startDate->gt($endDate)) {
            return;
        }

        $karyawans = Karyawan::where('status_karyawan', 'aktif')->get();
        $alfaCount = 0;
        $cutiCount = 0;
        
        \Log::info("AutoAlpha: Processing dari {$startDate->toDateString()} to {$endDate->toDateString()} (Current time: {$now->format('Y-m-d H:i:s')})");

        for ($tanggal = $startDate->copy(); $tanggal->lte($endDate); $tanggal->addDay()) {
            if ($tanggal->isWeekend()) {
                continue;
            }

            $isLibur = DB::table('hari_libur')
                ->where('tanggal', $tanggal->toDateString())
                ->first();

            if ($isLibur) {
                continue;
            }

            foreach ($karyawans as $karyawan) {
                $tanggalMulaiAktif = Carbon::parse($karyawan->tanggal_masuk ?? $karyawan->created_at)->startOfDay();

                if ($tanggalMulaiAktif->gt($tanggal)) {
                    continue;
                }

                // Cek cuti dengan status yang lebih lengkap
                $sedangCuti = Cuti::where('id_karyawan', $karyawan->id_karyawan)
                    ->whereIn('status', ['approved', 'disetujui_hrd', 'disetujui_kabag', 'Disetujui'])
                    ->whereDate('tanggal_mulai', '<=', $tanggal->toDateString())
                    ->whereDate('tanggal_selesai', '>=', $tanggal->toDateString())
                    ->first();

                if ($sedangCuti) {
                    $cutiCount++;
                    continue;
                }

                $sudahAdaAbsensi = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                    ->whereDate('tanggal', $tanggal->toDateString())
                    ->exists();

                if ($sudahAdaAbsensi) {
                    continue;
                }

                Absensi::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal'     => $tanggal->toDateString(),
                    'status'      => 'alfa',
                ]);
                $alfaCount++;
            }
        }
        
        \Log::info("AutoAlpha: Selesai - {$alfaCount} alfa dicatat, {$cutiCount} cuti terdeteksi");
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user'   => 'required',
            'jenis'     => 'required|in:masuk,pulang',
            'latitude'  => 'required',
            'longitude' => 'required',
            'foto'      => 'required|image|max:3072',
        ]);

        try {
            $karyawan = Karyawan::where('id_user', $request->id_user)->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Karyawan tidak ditemukan!'
                ], 404);
            }

            $now = Carbon::now('Asia/Jakarta');
            $tanggalHariIni = $now->toDateString();
            $waktuSekarang = $now->toTimeString();

            // Cek hari libur nasional
            $isLibur = DB::table('hari_libur')
                ->where('tanggal', $tanggalHariIni)
                ->first();

            if ($isLibur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hari ini adalah hari libur (' . $isLibur->keterangan . '). Absensi dinonaktifkan.'
                ], 403);
            }

            // Cek weekend
            if ($now->isWeekend()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi tidak tersedia di hari libur akhir pekan.'
                ], 403);
            }

            // Cek radius kantor
            $officeSetting = DB::table('pengaturan_kantor')->first();

            if ($officeSetting) {
                $distanceKm = $this->calculateDistance(
                    $officeSetting->latitude,
                    $officeSetting->longitude,
                    $request->latitude,
                    $request->longitude
                );

                $distanceInMeters = $distanceKm * 1000;

                if ($distanceInMeters > $officeSetting->radius) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Absensi ditolak! Jarak Anda ' . round($distanceInMeters) . ' meter dari kantor (Maks ' . $officeSetting->radius . ' meter).'
                    ], 403);
                }
            }

            $id_karyawan = $karyawan->id_karyawan;

            // Cek apakah sedang cuti pada hari ini
            $sedangCuti = Cuti::where('id_karyawan', $id_karyawan)
                ->where('status', 'approved')
                ->where('tanggal_mulai', '<=', $tanggalHariIni)
                ->where('tanggal_selesai', '>=', $tanggalHariIni)
                ->first();

            if ($sedangCuti) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sedang dalam masa pengajuan (' . $sedangCuti->jenis_cuti . '). Tidak perlu melakukan presensi.'
                ], 400);
            }

            // Simpan foto
            $file = $request->file('foto');
            $namaFile = $id_karyawan . '_' . $request->jenis . '_' . time() . '.' . $file->extension();
            $pathFoto = $file->storeAs('absensi', $namaFile, 'public');

            if ($request->jenis === 'masuk') {
                if ($waktuSekarang < '06:00:00') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Belum waktunya absen. Absen masuk dimulai pukul 06:00.'
                    ], 400);
                }

                if ($waktuSekarang >= '17:00:00') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Batas waktu absen masuk telah habis (17:00). Anda tercatat Alfa.'
                    ], 400);
                }

                $cekAbsen = Absensi::where('id_karyawan', $id_karyawan)
                    ->where('tanggal', $tanggalHariIni)
                    ->first();

                if ($cekAbsen) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah melakukan presensi MASUK hari ini!'
                    ], 400);
                }

                $statusKehadiran = ($waktuSekarang > '08:15:00') ? 'terlambat' : 'hadir';

                $absensi = Absensi::create([
                    'id_karyawan'     => $id_karyawan,
                    'tanggal'         => $tanggalHariIni,
                    'jam_masuk'       => $waktuSekarang,
                    'latitude_masuk'  => $request->latitude,
                    'longitude_masuk' => $request->longitude,
                    'foto_masuk'      => $pathFoto,
                    'status'          => $statusKehadiran,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Presensi Masuk Berhasil!',
                    'data'    => $absensi
                ], 200);
            }

            if ($request->jenis === 'pulang') {
                if ($waktuSekarang < '15:00:00') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Belum waktunya pulang. Absen pulang baru bisa dilakukan pukul 15:00.'
                    ], 400);
                }

                $absensi = Absensi::where('id_karyawan', $id_karyawan)
                    ->where('tanggal', $tanggalHariIni)
                    ->first();

                if (!$absensi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda belum melakukan absen MASUK hari ini!'
                    ], 400);
                }

                if ($absensi->jam_pulang !== null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah melakukan presensi PULANG hari ini!'
                    ], 400);
                }

                $absensi->update([
                    'jam_pulang'       => $waktuSekarang,
                    'latitude_pulang'  => $request->latitude,
                    'longitude_pulang' => $request->longitude,
                    'foto_pulang'      => $pathFoto,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Presensi Pulang Berhasil!',
                    'data'    => $absensi
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Jenis absensi tidak valid.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Riwayat hanya membaca data, tidak melakukan insert alfa otomatis
    public function riwayatAbsensi(Request $request)
    {
        $request->validate([
            'id_user' => 'required'
        ]);

        $karyawan = Karyawan::where('id_user', $request->id_user)->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        }

        $tanggalMulaiAktif = Carbon::parse($karyawan->tanggal_masuk ?? $karyawan->created_at)->startOfDay();

        $riwayat = Absensi::where('id_karyawan', $karyawan->id_karyawan)
            ->whereDate('tanggal', '>=', $tanggalMulaiAktif->toDateString())
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($absen) {
                $absen->foto_masuk = $this->getPhotoUrl($absen->foto_masuk);
                $absen->foto_pulang = $this->getPhotoUrl($absen->foto_pulang);
                return $absen;
            });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat absensi',
            'data'    => $riwayat
        ], 200);
    }

    public function getConfigPresensi()
    {
        $config = DB::table('pengaturan_kantor')->first();

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi kantor belum diatur oleh admin',
                'data' => null
            ], 404);
        }

        $now = Carbon::now('Asia/Jakarta');
        $tanggalHariIni = $now->toDateString();

        $isLibur = DB::table('hari_libur')
            ->where('tanggal', $tanggalHariIni)
            ->first();

        $isWeekend = $now->isWeekend();

        $statusLibur = false;
        $pesanLibur = '';

        if ($isLibur) {
            $statusLibur = true;
            $pesanLibur = 'Hari ini adalah hari libur (' . $isLibur->keterangan . '). Presensi dinonaktifkan.';
        } elseif ($isWeekend) {
            $statusLibur = true;
            $pesanLibur = 'Presensi tidak tersedia di hari libur akhir pekan.';
        }

        $maxRadius = (double) $config->radius;
        if ($maxRadius <= 0) {
            $maxRadius = 100;
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi berhasil dimuat',
            'data' => [
                'office_lat'  => (double) $config->latitude,
                'office_lon'  => (double) $config->longitude,
                'max_radius'  => $maxRadius,
                'is_libur'    => $statusLibur,
                'pesan_libur' => $pesanLibur,
            ]
        ], 200);
    }
}