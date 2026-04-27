<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Cuti;
use App\Models\Penilaian;
use App\Models\Penggajian;
use App\Models\User;

class KepalaBagianController extends Controller
{
    public function index()
    {
        $jumlahKaryawan = Karyawan::where('status_karyawan', 'aktif')->count();
        $penilaian = Penilaian::latest()->take(2)->get();
        $karyawan = Karyawan::latest()->take(5)->get();

        $karyawanBelumLengkap = User::whereHas('role', function ($query) {
            $query->where('nama_role', 'Karyawan')->orWhere('nama_role', 'karyawan');
        })->where(function ($query) {
            $query->doesntHave('karyawan')
                  ->orWhereHas('karyawan', function ($sub) {
                      $sub->whereNull('status_karyawan')->orWhere('status_karyawan', '');
                  });
        })->count();

        $karyawanTidakAktif = Karyawan::where('status_karyawan', 'keluar')->count();

        $bulanSekarang = now()->month;
        $tahunSekarang = now()->year;

        $evaluasiSelesai = Penilaian::where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->distinct('id_karyawan')
            ->count('id_karyawan');

        $progressPenilaian = Karyawan::where('status_karyawan', 'aktif')
            ->leftJoin('penilaian', function ($join) use ($bulanSekarang, $tahunSekarang) {
                $join->on('karyawan.id_karyawan', '=', 'penilaian.id_karyawan')
                     ->where('penilaian.bulan', '=', $bulanSekarang)
                     ->where('penilaian.tahun', '=', $tahunSekarang);
            })
            ->select('karyawan.*', 'penilaian.id_penilaian as is_dinilai', 'penilaian.total_skor')
            ->orderBy('karyawan.nama')
            ->get();

        return view('kepala_bagian.home', compact(
            'jumlahKaryawan',
            'penilaian',
            'karyawan',
            'karyawanBelumLengkap',
            'karyawanTidakAktif',
            'evaluasiSelesai',
            'progressPenilaian'
        ));
    }

    public function karyawan()
    {
        // Mengambil user dengan role 'karyawan'
        // Jika belum ada data karyawan, otomatis kita tampung (bisa null relation-nya)
        $dataKaryawan = User::with('karyawan')
            ->whereHas('role', function ($query) {
                $query->where('nama_role', 'Karyawan')->orWhere('nama_role', 'karyawan');
            })->get();

        return view('kepala_bagian.kelola_karyawan', compact('dataKaryawan'));
    }

    public function penilaian()
    {
        $karyawan = Karyawan::where('status_karyawan', 'Aktif')->get();
        $riwayatPenilaian = Penilaian::with('karyawan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();

        $karyawan = Karyawan::where('status_karyawan', 'aktif')
            ->select('id_karyawan', 'nama')
            ->orderBy('nama')
            ->get();

        return view('kepala_bagian.penilaian_kinerja', compact('karyawan', 'riwayatPenilaian'));
    }

    public function storePenilaian(Request $request)
    {
        $request->validate([
            'id_karyawan'    => 'required|exists:karyawan,id_karyawan',
            'periode'        => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'disiplin'       => 'required|numeric|min:0|max:100',
            'produktivitas'  => 'required|numeric|min:0|max:100',
            'tanggung_jawab' => 'required|numeric|min:0|max:100',
            'sikap_kerja'    => 'required|numeric|min:0|max:100',
            'loyalitas'      => 'required|numeric|min:0|max:100',
            'total_skor'     => 'required|numeric|min:0|max:100',
        ]);

        [$tahun, $bulan] = explode('-', $request->periode);

        Penilaian::create([
            'id_karyawan'    => $request->id_karyawan,
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'disiplin'       => $request->disiplin,
            'produktivitas'  => $request->produktivitas,
            'tanggung_jawab' => $request->tanggung_jawab,
            'sikap_kerja'    => $request->sikap_kerja,
            'loyalitas'      => $request->loyalitas,
            'total_skor'     => $request->total_skor,
            'dinilai_oleh'   => auth()->user()->id_user
        ]);

        return redirect()->route('kabag.penilaian')->with('success', 'Penilaian Kinerja berhasil disimpan!');
    }

    public function storePenilaianSkala5(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'disiplin' => 'required|integer|min:1|max:5',
            'produktivitas' => 'required|integer|min:1|max:5',
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'sikap_kerja' => 'required|integer|min:1|max:5',
            'loyalitas' => 'required|integer|min:1|max:5',
            'catatan_evaluasi' => 'nullable|string',
        ]);

        $bobot = [
            'disiplin' => 0.20,
            'produktivitas' => 0.30,
            'tanggung_jawab' => 0.20,
            'sikap_kerja' => 0.15,
            'loyalitas' => 0.15,
        ];

        $skorTertimbang = 0;
        foreach ($bobot as $indikator => $persentase) {
            $skorTertimbang += ((int)$validated[$indikator]) * $persentase;
        }
        $skorAkhir = (int) round($skorTertimbang);

