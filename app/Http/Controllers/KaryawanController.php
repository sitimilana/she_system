<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Penggajian;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    private const BULAN_LIST = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    private function getKaryawan()
    {
        return Karyawan::where('id_user', Auth::user()->id_user)->first();
    }

    public function index()
    {
        $karyawan = $this->getKaryawan();

        $latestSlip = null;
        if ($karyawan) {
            $latestSlip = Penggajian::where('id_karyawan', $karyawan->id_karyawan)
                ->where('status_slip', 'final')
                ->orderByDesc('tahun')
                ->orderByDesc('bulan')
                ->first();
        }

        return view('karyawan.home', [
            'karyawan'   => $karyawan,
            'latestSlip' => $latestSlip,
            'bulanList'  => self::BULAN_LIST,
        ]);
    }

    public function slipGaji()
    {
        $karyawan = $this->getKaryawan();

        $dataSlip = [];
        if ($karyawan) {
            $dataSlip = Penggajian::where('id_karyawan', $karyawan->id_karyawan)
                ->where('status_slip', 'final')
                ->orderByDesc('tahun')
                ->orderByDesc('bulan')
                ->get();
        }

        return view('karyawan.slip_gaji', [
            'karyawan'  => $karyawan,
            'dataSlip'  => $dataSlip,
            'bulanList' => self::BULAN_LIST,
        ]);
    }

    public function slipGajiDetail($id)
    {
        $karyawan = $this->getKaryawan();

        if (!$karyawan) {
            abort(404, 'Data karyawan tidak ditemukan.');
        }

        $slip = Penggajian::where('id_gaji', $id)
            ->where('id_karyawan', $karyawan->id_karyawan)
            ->where('status_slip', 'final')
            ->firstOrFail();

        return view('karyawan.slip_gaji_detail', [
            'karyawan'  => $karyawan,
            'slip'      => $slip,
            'bulanList' => self::BULAN_LIST,
        ]);
    }
}
