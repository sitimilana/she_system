<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class SlipGajiController extends Controller
{
    private const BULAN_LIST = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    public function index(Request $request)
    {
        $user = $request->user();

        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.',
                'data'    => []
            ], 404);
        }

        $slipGaji = Penggajian::where('id_karyawan', $karyawan->id_karyawan)
            ->where('status_slip', 'final') // Memastikan mencari status final
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        if ($slipGaji->isEmpty()) {
            return response()->json([
                'success' => true,
                // TAMBAHAN DEBUGGING: Sistem akan memberitahu ID Karyawan yang sedang login
                'message' => 'Belum ada data slip gaji final untuk id_karyawan: ' . $karyawan->id_karyawan,
                'data'    => []
            ], 200);
        }

        // TAMBAHAN FORMATTING: Kita ubah output datanya agar persis dengan yang diharapkan Android
        $formattedData = $slipGaji->map(function($gaji) {
            return [
                'id_gaji'        => $gaji->id_gaji,
                'bulan'          => $gaji->bulan,
                'tahun'          => $gaji->tahun,
                // Menggabungkan bulan dan tahun langsung dari server
                'periode'        => (self::BULAN_LIST[$gaji->bulan] ?? $gaji->bulan) . ' ' . $gaji->tahun,
                'status'         => $gaji->status_slip,
                'status_slip'    => $gaji->status_slip,
                'total_gaji'     => (int) $gaji->total_gaji,
                'tanggal_dibuat' => $gaji->tanggal_dibuat,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat slip gaji',
            'data'    => $formattedData,
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        // Cari profil karyawan
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // CARI SLIP GAJI (Menggunakan id_gaji sesuai dengan gambar tabel database Anda)
        $slip = Penggajian::where('id_gaji', $id) 
            ->where('id_karyawan', $karyawan->id_karyawan)
            ->where('status_slip', 'final')
            ->first();

        if (!$slip) {
            return response()->json(['message' => 'Slip gaji tidak ditemukan atau belum difinalisasi.'], 404);
        }

        // KEMBALIKAN JSON SESUAI STRUKTUR ANDROID (SalaryDetailResponse)
        return response()->json([
            'id_gaji'        => $slip->id_gaji, 
            'karyawan'       => $karyawan->nama,
            'jabatan'        => $karyawan->divisi ?? '-', // Pakai divisi agar tidak error (karena Anda pakai kolom divisi saat pendaftaran)
            'periode'        => (self::BULAN_LIST[$slip->bulan] ?? $slip->bulan) . ' ' . $slip->tahun,
            'status'         => $slip->status_slip,
            'total_gaji'     => (int) $slip->total_gaji,
            'penerimaan'     => [
                'gaji_pokok'        => (int) $slip->gaji_pokok,
                'uang_makan'        => (int) $slip->uang_makan,
                'tunjangan_jabatan' => (int) $slip->tunjangan_jabatan,
                'insentif_kinerja'  => (int) $slip->insentif_kinerja,
                'tunjangan_program' => (int) $slip->tunjangan_program,
                'tunjangan_bpjs'    => (int) $slip->tunjangan_bpjs,
                'bonus'             => (int) $slip->bonus,
                'lain_lain'         => (int) $slip->lain_lain,
                'total_penerimaan'  => (int) $slip->total_penerimaan,
            ],
            'potongan'       => [
                'potongan_absen' => (int) $slip->potongan_absen,
                'cash_bon'       => (int) $slip->cash_bon,
                'cash_bon_2'     => (int) $slip->cash_bon_2,
                'potongan_bpjs'  => (int) $slip->potongan_bpjs,
                'potongan_lain'  => (int) $slip->potongan_lain,
            ],
            'tanggal_dibuat' => $slip->tanggal_dibuat,
        ]);
    }
}
