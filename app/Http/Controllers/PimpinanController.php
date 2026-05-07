<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Cuti;
use App\Models\Penggajian;
use App\Models\User;
use App\Models\PengaturanKantor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\KaryawanApproveMail;
use App\Models\Penilaian;
use App\Models\Absensi;
use Carbon\Carbon;

class PimpinanController extends Controller
{
    
    // PENGATURAN NOMINAL GAJI STANDAR (UBAH ANGKA DI SINI JIKA ADA PERUBAHAN)
    private const GAJI_POKOK_STANDAR = 3000000; //  3 Juta
    private const TUNJ_JABATAN_STANDAR = 500000;  // 500 Ribu
    private const TUNJ_BPJS_STANDAR = 150000;     // 150 Ribu
    
    public function karyawanPending()
    {
        $users = User::with('role', 'karyawan')
                     ->where('status_akun', 'pending')
                     ->get();
        return view('pimpinan.karyawan_pending', compact('users'));
    }

    public function approveKaryawan($id)
    {
        $user = User::with('karyawan')->findOrFail($id);
        $user->status_akun = 'aktif';
        
        if ($user->karyawan) {
            $user->karyawan->status_karyawan = 'aktif';
            $user->karyawan->save();

            if($user->karyawan->email && $user->karyawan->email != '-') {
                try {
                    Mail::to($user->karyawan->email)->send(new KaryawanApproveMail($user));
                } catch (\Exception $e) {
                    // Abaikan jika error
                }
            }
        }
        
        $user->password_sementara = null;
        $user->save();

        return redirect()->route('pimpinan.karyawan_pending')->with('success', 'Akun & Profil karyawan berhasil diaktifkan dan dikirimkan email kredensial.');
    }

    // Jangan lupa tambahkan 'Request $request' di dalam parameter fungsi index
    public function index(Request $request)
    {
        $totalKaryawan = Karyawan::where('status_karyawan', 'aktif')->count();
        
        // Menghitung Cuti yang sedang berjalan hari ini dari tabel Cuti
        $karyawanCutiHariIni = Cuti::where('status', 'approved')
            ->whereDate('tanggal_mulai', '<=', Carbon::today())
            ->whereDate('tanggal_selesai', '>=', Carbon::today())
            ->count();

        // ==================================================
        // LOGIKA FILTER GRAFIK KEHADIRAN
        // ==================================================
        $filterKehadiran = $request->input('filter_kehadiran', 'hari_ini');
        $queryAbsensi = Absensi::query();

        if ($filterKehadiran == 'hari_ini') {
            $queryAbsensi->whereDate('tanggal', Carbon::today());
        } elseif ($filterKehadiran == 'minggu_ini') {
            $queryAbsensi->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filterKehadiran == 'bulan_ini') {
            $queryAbsensi->whereMonth('tanggal', Carbon::now()->month)
                         ->whereYear('tanggal', Carbon::now()->year);
        }

        // Klon query agar bisa mengeksekusi multiple count() tanpa menimpa satu sama lain
        $jmlHadir = (clone $queryAbsensi)->where('status', 'hadir')->count();
        $jmlTerlambat = (clone $queryAbsensi)->where('status', 'terlambat')->count();
        $jmlAlpha = (clone $queryAbsensi)->whereIn('status', ['alfa', 'alpha'])->count(); 
        $jmlCuti = (clone $queryAbsensi)->whereIn('status', ['cuti', 'izin', 'sakit'])->count();

        // ==================================================

        $totalBebanGaji = Penggajian::whereMonth('tanggal_dibuat', now()->month)
            ->whereYear('tanggal_dibuat', now()->year)
            ->sum('total_gaji');
        $totalBebanGaji = number_format($totalBebanGaji ?: 150000000, 0, ',', '.');

        $cutiTerbaru = []; // (Biarkan kosong seperti aslinya, atau isi dengan query cuti)
        $topKaryawan = []; // (Biarkan kosong seperti aslinya, atau isi dengan query penilaian)

        return view('pimpinan.dashboard', compact(
            'totalKaryawan',
            'karyawanCutiHariIni',
            'totalBebanGaji',
            'cutiTerbaru',
            'topKaryawan',
            'jmlHadir',
            'jmlTerlambat',
            'jmlAlpha',
            'jmlCuti',
            'filterKehadiran'
        ));
    }

