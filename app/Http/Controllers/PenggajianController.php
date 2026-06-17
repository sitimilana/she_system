<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\Absensi;

class PenggajianController extends Controller
{
    private const TUNJ_JABATAN_STANDAR = 0;
    private const TUNJ_BPJS_STANDAR = 25000;

    private function getGajiPokok($karyawan)
    {
        $gajiPokok = 2000000; // Default Dasar
        if ($karyawan && $karyawan->user && $karyawan->user->role) {
            $roleName = strtolower($karyawan->user->role->nama_role);
            if ($roleName == 'pimpinan') {
                $gajiPokok = 3500000;
            } elseif (in_array($roleName, ['kepala bagian', 'kabag'])) {
                $gajiPokok = 2800000;
            } else {
                $divisi = strtolower($karyawan->divisi);
                if ($divisi == 'keuangan') {
                    $gajiPokok = 2500000;
                } elseif (in_array($divisi, ['admin umum', 'akademik', 'marketing'])) {
                    $gajiPokok = 2415000; // Standar UMK Kediri
                } elseif ($divisi == 'office boy') {
                    $gajiPokok = 2000000;
                }
            }
        }
        return $gajiPokok;
    }

    public function gaji(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $search = $request->input('search');

        $query = Penggajian::with('karyawan')
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->latest();
        
        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        $dataGaji = $query->paginate(10)->appends(request()->query());

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tahunList = range(now()->year, now()->year - 4);

        return view('pimpinan.manajemen_gaji', compact('dataGaji', 'bulan', 'tahun', 'bulanList', 'tahunList'));
    }

    public function createGaji()
    {
        $karyawan = Karyawan::where('status_karyawan', 'aktif')->get();
        return view('pimpinan.form_gaji', compact('karyawan'));
    }

