<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;

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
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($request->has('jenis_cuti') && $request->jenis_cuti != '') {
            $query->where('jenis_cuti', $request->jenis_cuti);
        }

        // Data Cuti Menunggu Validasi (Pimpinan)
        // Jika sebelumnya sudah diverifikasi Kabag, berarti statusnya 'pending_pimpinan'
        // Namun sesuaikan dengan status aktual aplikasi Anda, misalnya 'Pending'
        $dataCuti = (clone $query)->where('status', 'Pending')->paginate(10, ['*'], 'pending_page');

        // Riwayat Cuti (yang sudah disetujui / ditolak)
        $riwayatCuti = (clone $query)->whereIn('status', ['Disetujui', 'Ditolak', 'approved', 'rejected'])->paginate(10, ['*'], 'riwayat_page');

        return view('pimpinan.manajemen_cuti', compact('dataCuti', 'riwayatCuti'));
    }

    /**
     * Menyetujui pengajuan cuti
     */
    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);
        $cuti->update([
            'status' => 'Disetujui',
            'disetujui_oleh' => Auth::id() // Menyimpan ID pimpinan yang menyetujui
        ]);

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    /**
     * Menolak pengajuan cuti
     */
    public function reject(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        $cuti->update([
            'status' => 'Ditolak',
            'disetujui_oleh' => Auth::id() // Menyimpan ID pimpinan yang menolak
        ]);

        return redirect()->back()->with('success', 'Pengajuan cuti telah ditolak.');
    }
}