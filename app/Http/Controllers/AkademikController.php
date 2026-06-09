<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkademikController extends Controller
{
    public function index()
    {
        $today = now();
        $chartPeriod = request('periode_absensi', 'hari');
        $chartPeriods = [
            'hari' => [
                'label' => 'Hari Ini',
                'start' => $today->copy()->startOfDay(),
                'end' => $today->copy()->endOfDay(),
            ],
            'minggu' => [
                'label' => 'Minggu Ini',
                'start' => $today->copy()->startOfWeek(),
                'end' => $today->copy()->endOfWeek(),
            ],
            'bulan' => [
                'label' => 'Bulan Ini',
                'start' => $today->copy()->startOfMonth(),
                'end' => $today->copy()->endOfMonth(),
            ],
        ];

        if (!array_key_exists($chartPeriod, $chartPeriods)) {
            $chartPeriod = 'hari';
        }

        $selectedPeriod = $chartPeriods[$chartPeriod];
    
        $totalKaryawan = \App\Models\Karyawan::count();

        $rekapCuti = \App\Models\Cuti::with('karyawan')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get()
                            ->map(function ($cuti) {
                                return (object)[
                                    'nama' => $cuti->karyawan->nama ?? 'Tidak diketahui',
                                    'tgl_mulai' => $cuti->tanggal_mulai,
                                    'tgl_selesai' => $cuti->tanggal_selesai,
                                    'status' => $cuti->status
                                ];
                            });

        $dataAbsensiPeriode = \App\Models\Absensi::whereBetween('tanggal', [
            $selectedPeriod['start']->toDateString(),
            $selectedPeriod['end']->toDateString(),
        ])->get();

        $hadir = $dataAbsensiPeriode->whereIn('status', ['hadir', 'terlambat'])->count();
        $sakit = $dataAbsensiPeriode->where('status', 'sakit')->count();
        $izin = $dataAbsensiPeriode->where('status', 'izin')->count();
        $alpha = $dataAbsensiPeriode->whereIn('status', ['alpha', 'alfa', 'tidak hadir'])->count();

        $cuti = \App\Models\Cuti::whereIn('status', ['Disetujui', 'approved', 'disetujui_hrd'])
            ->whereDate('tanggal_mulai', '<=', $selectedPeriod['end']->toDateString())
            ->whereDate('tanggal_selesai', '>=', $selectedPeriod['start']->toDateString())
            ->count();

        $rekapAbsensi = [
            'Hadir' => $hadir,
            'Tidak Hadir' => $alpha,
            'Sakit' => $sakit,
            'Izin' => $izin,
            'Cuti' => $cuti
        ];

        return view('akademik.beranda', compact('totalKaryawan', 'hadir', 'rekapCuti', 'rekapAbsensi', 'chartPeriod', 'selectedPeriod'));
    }

    public function absensi(Request $request)
    {
        $query = \App\Models\Absensi::with(['karyawan.user']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('bulan')) {
            $bulan = explode('-', $request->bulan);
            if (count($bulan) == 2) {
                $query->whereYear('tanggal', $bulan[0])
                      ->whereMonth('tanggal', $bulan[1]);
            }
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dataAbsensi = $query->orderBy('tanggal', 'desc')
            ->paginate(15);
        $dataAbsensi->appends($request->query());

        return view('akademik.riwayat_absensi', compact('dataAbsensi'));
    }

    public function cuti(Request $request)
    {
        $query = \App\Models\Cuti::with(['karyawan.user']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('bulan')) {
            $bulan = explode('-', $request->bulan);
            if (count($bulan) == 2) {
                $query->whereYear('tanggal_pengajuan', $bulan[0])
                      ->whereMonth('tanggal_pengajuan', $bulan[1]);
            }
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dataCuti = $query->orderBy('tanggal_pengajuan', 'desc')
            ->paginate(15);
        $dataCuti->appends($request->query());

        return view('akademik.riwayat_cuti', compact('dataCuti'));
    }

    public function karyawan(Request $request)
    {
        $query = \App\Models\User::with(['karyawan', 'role'])
            ->whereHas('role', function ($q) {
                $q->where('nama_role', 'Karyawan')
                ->orWhere('nama_role', 'karyawan');
            });

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhereHas('karyawan', function ($qk) use ($search) {
                    $qk->where('nama', 'like', "%{$search}%")
                        ->orWhere('divisi', 'like', "%{$search}%");
                });
            });
        }


        // Urutkan nama karyawan A-Z
        $query->join('karyawan', 'user.id_user', '=', 'karyawan.id_user')
            ->orderBy('karyawan.nama', 'asc')
            ->select('user.*');


        $dataKaryawan = $query->paginate(15);

        $dataKaryawan->appends($request->query());


        return view('akademik.manajemen_karyawan', compact('dataKaryawan'));
    }

    public function cetakKaryawan(Request $request)
    {
        $query = \App\Models\User::with(['karyawan', 'role'])
            ->whereHas('role', function ($q) {
                $q->where('nama_role', 'Karyawan')->orWhere('nama_role', 'karyawan');
            });

        // Tetap bawa filter pencarian saat dicetak
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function ($qk) use ($search) {
                      $qk->where('nama', 'like', "%{$search}%")
                         ->orWhere('divisi', 'like', "%{$search}%");
                  });
            });
        }

        // Ambil semua data (bukan paginate) untuk dicetak
        $dataKaryawan = $query->get();

        return view('akademik.cetak_karyawan', compact('dataKaryawan'));
    }
    
    public function cetakAbsensi(Request $request)
    {
        $query = \App\Models\Absensi::with('karyawan');

    
        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }
        if ($request->filled('bulan')) {

            $periode = explode('-', $request->bulan);

            if (count($periode) == 2) {

                $tahun = $periode[0];
                $bulan = $periode[1];

                $query->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan);
            }
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dataAbsensi = $query->orderBy('tanggal', 'desc')->get();

        $jumlahHadir = $dataAbsensi->where('status', 'hadir')->count();

        $jumlahTerlambat = $dataAbsensi->where('status', 'terlambat')->count();

        $jumlahIzin = $dataAbsensi->where('status', 'izin')->count();

        $jumlahSakit = $dataAbsensi->where('status', 'sakit')->count();

        $jumlahAlfa = $dataAbsensi->where('status', 'alfa')->count();

        $jumlahCuti = $dataAbsensi->where('status', 'cuti')->count();

        return view('akademik.cetak_absensi', compact(
            'dataAbsensi',
            'jumlahHadir',
            'jumlahTerlambat',
            'jumlahIzin',
            'jumlahSakit',
            'jumlahAlfa',
            'jumlahCuti'
        ));
    }

    public function hariLibur()
    {
        $hariLibur = DB::table('hari_libur')->orderBy('tanggal', 'desc')->get();
        return view('akademik.create_hari_libur', compact('hariLibur'));
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
        return redirect()->route('akademik.create_hari_libur')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroyHariLibur($id)
    {
        DB::table('hari_libur')->where('id_libur', $id)->delete();
        return redirect()->route('akademik.create_hari_libur')->with('success', 'Hari libur berhasil dihapus.');
    }
}