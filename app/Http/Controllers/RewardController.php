<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilaian;
use App\Models\Reward;
 // Pastikan model Reward di-import

class RewardController extends Controller
{
    /**
     * Menampilkan halaman utama Reward & Recognition
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $search = $request->input('search');

        $query = Penilaian::with('karyawan.user.role')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $topKandidat = (clone $query)
            ->orderBy('total_skor', 'desc')
            ->take(1)
            ->get();

        // TAMBAHKAN INI
        $daftarReward = Reward::with(['karyawan', 'penilaian'])
            ->latest()
            ->paginate(15);

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tahunList = range(now()->year, now()->year - 4);

        return view('pimpinan.reward', compact(
            'topKandidat',
            'daftarReward',
            'bulan',
            'tahun',
            'bulanList',
            'tahunList',
            'search'
        ));
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required',
            'id_penilaian' => 'required',
            'keterangan' => 'nullable|string'
        ]);

        $exists = Reward::where('id_karyawan', $request->id_karyawan)
                    ->whereMonth('tanggal_reward', now()->month)
                    ->whereYear('tanggal_reward', now()->year)
                    ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Gagal: Karyawan ini sudah dieksekusi/diberikan reward pada bulan ini.');
        }

        Reward::create([
            'id_karyawan' => $request->id_karyawan,
            'id_penilaian' => $request->id_penilaian,
            'tanggal_reward' => now()->toDateString(), // Format YYYY-MM-DD
            'keterangan' => $request->keterangan ?? 'Karyawan Terbaik Bulan Ini',
            'diberikan_oleh' => auth()->user()->id_user,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Karyawan telah resmi ditetapkan sebagai penerima Reward bulan ini.');
    }
    
    public function getRewards()
    {
        $rewards = Reward::with(['karyawan', 'penilaian'])
            ->latest()
            ->get();

        $data = $rewards->map(function ($item) {

            return [
                'id_reward' => $item->id_reward,
                'nama_karyawan' => $item->karyawan->nama ?? '-',
                'tanggal_reward' => $item->tanggal_reward,
                'keterangan' => $item->keterangan,
                'total_skor' => $item->penilaian->total_skor ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data reward berhasil diambil',
            'data' => $data
        ]);
    }

}