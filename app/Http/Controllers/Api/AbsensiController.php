<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Cuti; 
use Illuminate\Support\Facades\Storage;
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
        $earthRadius = 6371; // Radius bumi dalam kilometer
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

    public function store(Request $request)
    {
        // 1. Validasi Input dari Android
        $request->validate([
            'id_user'   => 'required', 
            'jenis'     => 'required|in:masuk,pulang',
            'latitude'  => 'required',
            'longitude' => 'required',
            'foto'      => 'required|image|max:3072' // Maks 3MB
        ]);

        try {
            // 2. Cari Data Karyawan berdasarkan id_user
            $karyawan = Karyawan::where('id_user', $request->id_user)->first();
            if (!$karyawan) {
                return response()->json(['success' => false, 'message' => 'Data Karyawan tidak ditemukan!'], 404);
            }

            $tanggalHariIni = now()->toDateString(); // YYYY-MM-DD
            $waktuSekarang = now()->toTimeString();  // HH:MM:SS

            // ==========================================
            // CEK HARI LIBUR NASIONAL / WEEKEND
            // ==========================================
            $isLibur = DB::table('hari_libur')->where('tanggal', $tanggalHariIni)->first();
            if ($isLibur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hari ini adalah hari libur (' . $isLibur->keterangan . '). Absensi dinonaktifkan.'
                ], 403);
            }

            if (now()->isWeekend()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi tidak tersedia di hari libur akhir pekan.'
                ], 403);
            }

            // ==========================================
            // CEK RADIUS KANTOR
            // ==========================================
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

            // ==========================================
            // CEK APAKAH KARYAWAN SEDANG CUTI HARI INI
            // ==========================================
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

            // 3. Simpan Foto ke Storage
            $file = $request->file('foto');
            $namaFile = $id_karyawan . '_' . $request->jenis . '_' . time() . '.' . $file->extension();
            $pathFoto = $file->storeAs('absensi', $namaFile, 'public');

            // ==========================================
            // LOGIKA PRESENSI MASUK
            // ==========================================
            if ($request->jenis == 'masuk') {
                
                // Jangan izinkan absen masuk jika masih terlalu pagi (misal sebelum 06:00)
                if ($waktuSekarang < '06:00:00') {
                    return response()->json(['success' => false, 'message' => 'Belum waktunya absen. Absen masuk dimulai pukul 06:00.'], 400);
                }

                // Jangan izinkan absen masuk jika sudah lewat jam 17:00 (Otomatis Alfa)
                if ($waktuSekarang >= '17:00:00') {
                    return response()->json(['success' => false, 'message' => 'Batas waktu absen masuk telah habis (17:00). Anda tercatat Alfa.'], 400);
                }

                // Cek apakah sudah absen masuk hari ini?
                $cekAbsen = Absensi::where('id_karyawan', $id_karyawan)
                                   ->where('tanggal', $tanggalHariIni)
                                   ->first();
                                   
                if ($cekAbsen) {
                    return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi MASUK hari ini!'], 400);
                }

                // Tentukan status terlambat atau tidak (Jam 08:00 + toleransi 15 menit)
                $batasToleransi = '08:15:00';
                $statusKehadiran = ($waktuSekarang > $batasToleransi) ? 'terlambat' : 'hadir';

                // Buat record absen baru
                $absensi = Absensi::create([
                    'id_karyawan'     => $id_karyawan,
                    'tanggal'         => $tanggalHariIni,
                    'jam_masuk'       => $waktuSekarang,
                    'latitude_masuk'  => $request->latitude,
                    'longitude_masuk' => $request->longitude,
                    'foto_masuk'      => $pathFoto,
                    'status'          => $statusKehadiran
                ]);

                return response()->json(['success' => true, 'message' => 'Presensi Masuk Berhasil!', 'data' => $absensi], 200);
            }

            // ==========================================
            // LOGIKA PRESENSI PULANG
            // ==========================================
            else if ($request->jenis == 'pulang') {
                
                // Cek batas waktu absen pulang (Hanya boleh jam 15:00 ke atas)
                if ($waktuSekarang < '15:00:00') {
                    return response()->json(['success' => false, 'message' => 'Belum waktunya pulang. Absen pulang baru bisa dilakukan pukul 15:00.'], 400);
                }

                // Cari absen masuk milik karyawan ini pada hari ini
                $absensi = Absensi::where('id_karyawan', $id_karyawan)
                                  ->where('tanggal', $tanggalHariIni)
                                  ->first();

                // Kalau belum absen masuk, tidak boleh absen pulang
                if (!$absensi) {
                    return response()->json(['success' => false, 'message' => 'Anda belum melakukan absen MASUK hari ini!'], 400);
                }

                // Kalau sudah absen pulang sebelumnya
                if ($absensi->jam_pulang != null) {
                    return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi PULANG hari ini!'], 400);
                }

                // Update record yang ada dengan data kepulangan
                $absensi->update([
                    'jam_pulang'       => $waktuSekarang,
                    'latitude_pulang'  => $request->latitude,
                    'longitude_pulang' => $request->longitude,
                    'foto_pulang'      => $pathFoto
                ]);

                return response()->json(['success' => true, 'message' => 'Presensi Pulang Berhasil!', 'data' => $absensi], 200);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // FUNGSI UNTUK MENGAMBIL RIWAYAT SEKALIGUS MERAPEL ALFA
    public function riwayatAbsensi(Request $request)
    {
        $request->validate([
            'id_user' => 'required'
        ]);

        $karyawan = Karyawan::where('id_user', $request->id_user)->first();
        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan'], 404);
        }

        $id_karyawan = $karyawan->id_karyawan;
        $tanggalMulaiAktif = Carbon::parse($karyawan->tanggal_masuk ?? $karyawan->created_at)->startOfDay();

        // =================================================================
        // PROSES CATCH-UP (MERAPEL ALFA OTOMATIS)
        // =================================================================
        $startDate = Carbon::now()->startOfMonth();
        if ($tanggalMulaiAktif->greaterThan($startDate)) {
            $startDate = $tanggalMulaiAktif->copy();
        }
        $endDate = Carbon::now()->subDay(); // Sampai H-1 (Kemarin)

        // Jika hari ini sudah lewat jam 17:00, kita include hari ini untuk dicek Alfa-nya
        if (now()->toTimeString() >= '17:00:00') {
            $endDate = Carbon::now();
        }

        while ($startDate->lte($endDate)) {
            $tanggalCek = $startDate->toDateString();

            // SKIP HARI LIBUR & WEEKEND
            $isLibur = DB::table('hari_libur')->where('tanggal', $tanggalCek)->exists();
            $isWeekend = ($startDate->isSaturday() || $startDate->isSunday());

            if (!$isLibur && !$isWeekend) {
                // Mencegah insert dobel
                $sudahAdaAbsen = Absensi::where('id_karyawan', $id_karyawan)
                                        ->where('tanggal', $tanggalCek)
                                        ->exists();

                if (!$sudahAdaAbsen) {
                    $sedangCuti = Cuti::where('id_karyawan', $id_karyawan)
                        ->where('status', 'approved')
                        ->where('tanggal_mulai', '<=', $tanggalCek)
                        ->where('tanggal_selesai', '>=', $tanggalCek)
                        ->first(); 

                    if ($sedangCuti) {
                        $jenisPengajuan = strtolower($sedangCuti->jenis_cuti);
                        $statusAbsen = 'cuti'; // Default

                        if (str_contains($jenisPengajuan, 'sakit')) {
                            $statusAbsen = 'sakit';
                        } elseif (str_contains($jenisPengajuan, 'izin')) {
                            $statusAbsen = 'izin';
                        }

                        Absensi::create([
                            'id_karyawan' => $id_karyawan,
                            'tanggal'     => $tanggalCek,
                            'status'      => $statusAbsen
                        ]);
                    } else {
                        // Jika tidak ada pengajuan apa-apa & bukan hari libur, simpan secara Alfa otomatis
                        Absensi::create([
                            'id_karyawan' => $id_karyawan,
                            'tanggal'     => $tanggalCek,
                            'status'      => 'alfa'
                        ]);
                    }
                }
            }
            $startDate->addDay();
        }

        $riwayat = Absensi::where('id_karyawan', $id_karyawan)
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
        ]);
    }

    public function getConfigPresensi()
    {
        $config = DB::table('pengaturan_kantor')->first();
        
        // Jika konfigurasi kantor belum ada, return error
        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi kantor belum diatur oleh admin',
                'data' => null
            ], 404);
        }

        $tanggalHariIni = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();
        $isLibur = DB::table('hari_libur')->where('tanggal', $tanggalHariIni)->first();
        $isWeekend = \Carbon\Carbon::now('Asia/Jakarta')->isWeekend();

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
        $maxRadius = (double) $config->radius;
        if ($maxRadius <= 0) {
            $maxRadius = 100; // Default 100 meter jika tidak ada atau 0
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi berhasil dimuat',
            'data' => [
                'office_lat' => (double) $config->latitude,
                'office_lon' => (double) $config->longitude,
                'max_radius' => $maxRadius,
                'is_libur'   => $statusLibur,
                'pesan_libur'=> $pesanLibur
            ]
        ]);
    }
}