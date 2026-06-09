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

        // Base query penilaian yang sudah terfilter berdasarkan bulan, tahun, dan search
        $query = Penilaian::with('karyawan.user.role')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        // 1. Ambil top 1 kandidat
        $topKandidat = (clone $query)
            ->orderBy('total_skor', 'desc')
            ->take(1)
            ->get();

        // 2. [BARU] Ambil daftar penilaian untuk tabel Penilaian Kinerja
        // Menggunakan parameter 'penilaian_page' agar tidak bentrok dengan pagination reward
        $daftarPenilaian = (clone $query)
            ->orderBy('total_skor', 'desc')
            ->paginate(10, ['*'], 'penilaian_page')
            ->appends(request()->except('penilaian_page'));

        // 3. Ambil daftar reward
        // Menggunakan parameter 'reward_page'
        $daftarReward = Reward::with(['karyawan', 'penilaian'])
            ->join('penilaian', 'reward.id_penilaian', '=', 'penilaian.id_penilaian')
            ->select('reward.*')
            ->orderBy('penilaian.tahun', 'desc')
            ->orderBy('penilaian.bulan', 'desc')
            ->orderBy('reward.tanggal_reward', 'desc')
            ->paginate(15, ['*'], 'reward_page')
            ->appends(request()->except('reward_page'));

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $tahunList = range(now()->year, now()->year - 4);

        return view('pimpinan.reward', compact(
            'topKandidat',
            'daftarReward',
            'daftarPenilaian', // <-- Tambahkan variabel ini ke view
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
            'bulan' => 'required|numeric|between:1,12',
            'tahun' => 'required|numeric|digits:4',
            'keterangan' => 'nullable|string'
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Cek apakah reward untuk karyawan ini sudah ada di bulan/tahun yang dipilih
        $exists = Reward::where('id_karyawan', $request->id_karyawan)
                    ->whereMonth('tanggal_reward', $bulan)
                    ->whereYear('tanggal_reward', $tahun)
                    ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Gagal: Karyawan ini sudah diberikan reward pada periode bulan ini.');
        }

        // Buat tanggal reward = hari pertama bulan yang dipilih (atau hari terakhir)
        $tanggalReward = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateString();

        Reward::create([
            'id_karyawan' => $request->id_karyawan,
            'id_penilaian' => $request->id_penilaian,
            'tanggal_reward' => $tanggalReward, // Format YYYY-MM-DD (hari terakhir bulan)
            'keterangan' => $request->keterangan ?? 'Karyawan Terbaik Bulan Ini',
            'diberikan_oleh' => auth()->user()->id_user,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Karyawan telah resmi ditetapkan sebagai penerima Reward.');
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