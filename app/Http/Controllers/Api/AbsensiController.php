<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input dari Android
        $request->validate([
            'id_user'   => 'required', // Android mengirim id_user dari SharedPreferences
            'jenis'     => 'required|in:masuk,pulang', // Wajib mengirim status absen
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

            // 3. Simpan Foto ke Storage
            $file = $request->file('foto');
            $namaFile = $id_karyawan . '_' . $request->jenis . '_' . time() . '.' . $file->extension();
            $pathFoto = $file->storeAs('absensi', $namaFile, 'public');

            // ==========================================
            // LOGIKA PRESENSI MASUK
            // ==========================================
            if ($request->jenis == 'masuk') {
                
                // Cek apakah sudah absen masuk hari ini?
                $cekAbsen = Absensi::where('id_karyawan', $id_karyawan)
                                   ->where('tanggal', $tanggalHariIni)
                                   ->first();
                                   
                if ($cekAbsen) {
                    return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi MASUK hari ini!'], 400);
                }

                // Buat record absen baru
                $absensi = Absensi::create([
                    'id_karyawan'     => $id_karyawan,
                    'tanggal'         => $tanggalHariIni,
                    'jam_masuk'       => $waktuSekarang,
                    'latitude_masuk'  => $request->latitude,
                    'longitude_masuk' => $request->longitude,
                    'foto_masuk'      => $pathFoto,
                    'status'          => 'Hadir'
                ]);

                return response()->json(['success' => true, 'message' => 'Presensi Masuk Berhasil!', 'data' => $absensi], 200);
            }

            // ==========================================
            // LOGIKA PRESENSI PULANG
            // ==========================================
            else if ($request->jenis == 'pulang') {
                
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
}