        Penilaian::create([
            'id_karyawan' => $validated['id_karyawan'],
            'bulan' => now()->month,
            'tahun' => now()->year,
            'disiplin' => $validated['disiplin'],
            'produktivitas' => $validated['produktivitas'],
            'tanggung_jawab' => $validated['tanggung_jawab'],
            'sikap_kerja' => $validated['sikap_kerja'],
            'loyalitas' => $validated['loyalitas'],
            'total_skor' => $skorAkhir,
            'catatan_evaluasi' => $validated['catatan_evaluasi'] ?? null,
            'dinilai_oleh' => auth()->id(),
        ]);

        return redirect()->route('kabag.penilaian')->with('success', 'Penilaian kinerja berhasil disimpan.');
    }

    public function gaji(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $dataGaji = Penggajian::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tahunList = range(now()->year, now()->year - 4);

        return view('kepala_bagian.manajemen_gaji', compact('dataGaji', 'bulan', 'tahun', 'bulanList', 'tahunList'));
    }

    public function createGaji()
    {
        $karyawan = Karyawan::where('status_karyawan', 'aktif')->get();
        return view('kepala_bagian.form_gaji', compact('karyawan'));
    }

    private function parsePeriode(string $periode): array
    {
        if (!preg_match('/^\d{4}-\d{2}$/', $periode)) {
            abort(422, 'Format periode tidak valid. Gunakan format YYYY-MM.');
        }
        [$tahun, $bulan] = explode('-', $periode);
        return [(int)$tahun, (int)$bulan];
    }

    public function storeGaji(Request $request)
    {
        $request->validate([
            'id_karyawan'       => 'required|exists:karyawan,id_karyawan',
            'periode'           => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'gaji_pokok'        => 'required|numeric|min:0',
            'uang_makan'        => 'nullable|numeric|min:0',
            'tunjangan_jabatan' => 'nullable|numeric|min:0',
            'insentif_kinerja'  => 'nullable|numeric|min:0',
            'tunjangan_program' => 'nullable|numeric|min:0',
            'tunjangan_bpjs'    => 'nullable|numeric|min:0',
            'bonus'             => 'nullable|numeric|min:0',
            'lain_lain'         => 'nullable|numeric|min:0',
            'potongan_absen'    => 'nullable|numeric|min:0',
            'cash_bon'          => 'nullable|numeric|min:0',
            'potongan_bpjs'     => 'nullable|numeric|min:0',
            'potongan_lain'     => 'nullable|numeric|min:0',
        ]);

        [$tahun, $bulan] = $this->parsePeriode($request->periode);

        $totalPenerimaan = (float)($request->gaji_pokok ?? 0)
            + (float)($request->uang_makan ?? 0)
            + (float)($request->tunjangan_jabatan ?? 0)
            + (float)($request->insentif_kinerja ?? 0)
            + (float)($request->tunjangan_program ?? 0)
            + (float)($request->tunjangan_bpjs ?? 0)
            + (float)($request->bonus ?? 0)
            + (float)($request->lain_lain ?? 0);

        $totalPotongan = (float)($request->potongan_absen ?? 0)
            + (float)($request->cash_bon ?? 0)
            + (float)($request->potongan_bpjs ?? 0)
            + (float)($request->potongan_lain ?? 0);

        $totalGaji = $totalPenerimaan - $totalPotongan;

        Penggajian::create([
            'id_karyawan'       => $request->id_karyawan,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => $request->gaji_pokok ?? 0,
            'uang_makan'        => $request->uang_makan ?? 0,
            'tunjangan_jabatan' => $request->tunjangan_jabatan ?? 0,
            'insentif_kinerja'  => $request->insentif_kinerja ?? 0,
            'tunjangan_program' => $request->tunjangan_program ?? 0,
            'tunjangan_bpjs'    => $request->tunjangan_bpjs ?? 0,
            'bonus'             => $request->bonus ?? 0,
            'lain_lain'         => $request->lain_lain ?? 0,
            'total_penerimaan'  => $totalPenerimaan,
            'potongan_absen'    => $request->potongan_absen ?? 0,
            'cash_bon'          => $request->cash_bon ?? 0,
            'potongan_bpjs'     => $request->potongan_bpjs ?? 0,
            'potongan_lain'     => $request->potongan_lain ?? 0,
            'total_gaji'        => $totalGaji,
            'tanggal_dibuat'    => now()->toDateString(),
            'status_slip'       => 'draft',
        ]);

        return redirect()->route('kabag.gaji', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Slip gaji berhasil disimpan.');
    }

    public function editGaji($id)
    {
        $gaji = Penggajian::findOrFail($id);
        $karyawan = Karyawan::where('status_karyawan', 'aktif')->get();
        return view('kepala_bagian.edit_gaji', compact('gaji', 'karyawan'));
    }

    public function updateGaji(Request $request, $id)
    {
        $gaji = Penggajian::findOrFail($id);

        $request->validate([
            'id_karyawan'       => 'required|exists:karyawan,id_karyawan',
            'periode'           => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'gaji_pokok'        => 'required|numeric|min:0',
            'uang_makan'        => 'nullable|numeric|min:0',
            'tunjangan_jabatan' => 'nullable|numeric|min:0',
            'insentif_kinerja'  => 'nullable|numeric|min:0',
            'tunjangan_program' => 'nullable|numeric|min:0',
            'tunjangan_bpjs'    => 'nullable|numeric|min:0',
            'bonus'             => 'nullable|numeric|min:0',
            'lain_lain'         => 'nullable|numeric|min:0',
            'potongan_absen'    => 'nullable|numeric|min:0',
            'cash_bon'          => 'nullable|numeric|min:0',
            'potongan_bpjs'     => 'nullable|numeric|min:0',
            'potongan_lain'     => 'nullable|numeric|min:0',
        ]);

        [$tahun, $bulan] = $this->parsePeriode($request->periode);

        $totalPenerimaan = (float)($request->gaji_pokok ?? 0)
            + (float)($request->uang_makan ?? 0)
            + (float)($request->tunjangan_jabatan ?? 0)
            + (float)($request->insentif_kinerja ?? 0)
            + (float)($request->tunjangan_program ?? 0)
            + (float)($request->tunjangan_bpjs ?? 0)
            + (float)($request->bonus ?? 0)
            + (float)($request->lain_lain ?? 0);

        $totalPotongan = (float)($request->potongan_absen ?? 0)
            + (float)($request->cash_bon ?? 0)
            + (float)($request->potongan_bpjs ?? 0)
            + (float)($request->potongan_lain ?? 0);

        $totalGaji = $totalPenerimaan - $totalPotongan;

        $gaji->update([
            'id_karyawan'       => $request->id_karyawan,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => $request->gaji_pokok ?? 0,
            'uang_makan'        => $request->uang_makan ?? 0,
            'tunjangan_jabatan' => $request->tunjangan_jabatan ?? 0,
            'insentif_kinerja'  => $request->insentif_kinerja ?? 0,
            'tunjangan_program' => $request->tunjangan_program ?? 0,
            'tunjangan_bpjs'    => $request->tunjangan_bpjs ?? 0,
            'bonus'             => $request->bonus ?? 0,
            'lain_lain'         => $request->lain_lain ?? 0,
            'total_penerimaan'  => $totalPenerimaan,
            'potongan_absen'    => $request->potongan_absen ?? 0,
            'cash_bon'          => $request->cash_bon ?? 0,
            'potongan_bpjs'     => $request->potongan_bpjs ?? 0,
            'potongan_lain'     => $request->potongan_lain ?? 0,
            'total_gaji'        => $totalGaji,
            'status_slip'       => $request->input('status_slip', $gaji->status_slip),
        ]);

        return redirect()->route('kabag.gaji', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Slip gaji berhasil diperbarui.');
    }

    public function destroyGaji($id)
    {
        $gaji = Penggajian::findOrFail($id);
        $bulan = $gaji->bulan;
        $tahun = $gaji->tahun;
        $gaji->delete();

        return redirect()->route('kabag.gaji', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Slip gaji berhasil dihapus.');
    }
    public function detailKaryawan($id)
    {
        // Cari user berdasarkan id_user, sekalian bawa relasi karyawannya (jika sudah ada)
        $user = User::with('karyawan')->findOrFail($id);
        
        return view('kepala_bagian.detail_karyawan', compact('user'));
    }

    public function storeKaryawan(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1. Validasi inputan form
        $request->validate([
            'nama'            => 'required|string|max:255',
            'no_hp'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'alamat'          => 'nullable|string',
            'status_karyawan' => 'required|string',
            'divisi'          => 'nullable|in:keuangan,admin umum,akademik,marketing,office boy',
        ]);

        Karyawan::updateOrCreate(
            ['id_user' => $user->id_user], 
            [
                'nama'            => $request->nama,
                'no_hp'           => $request->no_hp,
                'email'           => $request->email,
                'alamat'          => $request->alamat,
                'status_karyawan' => $request->status_karyawan,
                'divisi'          => $request->divisi,
                // Kolom foto bisa ditambahkan nanti jika Anda sudah siap dengan logika file upload
            ]
        );

        return redirect()->route('kabag.karyawan')->with('success', 'Biodata karyawan berhasil disimpan.');
    }

    public function cuti()
    {
        $dataCuti = Cuti::with('karyawan')
            ->where('status', 'pending_kabag')
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        return view('kepala_bagian.verifikasi_cuti', compact('dataCuti'));
    }

    public function approveCuti($id)
    {
        $cuti = Cuti::where('id_cuti', $id)
            ->where('status', 'pending_kabag')
            ->firstOrFail();

        $cuti->update([
            'status'        => 'pending_pimpinan',
            'disetujui_oleh'=> auth()->id(),
        ]);

        return redirect()->route('kabag.cuti')
            ->with('success', 'Pengajuan cuti disetujui dan diteruskan ke Pimpinan.');
    }

    public function rejectCuti($id)
    {
        $cuti = Cuti::where('id_cuti', $id)
            ->where('status', 'pending_kabag')
            ->firstOrFail();

        $cuti->update([
            'status'        => 'rejected',
            'disetujui_oleh'=> auth()->id(),
        ]);

        return redirect()->route('kabag.cuti')
            ->with('success', 'Pengajuan cuti telah ditolak.');
    }
}
