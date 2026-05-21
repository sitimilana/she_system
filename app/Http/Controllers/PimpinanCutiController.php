<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\StatusCutiMail;

class PimpinanCutiController extends Controller
{
    /**
     * Menampilkan daftar pengajuan cuti untuk Pimpinan
     */
    public function indexPimpinan(Request $request)
    {
        $query = Cuti::with('karyawan.user')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%");
            });
        }

        if ($request->has('jenis_cuti') && $request->jenis_cuti != '') {
            $query->where('jenis_cuti', $request->jenis_cuti);
        }

        $dataCuti = (clone $query)->whereIn('status', ['pending_pimpinan', 'pending_kabag'])->paginate(10, ['*'], 'pending_page');

        // Deteksi Bentrok untuk pengajuan Cuti yang pending
        foreach ($dataCuti as $cuti) {
            if (in_array(strtolower($cuti->jenis_cuti), ['cuti', 'cuti tahunan']) && $cuti->karyawan) {
                $bentrok = \App\Models\Cuti::where('id_cuti', '!=', $cuti->id_cuti)
                    ->whereIn('status', ['approved', 'pending_pimpinan', 'pending_kabag'])
                    ->where(function($query) {
                        $query->whereRaw('LOWER(jenis_cuti) = ?', ['cuti'])
                            ->orWhereRaw('LOWER(jenis_cuti) = ?', ['cuti tahunan']);
                    })
                    ->where(function($q) use ($cuti) {
                        $q->whereBetween('tanggal_mulai', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                          ->orWhereBetween('tanggal_selesai', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                          ->orWhere(function($sq) use ($cuti) {
                              $sq->where('tanggal_mulai', '<=', $cuti->tanggal_mulai)
                                 ->where('tanggal_selesai', '>=', $cuti->tanggal_selesai);
                          });
                    })
                    ->whereHas('karyawan', function($q) use ($cuti) {
                        $q->where('divisi', $cuti->karyawan->divisi);
                    })
                    ->get();
                
                $cuti->is_bentrok = $bentrok->count() > 0;
                $cuti->data_bentrok = $bentrok;
            } else {
                $cuti->is_bentrok = false;
            }
        }

        // Riwayat Cuti (yang sudah disetujui / ditolak)
        $riwayatCuti = (clone $query)->whereIn('status', ['Disetujui', 'Ditolak', 'approved', 'rejected'])->paginate(10, ['*'], 'riwayat_page');

        return view('pimpinan.manajemen_cuti', compact('dataCuti', 'riwayatCuti'));
    }

    /**
     * Menyetujui pengajuan cuti
     */
    public function approve(Request $request, $id)
    {
        $cuti = Cuti::with('karyawan')->findOrFail($id);
        $cuti->update([
            'status' => 'approved',
            'keterangan_pimpinan' => $request->keterangan_pimpinan ?? 'Disetujui',
            'disetujui_oleh' => Auth::user()->id_user
        ]);

        // MENGURANGI SALDO JATAH CUTI (Hanya jika tipe pengajuannya Cuti)
        if (strtolower($cuti->jenis_cuti) === 'cuti' && $cuti->karyawan) {
            if ($cuti->karyawan->sisa_cuti >= 0) {
                $cuti->karyawan->decrement('sisa_cuti', 1);
            }
        }

        // PERBAIKAN: Langsung ke $cuti->karyawan->email
        if ($cuti->karyawan && $cuti->karyawan->email) {
            try {
                Mail::to($cuti->karyawan->email)->send(new \App\Mail\StatusCutiMail($cuti, 'Disetujui'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal mengirim email approve: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $cuti = Cuti::with('karyawan')->findOrFail($id);

        if (strtolower($cuti->jenis_cuti) === 'sakit') {
            // AUTO-CONVERT Sakit menjadi Izin
            $cuti->update([
                'jenis_cuti' => 'Izin',
                'status' => 'approved',
                'keterangan_pimpinan' => $request->keterangan_pimpinan ?? 'Bukti tidak valid. Dialihkan otomatis ke Izin (Potong Gaji).',
                'disetujui_oleh' => Auth::user()->id_user
            ]);

            if ($cuti->karyawan && $cuti->karyawan->email) {
                try {
                    Mail::to($cuti->karyawan->email)->send(new \App\Mail\StatusCutiMail($cuti, 'Dialihkan ke Izin'));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal mengirim email auto-convert: " . $e->getMessage());
                }
            }

            return redirect()->back()->with('success', 'Sakit ditolak, otomatis dialihkan menjadi Izin berbayar potongan.');
        }

        // PENOLAKAN NORMAL UNTUK CUTI & IZIN
        $cuti->update([
            'status' => 'rejected',
            'keterangan_pimpinan' => $request->keterangan_pimpinan ?? 'Ditolak oleh pimpinan.',
            'disetujui_oleh' => Auth::user()->id_user
        ]);

        // PERBAIKAN: Langsung ke $cuti->karyawan->email
        if ($cuti->karyawan && $cuti->karyawan->email) {
            try {
                Mail::to($cuti->karyawan->email)->send(new \App\Mail\StatusCutiMail($cuti, 'Ditolak'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal mengirim email reject: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Pengajuan cuti telah ditolak.');
    }
}