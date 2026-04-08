<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar pengajuan cuti untuk Pimpinan
     */
    public function indexPimpinan()
    {
        // Mengambil semua data cuti beserta relasi karyawan
        $dataCuti = Cuti::with('karyawan.user')->orderBy('created_at', 'desc')->get();

        return view('pimpinan.manajemen_cuti', compact('dataCuti'));
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