<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiCutiController extends Controller
{
    /**
     * GET /api/cuti/sisa-cuti
     * Return remaining leave quota for the authenticated employee.
     */
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

    /**
     * GET /api/cuti
     * Return leave history for the authenticated employee.
     */
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

    /**
     * POST /api/cuti
     * Submit a new leave request.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        if ($karyawan->sisa_cuti <= 0) {
            return response()->json([
                'message' => 'Jatah cuti tahunan Anda telah habis.',
            ], 422);
        }

        $request->validate([
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_cuti'      => 'required|string|max:100',
            'alasan'          => 'required|string|max:1000',
            'berkas_bukti'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $jumlahHari = \Carbon\Carbon::parse($request->tanggal_mulai)
            ->diffInDays(\Carbon\Carbon::parse($request->tanggal_selesai)) + 1;

        if ($jumlahHari > $karyawan->sisa_cuti) {
            return response()->json([
                'message' => "Durasi cuti ({$jumlahHari} hari) melebihi sisa kuota cuti Anda ({$karyawan->sisa_cuti} hari).",
            ], 422);
        }

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
            'message' => 'Pengajuan cuti berhasil dikirim. Menunggu persetujuan Kepala Bagian.',
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
