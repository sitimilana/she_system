<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Cuti; // Tambahkan ini
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // Untuk catch-up tanggal alpha

class AbsensiController extends Controller
{
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

            $id_karyawan = $karyawan->id_karyawan;
            $tanggalHariIni = now()->toDateString(); // YYYY-MM-DD
            $waktuSekarang = now()->toTimeString();  // HH:MM:SS

            // ==========================================
            // CEK APAKAH KARYAWAN SEDANG CUTI HARI INI
            // ==========================================
            $sedangCuti = Cuti::where('id_karyawan', $id_karyawan)
                ->whereIn('status', ['disetujui_hrd', 'disetujui_kabag', 'approved', 'Disetujui']) // Sesuaikan dengan status ACC di sistem Anda
                ->where('tanggal_mulai', '<=', $tanggalHariIni)
                ->where('tanggal_selesai', '>=', $tanggalHariIni)
                ->first();

            if ($sedangCuti) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Anda sedang dalam masa cuti (' . $sedangCuti->jenis_cuti . '). Tidak perlu melakukan presensi.'
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

                // Jangan izinkan absen masuk jika sudah lewat jam 17:00 (Otomatis Alpha)
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

    // FUNGSI UNTUK MENGAMBIL RIWAYAT SEKALIGUS MERAPEL ALPHA
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

        // =================================================================
        // PROSES CATCH-UP (MERAPEL ALPHA OTOMATIS)
        // Kita hitung misalnya dari awal bulan (atau min 30 hari kebelakang)
        // =================================================================
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->subDay(); // Sampai H-1 (Kemarin)

        // Jika hari ini sudah lewat jam 17:00, kita include hari ini untuk dicek Alpha-nya
        if (now()->toTimeString() >= '17:00:00') {
            $endDate = Carbon::now();
        }

        while ($startDate->lte($endDate)) {
            $tanggalCek = $startDate->toDateString();

            // Jika Anda ingin melewati hari minggu, uncomment kode dibawah ini
            // if ($startDate->isSunday()) {
            //     $startDate->addDay();
            //     continue;
            // }

            // Mencegah insert dobel: Cek apakah karyawan sudah punya record/absen/alpha di tanggalCek?
            $sudahAdaAbsen = Absensi::where('id_karyawan', $id_karyawan)
                                    ->where('tanggal', $tanggalCek)
                                    ->exists();

            if (!$sudahAdaAbsen) {
                // Cek apakah karyawan sedang cuti ACC di tanggalCek?
                $sedangCuti = Cuti::where('id_karyawan', $id_karyawan)
                    ->whereIn('status', ['disetujui_hrd', 'disetujui_kabag', 'approved', 'Disetujui'])
                    ->where('tanggal_mulai', '<=', $tanggalCek)
                    ->where('tanggal_selesai', '>=', $tanggalCek)
                    ->exists();

                if ($sedangCuti) {
                    // Jika cuti, simpan sbg cuti
                    Absensi::create([
                        'id_karyawan' => $id_karyawan,
                        'tanggal'     => $tanggalCek,
                        'status'      => 'cuti'
                    ]);
                } else {
                    // Jika tidak cuti & tidak ada rekam, simpan secara Alpha otomatis
                    Absensi::create([
                        'id_karyawan' => $id_karyawan,
                        'tanggal'     => $tanggalCek,
                        'status'      => 'alfa'
                    ]);
                }
            }
            $startDate->addDay();
        }

        // =================================================================
        // BACA KEMBALI DATABASE SETELAH REKAP
        // =================================================================
        $riwayat = Absensi::where('id_karyawan', $id_karyawan)
                          ->orderBy('tanggal', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat absensi',
            'data'    => $riwayat
        ]);
    }
}