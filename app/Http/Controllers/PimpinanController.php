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

    public function karyawanPending()
    {
        $users = User::with('role', 'karyawan')
            ->where('status_akun', 'pending')
            ->paginate(5, ['*'], 'pending_page');

        $approvedUsers = User::with('role', 'karyawan')
            ->where('status_akun', 'aktif')
            ->whereHas('karyawan')
            ->orderBy('updated_at', 'desc')
            ->paginate(5, ['*'], 'approved_page');

        return view('pimpinan.karyawan_pending', compact('users', 'approvedUsers'));
    }

    public function approveKaryawan($id)
    {
        /** @var User $user */
        $user = User::with('karyawan')->findOrFail($id);
        
        // VALIDASI: Pastikan karyawan memiliki tanggal_masuk
        if (!$user->karyawan || !$user->karyawan->tanggal_masuk) {
            return redirect()->back()->with('error', 'Tanggal masuk karyawan belum diisi. Silakan lengkapi data terlebih dahulu.');
        }
        
        $user->status_akun = 'aktif';
        
        if ($user->karyawan) {
            $user->karyawan->status_karyawan = 'aktif';
            $user->karyawan->save();

            if($user->karyawan->email && $user->karyawan->email != '-') {
                try {
                    Mail::to($user->karyawan->email)->send(new KaryawanApproveMail($user));
                } catch (\Exception $e) {}
            }
        }
        
        $user->password_sementara = null;
        $user->save();

        return redirect()->route('pimpinan.karyawan_pending')->with('success', 'Akun & Profil karyawan berhasil diaktifkan.');
    }

    public function index(Request $request)
    {
        $totalKaryawan = Karyawan::where('status_karyawan', 'aktif')->count();
        
        $karyawanCutiHariIni = Cuti::where('status', 'approved')
            ->whereDate('tanggal_mulai', '<=', Carbon::today())
            ->whereDate('tanggal_selesai', '>=', Carbon::today())
            ->count();
        
        $karyawanTanpaCuti = Karyawan::where('sisa_cuti', 0)->where('status_karyawan', 'aktif')->count();
        $totalSaldoCutiPerusahaan = Karyawan::where('status_karyawan', 'aktif')->sum('sisa_cuti');

        $filterKehadiran = $request->input('filter_kehadiran', 'hari_ini');
        $queryAbsensi = Absensi::query();

        if ($filterKehadiran == 'hari_ini') {
            $queryAbsensi->whereDate('tanggal', Carbon::today());
        } elseif ($filterKehadiran == 'minggu_ini') {
            $queryAbsensi->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filterKehadiran == 'bulan_ini') {
            $queryAbsensi->whereMonth('tanggal', Carbon::now()->month)->whereYear('tanggal', Carbon::now()->year);
        }

        $jmlHadir = (clone $queryAbsensi)->where('status', 'hadir')->count();
        $jmlTerlambat = (clone $queryAbsensi)->where('status', 'terlambat')->count();
        $jmlAlpha = (clone $queryAbsensi)->whereIn('status', ['alfa', 'alpha'])->count(); 
        $jmlCuti = (clone $queryAbsensi)->whereIn('status', ['cuti', 'izin', 'sakit'])->count();
        $totalBebanGaji = Penggajian::whereMonth('tanggal_dibuat', now()->month)
            ->whereYear('tanggal_dibuat', now()->year)
            ->sum('total_gaji');
        $totalBebanGaji = number_format($totalBebanGaji ?: 150000000, 0, ',', '.');
        $cutiTerbaru = Cuti::with('karyawan')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        $currentDate = Carbon::now();
        $bulanTopPerformer = $currentDate->translatedFormat('F Y');
        $topKaryawan = \App\Models\Reward::with(['karyawan', 'penilaian'])
            ->whereMonth('tanggal_reward', $currentDate->month)
            ->whereYear('tanggal_reward', $currentDate->year)
            ->get()
            ->sortByDesc(function($reward) {
                return $reward->penilaian->total_skor ?? 0;
            })
            ->take(1); 
        if ($topKaryawan->isEmpty()) {
            $lastMonthDate = Carbon::now()->subMonth();
            $topKaryawan = \App\Models\Reward::with(['karyawan', 'penilaian'])
                ->whereMonth('tanggal_reward', $lastMonthDate->month)
                ->whereYear('tanggal_reward', $lastMonthDate->year)
                ->get()
                ->sortByDesc(function($reward) {
                    return $reward->penilaian->total_skor ?? 0;
                })
                ->take(1);
            if ($topKaryawan->isNotEmpty()) {
                $bulanTopPerformer = $lastMonthDate->translatedFormat('F Y');
            }
        }

        $karyawanSudahDigaji = Penggajian::where('bulan', now()->month)
            ->where('tahun', now()->year)
            ->distinct('id_karyawan')
            ->count('id_karyawan');
        $belumDigaji = max(0, $totalKaryawan - $karyawanSudahDigaji);

        $karyawanPending = User::where('status_akun', 'pending')->count();
// Jangan lupa tambahkan 'bulanTopPerformer' ke compact()
        return view('pimpinan.dashboard', compact(
            'totalKaryawan', 'karyawanCutiHariIni', 'totalBebanGaji', 'cutiTerbaru', 'topKaryawan',
            'jmlHadir', 'jmlTerlambat', 'jmlAlpha', 'jmlCuti', 'filterKehadiran', 'karyawanTanpaCuti', 
            'totalSaldoCutiPerusahaan', 'belumDigaji', 'karyawanPending', 'bulanTopPerformer'
        ));
    }

    public function cuti(Request $request)
    {
        $query = Cuti::with('karyawan')->where('status', 'pending_pimpinan');
        $queryRiwayat = Cuti::with('karyawan')->whereIn('status', ['approved', 'rejected']);

        if ($request->filled('search')) {
            $search = $request->search;
            $karyawanFilter = function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('divisi', 'like', "%{$search}%");
            };
            $query->whereHas('karyawan', $karyawanFilter);
            $queryRiwayat->whereHas('karyawan', $karyawanFilter);
        }

        if ($request->filled('jenis_cuti')) {
            $query->where('jenis_cuti', $request->jenis_cuti);
            $queryRiwayat->where('jenis_cuti', $request->jenis_cuti);
        }

        $dataCuti = $query
            ->paginate(10, ['*'], 'page')
            ->appends(request()->query());

        $riwayatCuti = $queryRiwayat
            ->paginate(10, ['*'], 'riwayat_page')
            ->appends(request()->query());

        return view('pimpinan.manajemen_cuti', compact('dataCuti', 'riwayatCuti'));
    }

    public function approve(Request $request, $id)
    {
        // 1. Ambil data pengajuan beserta relasi karyawan
        $cuti = Cuti::with('karyawan')->findOrFail($id);
        
        // 2. Hitung durasi hari secara riil berdasarkan tanggal pengajuan
        $mulai      = \Carbon\Carbon::parse($cuti->tanggal_mulai);
        $selesai    = \Carbon\Carbon::parse($cuti->tanggal_selesai);
        $jumlahHari = $mulai->diffInDays($selesai) + 1;

        try {
            // 3. Gunakan DB::transaction untuk mengamankan perubahan data saldo kuota
            DB::transaction(function () use ($cuti, $jumlahHari, $request) {
                
                // Jika jenis perizinannya adalah murni 'Cuti', lakukan pemotongan kuota saldo
                if (strtolower($cuti->jenis_cuti) === 'cuti' && $cuti->karyawan) {
                    
                    // Kunci baris data karyawan di database agar tidak dimanipulasi request lain saat ini
                    $karyawan = Karyawan::lockForUpdate()->findOrFail($cuti->id_karyawan);
                    
                    // Validasi ulang kuota di sisi server sebelum memotong
                    if ($karyawan->sisa_cuti < $jumlahHari) {
                        throw new \RuntimeException('insufficient_quota');
                    }
                    
                    // Potong kuota jatah cuti secara dinamis sesuai total hari ($jumlahHari)
                    $karyawan->update(['sisa_cuti' => $karyawan->sisa_cuti - $jumlahHari]);
                }

                // Update status pengajuan cuti menjadi disetujui (Approved)
                $cuti->update([
                    'status' => 'approved',
                    'keterangan_pimpinan' => $request->keterangan_pimpinan ?? 'Disetujui',
                    'disetujui_oleh' => auth()->id()
                ]);
            });

        } catch (\RuntimeException $e) {
            // Tangkap error jika kuota tidak mencukupi saat divalidasi server
            if ($e->getMessage() === 'insufficient_quota') {
                return redirect()->back()->with('error', 'Saldo cuti karyawan tidak mencukupi.');
            }
            throw $e;
        }

        // 4. Kirim notifikasi email otomatis setelah transaksi database sukses diselesaikan
        if ($cuti->karyawan && $cuti->karyawan->email) {
            try {
                Mail::to($cuti->karyawan->email)->send(new \App\Mail\StatusCutiMail($cuti, 'Disetujui'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal mengirim email approve: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    public function rejectCuti($id)
    {
        $cuti = Cuti::where('id_cuti', $id)->where('status', 'pending_pimpinan')->firstOrFail();
        $cuti->update(['status' => 'rejected', 'disetujui_oleh'=> auth()->id()]);
        return redirect()->route('pimpinan.cuti')->with('success', 'Pengajuan cuti ditolak.');
    }

    public function gaji(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $search = $request->input('search');

        $query = Penggajian::with('karyawan')->where('bulan', $bulan)->where('tahun', $tahun);
        
        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        // PERUBAHAN DI SINI: Mengganti get() menjadi paginate(10)
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

        $karyawan = Karyawan::with('user.role')->find($request->id_karyawan);
        $gajiPokok = $this->getGajiPokok($karyawan);

        // PENERIMAAN (Semua Menggunakan Input Form)
        $totalPenerimaan = $gajiPokok
            + (float) ($request->uang_makan ?? 0)
            + (float) ($request->tunjangan_jabatan ?? 0)
            + (float) ($request->insentif_kinerja ?? 0)
            + (float) ($request->tunjangan_program ?? 0)
            + (float) ($request->bonus ?? 0)
            + (float) ($request->lain_lain ?? 0);

        // POTONGAN (Termasuk Konstanta BPJS)
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
            'tunjangan_bpjs'    => 0, // 0 Karena Murni Potongan
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

    public function destroyKaryawan($id_user)
    {
        $karyawan = Karyawan::where('id_user', $id_user)->first();
        if ($karyawan) $karyawan->delete();
        $user = User::find($id_user);
        if ($user) $user->delete();
        return back()->with('success', 'Akun Karyawan berhasil dihapus!');
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
            'radius' => 'required|numeric|min:0.001',
        ]);
        $pengaturan = PengaturanKantor::latest('id_pengaturan')->first();
        if ($pengaturan) $pengaturan->update($validated);
        else PengaturanKantor::create($validated);

        return redirect()->route('pimpinan.pengaturan-lokasi')->with('success', 'Pengaturan lokasi berhasil diperbarui.');
    }

    public function rejectKaryawan($id)
    {
        $user = User::with('karyawan')->findOrFail($id);
        if ($user->karyawan) $user->karyawan->delete();
        $user->delete();
        return redirect()->route('pimpinan.karyawan_pending')->with('error', 'Pengajuan karyawan ditolak.');
    }
    public function getKaryawanFinansial(Request $request, $id)
    {
        $karyawan = Karyawan::with('user.role')->find($id);
        $bulan = $request->query('bulan', now()->month);
        $tahun = $request->query('tahun', now()->year);
        
        $gajiPokok = $this->getGajiPokok($karyawan);

        // 1. Tunjangan Kinerja (Dari Nilai Kinerja)
        $penilaian = Penilaian::where('id_karyawan', $id)->where('bulan', $bulan)->where('tahun', $tahun)->first();
        $insentifKinerja = 0;
        if ($penilaian) {
            if ($penilaian->total_skor >= 90) {
                $insentifKinerja = 150000;
            } elseif ($penilaian->total_skor >= 80) {
                $insentifKinerja = 100000;
            }
        }

        // 2. Bonus Rank 1 (Tertinggi bulan ini = Rp 100.000)
        $bonus = 0;
        $topScore = Penilaian::where('bulan', $bulan)->where('tahun', $tahun)->max('total_skor');
        if ($topScore !== null && $penilaian && $penilaian->total_skor == $topScore) {
            // Pencarian tie-breaker (Jika skor sama, cari yang hadirnya terbanyak)
            $topKaryawans = Penilaian::where('bulan', $bulan)->where('tahun', $tahun)->where('total_skor', $topScore)->pluck('id_karyawan')->toArray();
            $maxHadir = -1;
            $bestKaryawanId = null;
            
            foreach ($topKaryawans as $kid) {
                $hadirCount = Absensi::where('id_karyawan', $kid)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'hadir')->count();
                if ($hadirCount > $maxHadir) {
                    $maxHadir = $hadirCount;
                    $bestKaryawanId = $kid;
                }
            }
            if ($bestKaryawanId == $id) {
                $bonus = 100000; // Bonus Karyawan Terbaik Rp 100.000
            }
        }

        // 3. Perhitungan Absensi
        $hadir = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'hadir')->count();
        $alpha = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->whereIn('status', ['alfa', 'alpha'])->count();
        $izin = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'izin')->count();
        $cuti = Absensi::where('id_karyawan', $id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'cuti')->count();

        // Uang Makan: 20rb x Hari Hadir
        $uangMakan = $hadir * 20000;
        
        // Logika Potongan Absen (Rp 70.000 per hari mbolos)
        // Alpha & Izin = potong. Cuti = hari 1 gratis, hari 2 dst dipotong. Sakit = Gratis.
        $hariPotong = $alpha + $izin; 
        if ($cuti > 1) {
            $hariPotong += ($cuti - 1);
        }
        $potonganAbsen = $hariPotong * 70000;

        return response()->json([
            'gaji_pokok'        => $gajiPokok,
            'tunjangan_jabatan' => self::TUNJ_JABATAN_STANDAR,
            'insentif_kinerja'  => $insentifKinerja,
            'uang_makan'        => $uangMakan,
            'potongan_absen'    => $potonganAbsen,
            'potongan_bpjs'     => self::TUNJ_BPJS_STANDAR,
            'bonus'             => $bonus, // Dikirim otomatis ke kolom bonus
        ]);
    }

    public function hariLibur()
    {
        $hariLibur = DB::table('hari_libur')->orderBy('tanggal', 'desc')->get();
        return view('pimpinan.hari_libur', compact('hariLibur'));
    }

    public function storeHariLibur(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date|unique:hari_libur,tanggal',
            'keterangan' => 'required|string|max:255',
        ], ['tanggal.unique' => 'Tanggal ini sudah didaftarkan sebagai hari libur.']);

        DB::table('hari_libur')->insert([
            'tanggal'    => $request->tanggal,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->route('pimpinan.hari_libur')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroyHariLibur($id)
    {
        DB::table('hari_libur')->where('id_libur', $id)->delete();
        return redirect()->route('pimpinan.hari_libur')->with('success', 'Hari libur berhasil dihapus.');
    }
}