    public function storeGaji(Request $request)
    {
        $request->validate([
            'id_karyawan'       => 'required|exists:karyawan,id_karyawan',
            'periode'           => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'tunjangan_jabatan' => 'nullable|numeric|min:0', 
            'tunjangan_program' => 'nullable|numeric|min:0',
            'bonus'             => 'nullable|numeric|min:0',
            'lain_lain'         => 'nullable|numeric|min:0',
            'uang_makan'        => 'nullable|numeric|min:0', 
            'insentif_kinerja'  => 'nullable|numeric|min:0',
            'potongan_absen'    => 'nullable|numeric|min:0', 
            'cash_bon'          => 'nullable|numeric|min:0',
            'cash_bon_2'        => 'nullable|numeric|min:0', 
            'potongan_lain'     => 'nullable|numeric|min:0',
        ]);

        [$tahun, $bulan] = explode('-', $request->periode);

        $gajiExist = Penggajian::where('id_karyawan', $request->id_karyawan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists();

        if ($gajiExist) {
            return redirect()->back()
                ->withErrors(['Slip gaji untuk karyawan ini pada periode tersebut sudah dibuat. Silakan edit data yang sudah ada di menu Manajemen Gaji jika ingin melakukan perubahan.'])
                ->withInput();
        }
        
        $karyawan = Karyawan::with('user.role')->find($request->id_karyawan);
        $gajiPokok = $this->getGajiPokok($karyawan);

        // PENERIMAAN
        $totalPenerimaan = $gajiPokok
            + (float) ($request->uang_makan ?? 0)
            + (float) ($request->tunjangan_jabatan ?? 0)
            + (float) ($request->insentif_kinerja ?? 0)
            + (float) ($request->tunjangan_program ?? 0)
            + (float) ($request->bonus ?? 0)
            + (float) ($request->lain_lain ?? 0);

        // POTONGAN
        $totalPotongan = (float) ($request->potongan_absen ?? 0)
            + (float) ($request->cash_bon ?? 0)
            + (float) ($request->cash_bon_2 ?? 0) 
            + (float) self::TUNJ_BPJS_STANDAR 
            + (float) ($request->potongan_lain ?? 0);

        $totalGaji = $totalPenerimaan - $totalPotongan;

        Penggajian::create([
            'id_karyawan'       => $request->id_karyawan,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => $gajiPokok,
            'uang_makan'        => $request->uang_makan ?? 0,
            'tunjangan_jabatan' => $request->tunjangan_jabatan ?? 0,
            'insentif_kinerja'  => $request->insentif_kinerja ?? 0, 
            'tunjangan_program' => $request->tunjangan_program ?? 0,
            'tunjangan_bpjs'    => 0,
            'bonus'             => $request->bonus ?? 0,
            'lain_lain'         => $request->lain_lain ?? 0,
            'total_penerimaan'  => $totalPenerimaan,
            'potongan_absen'    => $request->potongan_absen ?? 0,
            'cash_bon'          => $request->cash_bon ?? 0,
            'cash_bon_2'        => $request->cash_bon_2 ?? 0, 
            'potongan_bpjs'     => self::TUNJ_BPJS_STANDAR, 
            'potongan_lain'     => $request->potongan_lain ?? 0,
            'total_potongan'    => $totalPotongan, 
            'total_gaji'        => $totalGaji,
            'tanggal_dibuat'    => now()->toDateString(),
            'status_slip'       => $request->input('status_slip', 'draft'),
        ]);

        return redirect()->route('pimpinan.gaji', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Slip gaji berhasil disimpan.');
    }

    public function editGaji($id)
    {
        $gaji = Penggajian::findOrFail($id);
        $karyawan = Karyawan::where('status_karyawan', 'aktif')->get();
        return view('pimpinan.edit_gaji', compact('gaji', 'karyawan'));
    }

    public function updateGaji(Request $request, $id)
    {
        $gaji = Penggajian::findOrFail($id);

        $request->validate([
            'id_karyawan'       => 'required|exists:karyawan,id_karyawan',
            'periode'           => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'uang_makan'        => 'nullable|numeric|min:0', 
            'insentif_kinerja'  => 'nullable|numeric|min:0', 
            'tunjangan_jabatan' => 'nullable|numeric|min:0', 
            'tunjangan_program' => 'nullable|numeric|min:0',
            'bonus'             => 'nullable|numeric|min:0',
            'lain_lain'         => 'nullable|numeric|min:0',
            'potongan_absen'    => 'nullable|numeric|min:0', 
            'cash_bon'          => 'nullable|numeric|min:0',
            'cash_bon_2'        => 'nullable|numeric|min:0', 
            'potongan_lain'     => 'nullable|numeric|min:0',
        ]);

        [$tahun, $bulan] = $this->parsePeriode($request->periode);

        $karyawan = Karyawan::with('user.role')->find($request->id_karyawan);
        $gajiPokok = $this->getGajiPokok($karyawan);

        $totalPenerimaan = (float) $gajiPokok 
            + (float) ($request->uang_makan ?? 0)
            + (float) ($request->tunjangan_jabatan ?? 0)
            + (float) ($request->insentif_kinerja ?? 0)
            + (float) ($request->tunjangan_program ?? 0)
            + (float) ($request->bonus ?? 0)
            + (float) ($request->lain_lain ?? 0);

        $totalPotongan = (float) ($request->potongan_absen ?? 0)
            + (float) ($request->cash_bon ?? 0)
            + (float) ($request->cash_bon_2 ?? 0) 
            + (float) self::TUNJ_BPJS_STANDAR 
            + (float) ($request->potongan_lain ?? 0);

        $totalGaji = $totalPenerimaan - $totalPotongan;

        $gaji->update([
            'id_karyawan'       => $request->id_karyawan,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => $gajiPokok,
            'uang_makan'        => $request->uang_makan ?? 0,
            'tunjangan_jabatan' => $request->tunjangan_jabatan ?? 0,
            'insentif_kinerja'  => $request->insentif_kinerja ?? 0,
            'tunjangan_program' => $request->tunjangan_program ?? 0,
            'tunjangan_bpjs'    => 0,
            'bonus'             => $request->bonus ?? 0,
            'lain_lain'         => $request->lain_lain ?? 0,
            'total_penerimaan'  => $totalPenerimaan,
            'potongan_absen'    => $request->potongan_absen ?? 0,
            'cash_bon'          => $request->cash_bon ?? 0,
            'cash_bon_2'        => $request->cash_bon_2 ?? 0, 
            'potongan_bpjs'     => self::TUNJ_BPJS_STANDAR,
            'potongan_lain'     => $request->potongan_lain ?? 0,
            'total_potongan'    => $totalPotongan, 
            'total_gaji'        => $totalGaji,
            'status_slip'       => $request->input('status_slip', $gaji->status_slip),
        ]);

        return redirect()->route('pimpinan.gaji', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Slip gaji berhasil diperbarui.');
    }

    public function finalizeGaji($id)
    {
        $gaji = Penggajian::findOrFail($id);
        $gaji->update(['status_slip' => 'final']);
        return redirect()->route('pimpinan.gaji', ['bulan' => $gaji->bulan, 'tahun' => $gaji->tahun])
            ->with('success', 'Slip gaji berhasil difinalisasi.');
    }

    public function destroyGaji($id)
    {
        $gaji = Penggajian::findOrFail($id);
        $gaji->delete();
        return redirect()->route('pimpinan.gaji', ['bulan' => $gaji->bulan, 'tahun' => $gaji->tahun])
            ->with('success', 'Slip gaji berhasil dihapus.');
    }
    
    private function parsePeriode(string $periode): array
    {
        if (!preg_match('/^\d{4}-\d{2}$/', $periode)) {
            abort(422, 'Format periode tidak valid. Gunakan format YYYY-MM.');
        }
        [$tahun, $bulan] = explode('-', $periode);
        return [(int)$tahun, (int)$bulan];
    }

    public function getKaryawanFinansial(Request $request, $id)
    {
        $karyawan = Karyawan::with('user.role')->find($id);
        $bulan = $request->query('bulan', now()->month);
        $tahun = $request->query('tahun', now()->year);

        $bulanLalu = $bulan == 1 ? 12 : $bulan - 1;
        $tahunLalu = $bulan == 1 ? $tahun - 1 : $tahun;
        
        $gajiPokok = $this->getGajiPokok($karyawan);
        
        $penilaian = Penilaian::where('id_karyawan', $id)->where('bulan', $bulanLalu)->where('tahun', $tahunLalu)->first();
        $insentifKinerja = 0;
        $bonus = 0;
        $belumDinilai = false;
        if (!$penilaian) {
            // Sinyal penanda untuk memicu alert kuning di Blade
            $belumDinilai = true; 
        } else {
            // 1. Hitung Tunjangan Kinerja jika ada nilai
            if ($penilaian->total_skor >= 90) {
                $insentifKinerja = 150000;
            } elseif ($penilaian->total_skor >= 80) {
                $insentifKinerja = 100000;
            }

            // 2. Hitung Bonus Rank 1 jika ada nilai
            $topScore = Penilaian::where('bulan', $bulanLalu)->where('tahun', $tahunLalu)->max('total_skor');
            if ($topScore !== null && $penilaian->total_skor == $topScore) {
                $topKaryawans = Penilaian::where('bulan', $bulanLalu)->where('tahun', $tahunLalu)->where('total_skor', $topScore)->pluck('id_karyawan')->toArray();
                $maxHadir = -1;
                $bestKaryawanId = null;
                
                foreach ($topKaryawans as $kid) {
                    $hadirCount = Absensi::where('id_karyawan', $kid)->whereMonth('tanggal', $bulanLalu)->whereYear('tanggal', $tahunLalu)->where('status', 'hadir')->count();
                    if ($hadirCount > $maxHadir) {
                        $maxHadir = $hadirCount;
                        $bestKaryawanId = $kid;
                    }
                }
                if ($bestKaryawanId == $id) {
                    $bonus = 100000; 
                }
            }
        }
        // 3. Perhitungan Absensi (Menggunakan data 1 bulan sebelumnya)
        // Menggunakan whereIn agar status 'hadir' dan 'terlambat' sama-sama dihitung dapat Uang Makan
        $hadir = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulanLalu)->whereYear('tanggal', $tahunLalu)->whereIn('status', ['hadir', 'terlambat'])->count();
        $alpha = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulanLalu)->whereYear('tanggal', $tahunLalu)->whereIn('status', ['alfa', 'alpha'])->count();
        $izin = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulanLalu)->whereYear('tanggal', $tahunLalu)->where('status', 'izin')->count();
        $cuti = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulanLalu)->whereYear('tanggal', $tahunLalu)->where('status', 'cuti')->count();
        $cutiKehamilan = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulanLalu)->whereYear('tanggal', $tahunLalu)->where('status', 'cuti kehamilan')->count();
        
        // Uang Makan: 20rb x Hari Hadir
        $uangMakan = $hadir * 20000;
        
        // Logika Potongan Absen (Rp 70.000 per hari mbolos)
        // Alpha & Izin = potong. Cuti = hari 1 gratis, hari 2 dst dipotong. Sakit = Gratis.
        $hariPotong = $alpha + $izin; 

        $potonganAbsen = $hariPotong * 70000;

        $sudahDibuat = Penggajian::where('id_karyawan', $id)
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->exists();

        return response()->json([
            'belum_dinilai'     => $belumDinilai,
            'gaji_pokok'        => $gajiPokok,
            'tunjangan_jabatan' => self::TUNJ_JABATAN_STANDAR,
            'insentif_kinerja'  => $insentifKinerja,
            'uang_makan'        => $uangMakan,
            'potongan_absen'    => $potonganAbsen,
            'potongan_bpjs'     => self::TUNJ_BPJS_STANDAR,
            'bonus'             => $bonus,
            'cuti_kehamilan'    => $cutiKehamilan,
            'sudah_dibuat'      => $sudahDibuat,
        ]);
    }
}