    public function cuti(Request $request)
    {
        $query = Cuti::with('karyawan')
            ->where('status', 'pending_pimpinan');

        $queryRiwayat = Cuti::with('karyawan')
            ->whereIn('status', ['approved', 'rejected']);

        if ($request->filled('search')) {
            $search = $request->search;
            $karyawanFilter = function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%");
            };
            $query->whereHas('karyawan', $karyawanFilter);
            $queryRiwayat->whereHas('karyawan', $karyawanFilter);
        }

        if ($request->filled('jenis_cuti')) {
            $query->where('jenis_cuti', $request->jenis_cuti);
            $queryRiwayat->where('jenis_cuti', $request->jenis_cuti);
        }

        $dataCuti = $query->orderByDesc('tanggal_pengajuan')
            ->paginate(10, ['*'], 'page')
            ->withQueryString();

        $riwayatCuti = $queryRiwayat->orderByDesc('updated_at')
            ->paginate(10, ['*'], 'riwayat_page')
            ->withQueryString();

        return view('pimpinan.manajemen_cuti', compact('dataCuti', 'riwayatCuti'));
    }

    public function approveCuti($id)
    {
        $cuti = Cuti::with('karyawan')
            ->where('id_cuti', $id)
            ->where('status', 'pending_pimpinan')
            ->firstOrFail();

        $mulai      = \Carbon\Carbon::parse($cuti->tanggal_mulai);
        $selesai    = \Carbon\Carbon::parse($cuti->tanggal_selesai);
        $jumlahHari = $mulai->diffInDays($selesai) + 1;

        try {
            DB::transaction(function () use ($cuti, $jumlahHari) {
                $karyawan = Karyawan::lockForUpdate()->findOrFail($cuti->id_karyawan);

                if ($karyawan->sisa_cuti < $jumlahHari) {
                    throw new \RuntimeException('insufficient_quota');
                }

                $karyawan->update(['sisa_cuti' => $karyawan->sisa_cuti - $jumlahHari]);

                $cuti->update([
                    'status'        => 'approved',
                    'disetujui_oleh'=> auth()->id(),
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'insufficient_quota') {
                return redirect()->route('pimpinan.cuti')
                    ->with('error', 'Saldo cuti karyawan tidak mencukupi untuk pengajuan ini.');
            }
            throw $e;
        }

        return redirect()->route('pimpinan.cuti')
            ->with('success', 'Pengajuan cuti disetujui dan saldo cuti karyawan telah dipotong.');
    }

    public function rejectCuti($id)
    {
        $cuti = Cuti::where('id_cuti', $id)
            ->where('status', 'pending_pimpinan')
            ->firstOrFail();

        $cuti->update([
            'status'        => 'rejected',
            'disetujui_oleh'=> auth()->id(),
        ]);

        return redirect()->route('pimpinan.cuti')
            ->with('success', 'Pengajuan cuti telah ditolak.');
    }

    public function gaji(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $search = $request->input('search');

        $query = Penggajian::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        $dataGaji = $query->get();

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
            'tunjangan_program' => 'nullable|numeric|min:0',
            'bonus'             => 'nullable|numeric|min:0',
            'lain_lain'         => 'nullable|numeric|min:0',
            'cash_bon'          => 'nullable|numeric|min:0',
            'potongan_lain'     => 'nullable|numeric|min:0',
        ]);

        [$tahun, $bulan] = explode('-', $request->periode);

        $penilaian = Penilaian::where('id_karyawan', $request->id_karyawan)
                              ->where('bulan', $bulan)
                              ->where('tahun', $tahun)
                              ->first();

        $insentifKinerja = 0;
        if ($penilaian) {
            if ($penilaian->total_skor >= 90) {
                $insentifKinerja = 500000;
            } elseif ($penilaian->total_skor >= 80) {
                $insentifKinerja = 250000;
            }
        }

        $totalHadir = Absensi::where('id_karyawan', $request->id_karyawan)
                             ->whereMonth('tanggal', $bulan)
                             ->whereYear('tanggal', $tahun)
                             ->where('status', 'hadir')
                             ->count();
                             
        $totalAlpha = Absensi::where('id_karyawan', $request->id_karyawan)
                             ->whereMonth('tanggal', $bulan)
                             ->whereYear('tanggal', $tahun)
                             ->where('status', 'alpha')
                             ->count();

        $uangMakan = $totalHadir * 25000;
        $potonganAbsen = $totalAlpha * 100000;

        // MENGGUNAKAN NOMINAL STANDAR DARI CONSTANT DI ATAS
        $totalPenerimaan = (float) self::GAJI_POKOK_STANDAR 
            + $uangMakan 
            + (float) self::TUNJ_JABATAN_STANDAR 
            + $insentifKinerja 
            + (float) ($request->tunjangan_program ?? 0)
            + (float) self::TUNJ_BPJS_STANDAR 
            + (float) ($request->bonus ?? 0)
            + (float) ($request->lain_lain ?? 0);

        $totalPotongan = $potonganAbsen 
            + (float) ($request->cash_bon ?? 0)
            + (float) self::TUNJ_BPJS_STANDAR // Potongan BPJS sama dengan Tunjangan BPJS
            + (float) ($request->potongan_lain ?? 0);

        $totalGaji = $totalPenerimaan - $totalPotongan;

        Penggajian::create([
            'id_karyawan'       => $request->id_karyawan,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => self::GAJI_POKOK_STANDAR,
            'uang_makan'        => $uangMakan,
            'tunjangan_jabatan' => self::TUNJ_JABATAN_STANDAR,
            'insentif_kinerja'  => $insentifKinerja, 
            'tunjangan_program' => $request->tunjangan_program ?? 0,
            'tunjangan_bpjs'    => self::TUNJ_BPJS_STANDAR,
            'bonus'             => $request->bonus ?? 0,
            'lain_lain'         => $request->lain_lain ?? 0,
            'total_penerimaan'  => $totalPenerimaan,
            'potongan_absen'    => $potonganAbsen,
            'cash_bon'          => $request->cash_bon ?? 0,
            'potongan_bpjs'     => self::TUNJ_BPJS_STANDAR,
            'potongan_lain'     => $request->potongan_lain ?? 0,
            'total_gaji'        => $totalGaji,
            'tanggal_dibuat'    => now()->toDateString(),
            'status_slip'       => $request->input('status_slip', 'draft'),
        ]);

        return redirect()->route('pimpinan.gaji', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Slip gaji berhasil disimpan. Insentif dan Potongan telah dihitung otomatis!');
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
            'tunjangan_program' => 'nullable|numeric|min:0',
            'bonus'             => 'nullable|numeric|min:0',
            'lain_lain'         => 'nullable|numeric|min:0',
            'potongan_absen'    => 'nullable|numeric|min:0', 
            'cash_bon'          => 'nullable|numeric|min:0',
            'potongan_lain'     => 'nullable|numeric|min:0',
        ]);

        [$tahun, $bulan] = $this->parsePeriode($request->periode);

        // MENGGUNAKAN NOMINAL STANDAR DARI CONSTANT DI ATAS
        $totalPenerimaan = (float) self::GAJI_POKOK_STANDAR 
            + (float) ($request->uang_makan ?? 0)
            + (float) self::TUNJ_JABATAN_STANDAR 
            + (float) ($request->insentif_kinerja ?? 0)
            + (float) ($request->tunjangan_program ?? 0)
            + (float) self::TUNJ_BPJS_STANDAR 
            + (float) ($request->bonus ?? 0)
            + (float) ($request->lain_lain ?? 0);

        $totalPotongan = (float) ($request->potongan_absen ?? 0)
            + (float) ($request->cash_bon ?? 0)
            + (float) self::TUNJ_BPJS_STANDAR 
            + (float) ($request->potongan_lain ?? 0);

        $totalGaji = $totalPenerimaan - $totalPotongan;

        $gaji->update([
            'id_karyawan'       => $request->id_karyawan,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => self::GAJI_POKOK_STANDAR,
            'uang_makan'        => $request->uang_makan ?? 0,
            'tunjangan_jabatan' => self::TUNJ_JABATAN_STANDAR,
            'insentif_kinerja'  => $request->insentif_kinerja ?? 0,
            'tunjangan_program' => $request->tunjangan_program ?? 0,
            'tunjangan_bpjs'    => self::TUNJ_BPJS_STANDAR,
            'bonus'             => $request->bonus ?? 0,
            'lain_lain'         => $request->lain_lain ?? 0,
            'total_penerimaan'  => $totalPenerimaan,
            'potongan_absen'    => $request->potongan_absen ?? 0,
            'cash_bon'          => $request->cash_bon ?? 0,
            'potongan_bpjs'     => self::TUNJ_BPJS_STANDAR,
            'potongan_lain'     => $request->potongan_lain ?? 0,
            'total_gaji'        => $totalGaji,
            'status_slip'       => $request->input('status_slip', $gaji->status_slip),
        ]);

        return redirect()->route('pimpinan.gaji', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Slip gaji berhasil diperbarui dengan standar terbaru.');
    }

    public function finalizeGaji($id)
    {
        $gaji = Penggajian::findOrFail($id);
        $gaji->update(['status_slip' => 'final']);

        return redirect()->route('pimpinan.gaji', ['bulan' => $gaji->bulan, 'tahun' => $gaji->tahun])
            ->with('success', 'Slip gaji berhasil difinalisasi dan dikirim ke karyawan.');
    }

    public function destroyGaji($id)
    {
        $gaji = Penggajian::findOrFail($id);
        $bulan = $gaji->bulan;
        $tahun = $gaji->tahun;
        $gaji->delete();

        return redirect()->route('pimpinan.gaji', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Slip gaji berhasil dihapus.');
    }

    public function destroyKaryawan($id_user)
    {
        $karyawan = Karyawan::where('id_user', $id_user)->first();
        if ($karyawan) {
            $karyawan->delete();
        }
        $user = User::find($id_user);
        if ($user) {
            $user->delete();
        }
        return back()->with('success', 'Akun Karyawan berhasil dihapus secara permanen tanpa merusak Role Master!');
    }

    public function pengaturanLokasi()
    {
        $pengaturan = PengaturanKantor::latest('id_pengaturan')->first();
        return view('pimpinan.pengaturan_lokasi', compact('pengaturan'));
    }

    public function updatePengaturanLokasi(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
        ]);

        $pengaturan = PengaturanKantor::latest('id_pengaturan')->first();
        if ($pengaturan) {
            $pengaturan->update($validated);
        } else {
            PengaturanKantor::create($validated);
        }

        return redirect()->route('pimpinan.pengaturan-lokasi')
            ->with('success', 'Pengaturan lokasi kantor berhasil diperbarui.');
    }

    public function rejectKaryawan($id)
    {
        $user = User::with('karyawan')->findOrFail($id);
        if ($user->karyawan) {
            $user->karyawan->delete();
        }
        $user->delete();
        return redirect()->route('pimpinan.karyawan_pending')->with('error', 'Pengajuan karyawan baru telah ditolak dan dihapus.');
    }

    // FUNGSI AJAX AUTOFILL DI FORM HTML
    public function getKaryawanFinansial($id)
    {
        // Langsung lempar angka dari Constant, tidak perlu cek database karyawan lagi!
        return response()->json([
            'gaji_pokok'        => self::GAJI_POKOK_STANDAR,
            'tunjangan_jabatan' => self::TUNJ_JABATAN_STANDAR,
            'tunjangan_bpjs'    => self::TUNJ_BPJS_STANDAR,
        ]);
    }
}