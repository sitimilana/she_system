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

    /**
     * Get list of salary slips for the authenticated employee (mobile).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $slipGaji = Penggajian::where('id_karyawan', $karyawan->id_karyawan)
            ->where('status_slip', 'final')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get()
            ->map(function ($slip) {
                return [
                    'id_gaji'    => $slip->id_gaji,
                    'periode'    => (self::BULAN_LIST[$slip->bulan] ?? $slip->bulan) . ' ' . $slip->tahun,
                    'total_gaji' => $slip->total_gaji,
                    'status'     => $slip->status_slip,
                ];
            });

        return response()->json([
            'karyawan' => $karyawan->nama,
            'data'     => $slipGaji,
        ]);
    }

    /**
     * Get salary slip detail for the authenticated employee (mobile).
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $slip = Penggajian::where('id_gaji', $id)
            ->where('id_karyawan', $karyawan->id_karyawan)
            ->where('status_slip', 'final')
            ->first();

        if (!$slip) {
            return response()->json(['message' => 'Slip gaji tidak ditemukan.'], 404);
        }

        return response()->json([
            'id_gaji'        => $slip->id_gaji,
            'karyawan'       => $karyawan->nama,
            'jabatan'        => $karyawan->jabatan,
            'periode'        => (self::BULAN_LIST[$slip->bulan] ?? $slip->bulan) . ' ' . $slip->tahun,
            'status'         => $slip->status_slip,
            'penerimaan'     => [
                'gaji_pokok'        => $slip->gaji_pokok,
                'uang_makan'        => $slip->uang_makan,
                'tunjangan_jabatan' => $slip->tunjangan_jabatan,
                'insentif_kinerja'  => $slip->insentif_kinerja,
                'tunjangan_program' => $slip->tunjangan_program,
                'tunjangan_bpjs'    => $slip->tunjangan_bpjs,
                'bonus'             => $slip->bonus,
                'lain_lain'         => $slip->lain_lain,
                'total_penerimaan'  => $slip->total_penerimaan,
            ],
            'potongan'       => [
                'potongan_absen' => $slip->potongan_absen,
                'cash_bon'       => $slip->cash_bon,
                'potongan_bpjs'  => $slip->potongan_bpjs,
                'potongan_lain'  => $slip->potongan_lain,
            ],
            'total_gaji'     => $slip->total_gaji,
            'tanggal_dibuat' => $slip->tanggal_dibuat,
        ]);
    }
}
