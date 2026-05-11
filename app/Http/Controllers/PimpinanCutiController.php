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

        // Riwayat Cuti (yang sudah disetujui / ditolak)
        $riwayatCuti = (clone $query)->whereIn('status', ['Disetujui', 'Ditolak', 'approved', 'rejected'])->paginate(10, ['*'], 'riwayat_page');

        return view('pimpinan.manajemen_cuti', compact('dataCuti', 'riwayatCuti'));
    }

    /**
     * Menyetujui pengajuan cuti
     */
    public function approve($id)
    {
        $cuti = Cuti::with('karyawan')->findOrFail($id);
        $cuti->update([
            'status' => 'approved',
            'disetujui_oleh' => Auth::user()->id_user
        ]);

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
        $cuti->update([
            'status' => 'rejected',
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