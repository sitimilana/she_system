<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ApiCutiController extends Controller
{
    public function sisaCuti(Request $request)
    {
        $user = $request->user();
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        return response()->json([
            'nama'      => $karyawan->nama,
            'sisa_cuti' => $karyawan->sisa_cuti,
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $dataCuti = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->orderByDesc('tanggal_pengajuan')
            ->get()
            ->map(function ($cuti) {
                return [
                    'id_cuti'          => $cuti->id_cuti,
                    'tanggal_mulai'    => $cuti->tanggal_mulai,
                    'tanggal_selesai'  => $cuti->tanggal_selesai,
                    'jenis_cuti'       => $cuti->jenis_cuti,
                    'alasan'           => $cuti->alasan,
                    'status'           => $cuti->status,
                    'tanggal_pengajuan'=> $cuti->tanggal_pengajuan,
                ];
            });

        return response()->json([
            'karyawan'  => $karyawan->nama,
            'sisa_cuti' => $karyawan->sisa_cuti,
            'data'      => $dataCuti,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Validasi dasar (hapus aturan after_or_equal:today agar kita bisa atur manual)
        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_cuti'      => 'required|string|max:100',
            'alasan'          => 'required|string|max:1000',
            'berkas_bukti'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        // ==========================================
        // LOGIKA ATURAN HARI (H-1 atau Hari H)
        // ==========================================
        $tglMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $hariIni = now()->startOfDay();

        if (strtolower($request->jenis_cuti) === 'sakit') {
            // Jika SAKIT, boleh hari ini (tapi tidak boleh masa lalu)
            if ($tglMulai->lessThan($hariIni)) {
                return response()->json([
                    'message' => 'Tanggal mulai sakit tidak boleh di masa lalu.'
                ], 422);
            }
        } else {
            // Jika CUTI/IZIN dll, HARUS minimal besok (H-1)
            if ($tglMulai->lessThanOrEqualTo($hariIni)) {
                return response()->json([
                    'message' => 'Pengajuan ' . $request->jenis_cuti . ' harus dilakukan minimal 1 hari sebelumnya (H-1).'
                ], 422);
            }
        }

        // ==========================================
        // LOGIKA SISA CUTI
        // ==========================================
        $jumlahHari = $tglMulai->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;

        // Asumsi: Cuti mengurangi sisa cuti. (Opsional: Anda bisa mengecualikan 'Sakit' agar tidak memotong sisa cuti tahunan jika aturan perusahaan Anda begitu).
        if ($karyawan->sisa_cuti <= 0) {
            return response()->json([
                'message' => 'Jatah cuti tahunan Anda telah habis.',
            ], 422);
        }

        if ($jumlahHari > $karyawan->sisa_cuti) {
            return response()->json([
                'message' => "Durasi pengajuan ({$jumlahHari} hari) melebihi sisa kuota cuti Anda ({$karyawan->sisa_cuti} hari).",
            ], 422);
        }

        // Proses Simpan
        $berkasPath = null;
        if ($request->hasFile('berkas_bukti')) {
            $berkasPath = $request->file('berkas_bukti')->store('berkas_cuti', 'public');
        }

        $cuti = Cuti::create([
            'id_karyawan'      => $karyawan->id_karyawan,
            'tanggal_pengajuan'=> now()->toDateString(),
            'tanggal_mulai'    => $request->tanggal_mulai,
            'tanggal_selesai'  => $request->tanggal_selesai,
            'jenis_cuti'       => $request->jenis_cuti,
            'alasan'           => $request->alasan,
            'berkas_bukti'     => $berkasPath,
            'status'           => 'pending_kabag',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil dikirim. Menunggu persetujuan.',
            'data'    => [
                'id_cuti'          => $cuti->id_cuti,
                'tanggal_mulai'    => $cuti->tanggal_mulai,
                'tanggal_selesai'  => $cuti->tanggal_selesai,
                'jenis_cuti'       => $cuti->jenis_cuti,
                'status'           => $cuti->status,
            ],
        ], 201);
    }
}