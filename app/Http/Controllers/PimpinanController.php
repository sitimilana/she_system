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
use App\Models\Absensi;
use Carbon\Carbon;
use App\Models\Reward;

class PimpinanController extends Controller
{   

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
        $topKaryawan = Reward::with(['karyawan', 'penilaian'])
            ->whereMonth('tanggal_reward', $currentDate->month)
            ->whereYear('tanggal_reward', $currentDate->year)
            ->get()
            ->sortByDesc(function($reward) {
                return $reward->penilaian->total_skor ?? 0;
            })
            ->take(1); 
        if ($topKaryawan->isEmpty()) {
            $lastMonthDate = Carbon::now()->subMonth();
            $topKaryawan = Reward::with(['karyawan', 'penilaian'])
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