<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Cuti;
use App\Models\Penilaian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\KaryawanBaruMail;

class KepalaBagianController extends Controller
{
    public function index()
    {
        $jumlahKaryawan = Karyawan::where('status_karyawan', 'aktif')->count();
        $penilaian = Penilaian::latest()->take(2)->get();
        $karyawan = Karyawan::latest()->take(5)->get();

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
            'karyawanTidakAktif',
            'evaluasiSelesai',
            'progressPenilaian'
        ));
    }

    public function karyawan(Request $request)
    {
        $search = $request->input('search');

        $query = User::with('karyawan')
        ->whereHas('role', function ($query) {
            $query->where('nama_role', 'Karyawan')->orWhere('nama_role', 'karyawan');
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function($qKar) use ($search) {
                      $qKar->where('divisi', 'like', "%{$search}%")
                           ->orWhere('status_karyawan', 'like', "%{$search}%");
                  });
            });
        }

        $dataKaryawan = $query->paginate(15)->withQueryString();
        $roles = \App\Models\Role::all();

        return view('kepala_bagian.kelola_karyawan', compact('dataKaryawan', 'roles'));
    }

    // YANG DIUBAH: Menambahkan Request $request serta logika filter nama, bulan, & tahun
    public function penilaian(Request $request)
    {
        $karyawan = Karyawan::where('status_karyawan', 'aktif')
            ->select('id_karyawan', 'nama')
            ->orderBy('nama')
            ->get();

        $query = Penilaian::with('karyawan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        if ($request->filled('periode')) {
            $periode = explode('-', $request->periode);
            if (count($periode) == 2) {
                $query->where('tahun', $periode[0])
                      ->where('bulan', (int)$periode[1]);
            }
        }

        $riwayatPenilaian = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('kepala_bagian.penilaian_kinerja', compact('karyawan', 'riwayatPenilaian'));
    }

    public function storePenilaian(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'periode'     => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'disiplin'    => 'required|integer|min:1|max:5',
            'produktivitas' => 'required|integer|min:1|max:5',
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'sikap_kerja' => 'required|integer|min:1|max:5',
            'loyalitas'   => 'required|integer|min:1|max:5',
            'catatan_evaluasi' => 'nullable|string',
        ]);

        [$tahun, $bulan] = explode('-', $validated['periode']);

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
        
        $skorAkhir = (int) round($skorTertimbang * 20);

        Penilaian::create([
            'id_karyawan' => $validated['id_karyawan'],
            'bulan' => $bulan,
            'tahun' => $tahun,
            'disiplin' => $validated['disiplin'],
            'produktivitas' => $validated['produktivitas'],
            'tanggung_jawab' => $validated['tanggung_jawab'],
            'sikap_kerja' => $validated['sikap_kerja'],
            'loyalitas' => $validated['loyalitas'],
            'total_skor' => $skorAkhir,
            'catatan_evaluasi' => $validated['catatan_evaluasi'] ?? null,
            'dinilai_oleh' => auth()->user()->id_user,
        ]);

        return redirect()->route('kabag.penilaian')->with('success', 'Penilaian kinerja skala kuisioner berhasil disimpan dan dikonversi!');
    }

    public function showPenilaian($id)
    {
        try {
            $penilaian = Penilaian::with(['karyawan', 'penilai'])->findOrFail($id);
            
            $monthName = \Carbon\Carbon::create()->month($penilaian->bulan)->locale('id')->translatedFormat('F');
            $periode = "$monthName {$penilaian->tahun}";
            // Fallback for updated_at if null
            $tanggalPenilaian = $penilaian->updated_at ? $penilaian->updated_at->locale('id')->translatedFormat('d F Y H:i') : '-';

            // Format penilai
            $penilaiNama = 'Sistem';
            if ($penilaian->penilai) {
                $penilaiNama = $penilaian->penilai->nama_lengkap ?? 'Tidak diketahui';
            }

            return response()->json([
                'success' => true,
                'penilaian' => [
                    'id_penilaian' => $penilaian->id_penilaian,
                    'bulan' => sprintf("%02d", $penilaian->bulan),
                    'tahun' => $penilaian->tahun,
                    'disiplin' => $penilaian->disiplin,
                    'produktivitas' => $penilaian->produktivitas,
                    'tanggung_jawab' => $penilaian->tanggung_jawab,
                    'sikap_kerja' => $penilaian->sikap_kerja,
                    'loyalitas' => $penilaian->loyalitas,
                    'total_skor' => $penilaian->total_skor,
                    'catatan_evaluasi' => $penilaian->catatan_evaluasi,
                    'karyawan' => [
                        'nama' => $penilaian->karyawan->nama ?? 'Tidak Ditemukan'
                    ],
                    'penilai' => [
                        'nama_lengkap' => $penilaiNama
                    ]
                ],
                'periode' => $periode,
                'tanggal_penilaian' => $tanggalPenilaian
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data penilaian tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function updatePenilaian(Request $request, $id)
    {
        $penilaian = Penilaian::findOrFail($id);
        
        $validated = $request->validate([
            'periode'     => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'disiplin'    => 'required|integer|min:1|max:5',
            'produktivitas' => 'required|integer|min:1|max:5',
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'sikap_kerja' => 'required|integer|min:1|max:5',
            'loyalitas'   => 'required|integer|min:1|max:5',
            'catatan_evaluasi' => 'nullable|string',
        ]);

        [$tahun, $bulan] = explode('-', $validated['periode']);

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
        
        $skorAkhir = (int) round($skorTertimbang * 20);

        $penilaian->update([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'disiplin' => $validated['disiplin'],
            'produktivitas' => $validated['produktivitas'],
            'tanggung_jawab' => $validated['tanggung_jawab'],
            'sikap_kerja' => $validated['sikap_kerja'],
            'loyalitas' => $validated['loyalitas'],
            'total_skor' => $skorAkhir,
            'catatan_evaluasi' => $validated['catatan_evaluasi'] ?? null,
        ]);

        return redirect()->route('kabag.penilaian')->with('success', 'Data penilaian kinerja berhasil diperbarui!');
    }

    public function destroyPenilaian($id)
    {
        $penilaian = Penilaian::findOrFail($id);
        $namakaryawan = $penilaian->karyawan->nama ?? 'Karyawan';

        \App\Models\Reward::where('id_penilaian', $id)->delete();
        $penilaian->delete();

        return redirect()->route('kabag.penilaian')->with('success', "Penilaian kinerja untuk {$namakaryawan} beserta data reward terkait berhasil dihapus.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|unique:user,username',
            'role_id'      => 'required|exists:roles,role_id',
            'tanggal_masuk' => 'required|date',
            'sisa_cuti'    => 'required|integer|min:0',
            'no_hp'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'alamat'       => 'nullable|string',
            'divisi'       => 'nullable|in:keuangan,admin umum,akademik,marketing,office boy',
        ]);

        $defaultPassword = 'shekediri123';

        $user = \App\Models\User::create([
            'nama_lengkap'       => $request->nama_lengkap,
            'username'           => $request->username,
            'password'           => \Illuminate\Support\Facades\Hash::make($defaultPassword), // Enkripsi sandi default
            'password_sementara' => $defaultPassword, // Opsional: Simpan di kolom sementara agar mudah dicek jika ada
            'role_id'            => $request->role_id,
            'status_akun'        => 'pending',
        ]);

        Karyawan::create([
            'id_user'         => $user->id_user,
            'nama'            => $request->nama_lengkap,
            'no_hp'           => $request->no_hp ?? '-',
            'email'           => $request->email ?? '-',
            'alamat'          => $request->alamat ?? '-',
            'status_karyawan' => 'pending',
            'divisi'          => $request->divisi,
            'tanggal_masuk'   => $request->tanggal_masuk,
            'sisa_cuti'       => $request->sisa_cuti ?? 12,
        ]);

        return redirect()->route('kabag.karyawan')->with('success', 'Karyawan berhasil didaftarkan dan menunggu persetujuan Pimpinan. Password default karyawan: ' . $defaultPassword);
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
            ->with('success', 'Pengajuan cuti disetujui and diteruskan ke Pimpinan.');
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

    public function updateKaryawan(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap'    => 'required|string|max:255',
            'divisi'          => 'required|string',
            'tanggal_masuk'   => 'required|date',
            'sisa_cuti'       => 'required|integer|min:0',
            'no_hp'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'alamat'          => 'nullable|string',
            'status_karyawan' => 'required|string'
        ]);

        $statusDB = strtolower($request->status_karyawan);
        if ($statusDB === 'tidak aktif') {
            $statusDB = 'keluar';
        }

        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
            'status_akun'  => $statusDB === 'keluar' ? 'nonaktif' : $user->status_akun,
        ]);

        if ($user->karyawan) {
            $user->karyawan->update([
                'nama'            => $request->nama_lengkap,
                'divisi'          => $request->divisi,
                'tanggal_masuk'   => $request->tanggal_masuk,
                'sisa_cuti'       => $request->sisa_cuti,
                'no_hp'           => $request->no_hp ?? '-',
                'email'           => $request->email ?? '-',
                'alamat'          => $request->alamat ?? '-',
                'status_karyawan' => $statusDB,
            ]);
        }

        return redirect()->route('kabag.karyawan')->with('success', 'Data dan status karyawan berhasil diperbarui.');
    }

    public function destroyKaryawan($id)
    {
        $user = User::with('karyawan')->findOrFail($id);
        
        $status = strtolower($user->karyawan->status_karyawan ?? '');
        if (!in_array($status, ['tidak aktif', 'keluar', 'resign'])) {
            return redirect()->route('kabag.karyawan')->with('error', 'Gagal! Hanya karyawan berstatus Tidak Aktif atau Keluar yang boleh dihapus.');
        }

        if ($user->karyawan) {
            $id_karyawan = $user->karyawan->id_karyawan;
            \App\Models\Absensi::where('id_karyawan', $id_karyawan)->delete();
            \App\Models\Cuti::where('id_karyawan', $id_karyawan)->delete();
            \App\Models\Penilaian::where('id_karyawan', $id_karyawan)->delete();
            \App\Models\Penggajian::where('id_karyawan', $id_karyawan)->delete();
            \App\Models\Reward::where('id_karyawan', $id_karyawan)->delete();

            $user->karyawan->delete();
        }
        $user->delete();

        return redirect()->route('kabag.karyawan')->with('success', 'Data karyawan berstatus tidak aktif beserta data terkait (absensi, cuti, dl) berhasil dihapus permanen.');
    }
    public function cetakKaryawan(Request $request)
    {
        $search = $request->input('search');

        $query = User::with('karyawan')
        ->whereHas('role', function ($query) {
            $query->where('nama_role', 'Karyawan')
                ->orWhere('nama_role', 'karyawan');
        });
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhereHas('karyawan', function($qKar) use ($search) {
                    $qKar->where('divisi', 'like', "%{$search}%")
                        ->orWhere('status_karyawan', 'like', "%{$search}%");
                });
            });
        }
        $dataKaryawan = $query->orderBy('nama_lengkap', 'asc')->get();
        $jumlahAktif = $dataKaryawan->filter(function ($user) {
            return strtolower($user->karyawan->status_karyawan ?? '') == 'aktif';
        })->count();

        $jumlahPending = $dataKaryawan->filter(function ($user) {
            return strtolower($user->karyawan->status_karyawan ?? '') == 'pending';
        })->count();

        $jumlahKeluar = $dataKaryawan->filter(function ($user) {
            return strtolower($user->karyawan->status_karyawan ?? '') == 'keluar';
        })->count();

        return view('kepala_bagian.cetak_karyawan', compact(
            'dataKaryawan',
            'search',
            'jumlahAktif',
            'jumlahPending',
            'jumlahKeluar'
        ));
    }
    public function resetPassword($id)
    {
        // Cari user berdasarkan ID
        $user = \App\Models\User::findOrFail($id);
        
        // Set password kembali ke default (misal: shekediri123)
        $defaultPassword = 'shekediri123';
        
        $user->password = \Illuminate\Support\Facades\Hash::make($defaultPassword);
        $user->password_sementara = $defaultPassword; // Agar bisa dilihat di tabel jika Anda menampilkan kolom ini
        $user->save();

        return redirect()->back()->with('success', 'Password karyawan berhasil direset menjadi: ' . $defaultPassword);
    }

    public function riwayatAbsensi(Request $request)
    {
        $query = \App\Models\Absensi::with('karyawan')->orderBy('tanggal', 'desc');

        // Filter berdasarkan pencarian nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status (Hadir, Terlambat, Alfa, Izin)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $dataAbsensi = $query->paginate(15)->withQueryString();
        
        return view('kepala_bagian.riwayat_absensi', compact('dataAbsensi'));
    }

    public function riwayatCuti(Request $request)
    {
        $query = \App\Models\Cuti::with('karyawan')->orderBy('tanggal_pengajuan', 'desc');

        // Filter pencarian nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori/jenis_cuti (Cuti, Izin, Sakit, Cuti Kehamilan)
        if ($request->filled('kategori')) {
            $query->where('jenis_cuti', $request->kategori);
        }

        // Filter berdasarkan bulan (pada tanggal pengajuan)
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pengajuan', $request->bulan);
        }

        // Filter berdasarkan tahun (pada tanggal pengajuan)
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pengajuan', $request->tahun);
        }

        $dataCuti = $query->paginate(15)->withQueryString();
        
        return view('kepala_bagian.riwayat_cuti', compact('dataCuti'));
    }
}