<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    private function isAnnualLeave(string $jenisCuti): bool
    {
        $jenisCuti = strtolower(trim($jenisCuti));

        return in_array($jenisCuti, ['cuti', 'cuti tahunan'], true);
    }

    private function getAnnualLeaveRemaining(Karyawan $karyawan): int
    {
        $usedDays = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->whereIn('status', ['approved', 'Disetujui'])
            ->where(function ($query) {
                $query->whereRaw('LOWER(jenis_cuti) = ?', ['cuti'])
                    ->orWhereRaw('LOWER(jenis_cuti) = ?', ['cuti tahunan']);
            })
            ->whereYear('tanggal_mulai', now()->year)
            ->get()
            ->sum(function ($cuti) {
                $mulai = Carbon::parse($cuti->tanggal_mulai);
                $selesai = Carbon::parse($cuti->tanggal_selesai);

                return $mulai->diffInDays($selesai) + 1;
            });

        return max(0, 12 - (int) $usedDays);
    }

    private function hasAnnualLeaveInMonth(Karyawan $karyawan, string $periode, ?int $ignoreId = null): bool
    {
        [$tahun, $bulan] = array_map('intval', explode('-', $periode));

        $cutiQuery = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->whereIn('status', [
                'pending',
                'pending_pimpinan',
                'pending_kabag',
                'approved',
                'Disetujui'
            ])
            ->where(function ($query) {
                $query->whereRaw('LOWER(jenis_cuti) = ?', ['cuti'])
                      ->orWhereRaw('LOWER(jenis_cuti) = ?', ['cuti tahunan']);
            })
            ->where(function ($query) use ($tahun, $bulan) {
                $query->whereYear('tanggal_mulai', $tahun)
                    ->whereMonth('tanggal_mulai', $bulan)
                    ->orWhere(function ($subQuery) use ($tahun, $bulan) {
                        $subQuery->whereYear('tanggal_selesai', $tahun)
                            ->whereMonth('tanggal_selesai', $bulan);
                    });
            });

        if ($ignoreId !== null) {
            $cutiQuery->where('id_cuti', '!=', $ignoreId);
        }

        return $cutiQuery->exists();
    }

    public function sisaCuti(Request $request)
    {
        $user = $request->user();

        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.'
            ], 404);
        }

        // =========================================================
        // TAMBAHAN: Total jatah cuti per tahun
        // =========================================================
        $totalCutiTahunIni = 12;

        // =========================================================
        // TAMBAHAN: Hitung cuti yang sudah digunakan
        // =========================================================
        $cutiDigunakan = $totalCutiTahunIni - $this->getAnnualLeaveRemaining($karyawan);

        // =========================================================
        // TAMBAHAN: Hitung cuti pending
        // =========================================================
        $cutiPending = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->whereIn('status', [
                'pending',
                'pending_pimpinan',
                'pending_kabag'
            ])
            ->count();

        // =========================================================
        // TAMBAHAN: Hitung cuti approved
        // =========================================================
        $cutiApproved = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->whereIn('status', ['approved', 'Disetujui'])
            ->count();

        // =========================================================
        // TAMBAHAN: Hitung sisa cuti bulan ini
        // =========================================================
        $bulanSekarang = now()->month;
        $tahunSekarang = now()->year;

        $cutiBulanIni = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->whereIn('status', [
                'pending',
                'pending_pimpinan',
                'pending_kabag',
                'approved',
                'Disetujui'
            ])
            ->where(function ($query) {
                $query->whereRaw('LOWER(jenis_cuti) = ?', ['cuti'])
                    ->orWhereRaw('LOWER(jenis_cuti) = ?', ['cuti tahunan']);
            })
            ->whereMonth('tanggal_mulai', $bulanSekarang)
            ->whereYear('tanggal_mulai', $tahunSekarang)
            ->count();

        $sisaCutiBulanIni = max(0, 1 - $cutiBulanIni);

        // =========================================================
        // RESPONSE BARU SESUAI ANDROID
        // =========================================================
        return response()->json([
            'success' => true,
            'message' => 'Sisa cuti berhasil diambil',
            'data' => [
                'nama' => $karyawan->nama,

                'total_cuti_tahun_ini' => $totalCutiTahunIni,

                'cuti_digunakan' => $cutiDigunakan,

                'sisa_cuti_tahun_ini' => $this->getAnnualLeaveRemaining($karyawan),

                'cuti_pending' => $cutiPending,

                'cuti_approved' => $cutiApproved,

                'sisa_cuti_bulan_ini' => $sisaCutiBulanIni,
            ]
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json([
                'message' => 'Data karyawan tidak ditemukan.'
            ], 404);
        }

        $dataCuti = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->orderByDesc('tanggal_pengajuan')
            ->get()
            ->map(function ($cuti) {
                return [
                    'id_cuti'           => $cuti->id_cuti,
                    'tanggal_mulai'     => $cuti->tanggal_mulai,
                    'tanggal_selesai'   => $cuti->tanggal_selesai,
                    'jenis_cuti'        => $cuti->jenis_cuti,
                    'alasan'            => $cuti->alasan,
                    'status'            => $cuti->status,
                    'tanggal_pengajuan' => $cuti->tanggal_pengajuan,

                    /*
                    |--------------------------------------------------------------------------
                    | TAMBAHAN DARI ANDROID
                    |--------------------------------------------------------------------------
                    | Menambahkan keterangan pimpinan pada riwayat cuti
                    |--------------------------------------------------------------------------
                    */
                    'keterangan_pimpinan' => $cuti->keterangan_pimpinan,
                    'berkas_bukti' => $cuti->berkas_bukti ? asset('storage/' . $cuti->berkas_bukti) : null,
                ];
            });

        return response()->json([
            'karyawan'  => $karyawan->nama,
            'sisa_cuti' => $this->getAnnualLeaveRemaining($karyawan),
            'data'      => $dataCuti,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json([
                'message' => 'Data karyawan tidak ditemukan.'
            ], 404);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_cuti'      => 'required|string|max:100',
            'alasan'          => 'required|string|max:1000',
            'berkas_bukti'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $jenisCuti = strtolower(trim($request->jenis_cuti));

        if ($jenisCuti === 'sakit' && !$request->hasFile('berkas_bukti')) {
            return response()->json([
                'message' => 'Pengajuan sakit wajib melampirkan surat dokter atau bukti sakit.'
            ], 422);
        }

        $tglMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $tglSelesai = Carbon::parse($request->tanggal_selesai)->startOfDay();
        $hariIni = now()->startOfDay();

        $isCutiTahunan = $this->isAnnualLeave($jenisCuti);

        if ($isCutiTahunan) {

            if ($tglMulai->lessThanOrEqualTo($hariIni)) {
                return response()->json([
                    'message' => 'Pengajuan cuti harus dilakukan minimal H-1.'
                ], 422);
            }

            if ($request->tanggal_mulai !== $request->tanggal_selesai) {
                return response()->json([
                    'message' => 'Cuti tahunan hanya boleh 1 hari.'
                ], 422);
            }

            $periodePengajuan = $tglMulai->format('Y-m');

            if ($this->hasAnnualLeaveInMonth($karyawan, $periodePengajuan)) {
                return response()->json([
                    'message' => 'Dalam 1 bulan hanya boleh mengajukan 1 hari cuti.'
                ], 422);
            }

            if ($this->getAnnualLeaveRemaining($karyawan) <= 0) {
                return response()->json([
                    'message' => 'Jatah cuti tahunan Anda telah habis.'
                ], 422);
            }

        } elseif (in_array($jenisCuti, ['izin', 'sakit'])) {

            if ($tglMulai->lessThan($hariIni)) {
                return response()->json([
                    'message' => 'Tanggal pengajuan tidak boleh di masa lalu.'
                ], 422);
            }
        }

        $izinBentrok = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($request) {

                $query->whereBetween('tanggal_mulai', [
                    $request->tanggal_mulai,
                    $request->tanggal_selesai
                ])

                ->orWhereBetween('tanggal_selesai', [
                    $request->tanggal_mulai,
                    $request->tanggal_selesai
                ])

                ->orWhere(function ($q) use ($request) {
                    $q->where('tanggal_mulai', '<=', $request->tanggal_mulai)
                      ->where('tanggal_selesai', '>=', $request->tanggal_selesai);
                });

            })->exists();

        if ($izinBentrok) {
            return response()->json([
                'message' => 'Anda sudah memiliki pengajuan pada tanggal tersebut.'
            ], 422);
        }

        $sudahAbsen = \App\Models\Absensi::where('id_karyawan', $karyawan->id_karyawan)
            ->whereBetween('tanggal', [
                $request->tanggal_mulai,
                $request->tanggal_selesai
            ])
            ->whereNotNull('jam_masuk')
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'message' => 'Anda sudah melakukan presensi pada tanggal tersebut.'
            ], 422);
        }

        $jumlahHari = $tglMulai->diffInDays($tglSelesai) + 1;

        if ($isCutiTahunan) {

            // CODE LAMA TETAP DIPERTAHANKAN
            if ($jumlahHari > 1) {
                return response()->json([
                    'message' => 'Pengajuan Cuti maksimal adalah 1 hari. Jika lebih dari 1 hari, silakan ajukan sebagai Izin.'
                ], 422);
            }

            if ($this->getAnnualLeaveRemaining($karyawan) <= 0) {
                return response()->json([
                    'message' => 'Jatah cuti tahunan Anda telah habis.',
                ], 422);
            }
        }

        $berkasPath = null;

        if ($request->hasFile('berkas_bukti')) {
            $berkasPath = $request->file('berkas_bukti')
                ->store('berkas_cuti', 'public');
        }

        $cuti = Cuti::create([
            'id_karyawan'       => $karyawan->id_karyawan,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai'     => $request->tanggal_mulai,
            'tanggal_selesai'   => $request->tanggal_selesai,
            'jenis_cuti'        => $request->jenis_cuti,
            'alasan'            => $request->alasan,
            'berkas_bukti'      => $berkasPath,
            'status'            => 'pending_pimpinan',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil dikirim.',
            'data'    => [
                'id_cuti'         => $cuti->id_cuti,
                'tanggal_mulai'   => $cuti->tanggal_mulai,
                'tanggal_selesai' => $cuti->tanggal_selesai,
                'jenis_cuti'      => $cuti->jenis_cuti,
                'status'          => $cuti->status,
            ],
        ], 201);
    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        // Cari data karyawan berdasarkan user login
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.'
            ], 404);
        }

        // Cari pengajuan cuti berdasarkan ID dan ID Karyawan yang sedang login
        $cuti = Cuti::where('id_cuti', $id)
                    ->where('id_karyawan', $karyawan->id_karyawan)
                    ->first();

        // Pastikan cuti ditemukan
        if (!$cuti) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan atau bukan milik Anda.'
            ], 404);
        }

        // Pastikan hanya cuti yang berstatus 'pending', 'pending_pimpinan', atau 'pending_kabag' yang bisa dihapus
        $statusPending = ['pending', 'pending_pimpinan', 'pending_kabag'];
        if (!in_array($cuti->status, $statusPending)) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan yang sudah diproses (Disetujui/Ditolak) tidak dapat dihapus.'
            ], 400);
        }

        // Hapus file bukti jika ada
        if ($cuti->berkas_bukti) {
            Storage::disk('public')->delete($cuti->berkas_bukti);
        }

        // Hapus data dari database
        $cuti->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil dihapus.'
        ], 200);
    }
}