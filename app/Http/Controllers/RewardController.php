<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilaian;

class RewardController extends Controller
{
    /**
     * Menampilkan halaman utama Reward & Recognition
     */
    public function index()
    {
        // Mengambil data penilaian dan mengurutkannya berdasarkan skor tertinggi
        $daftarReward = Penilaian::with('karyawan.user.role')
            ->orderBy('total_skor', 'desc')
            ->get();

        // Mengambil 1 teratas sebagai Top Kandidat
        $topKandidat = $daftarReward->take(1);

        return view('pimpinan.reward', compact('topKandidat', 'daftarReward'));
    }
}