<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Cuti;
use App\Models\Penggajian;
use App\Models\PengaturanKantor;
use Illuminate\Support\Facades\DB;

class PimpinanController extends Controller
{
    public function index()
    {
        // 1. Metrik Utama
        $totalKaryawan = Karyawan::where('status_karyawan', 'aktif')->count();
        $karyawanCutiHariIni = 3;

        // 2. Metrik Finansial
        $totalBebanGaji = Penggajian::whereMonth('tanggal_dibuat', now()->month)
            ->whereYear('tanggal_dibuat', now()->year)
            ->sum('total_gaji');
        $totalBebanGaji = number_format($totalBebanGaji ?: 150000000, 0, ',', '.');

        // 3. Operasional
        $cutiTerbaru = [];

        // 4. Kinerja/Reward
        $topKaryawan = [];

        return view('pimpinan.dashboard', compact(
            'totalKaryawan',
            'karyawanCutiHariIni',
            'totalBebanGaji',
            'cutiTerbaru',
            'topKaryawan'
        ));
    }

    public function cuti(Request $request)
    {
        $query = Cuti::with('karyawan')
            ->where('status', 'pending_pimpinan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_cuti')) {
            $query->where('jenis_cuti', $request->jenis_cuti);
        }

        $dataCuti = $query->orderByDesc('tanggal_pengajuan')->paginate(10)->withQueryString();

        $riwayatCuti = Cuti::with('karyawan')
            ->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('updated_at')
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

        $dataGaji = Penggajian::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

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
            'gaji_pokok'        => 'required|numeric|min:0',
            'uang_makan'        => 'nullable|numeric|min:0',
            'tunjangan_jabatan' => 'nullable|numeric|min:0',
            'insentif_kinerja'  => 'nullable|numeric|min:0',
            'tunjangan_program' => 'nullable|numeric|min:0',
            'tunjangan_bpjs'    => 'nullable|numeric|min:0',
            'bonus'             => 'nullable|numeric|min:0',
            'lain_lain'         => 'nullable|numeric|min:0',
            'potongan_absen'    => 'nullable|numeric|min:0',
            'cash_bon'          => 'nullable|numeric|min:0',
            'potongan_bpjs'     => 'nullable|numeric|min:0',
            'potongan_lain'     => 'nullable|numeric|min:0',
        ]);

        [$tahun, $bulan] = $this->parsePeriode($request->periode);

        $totalPenerimaan = (float)($request->gaji_pokok ?? 0)
            + (float)($request->uang_makan ?? 0)
            + (float)($request->tunjangan_jabatan ?? 0)
            + (float)($request->insentif_kinerja ?? 0)
            + (float)($request->tunjangan_program ?? 0)
            + (float)($request->tunjangan_bpjs ?? 0)
            + (float)($request->bonus ?? 0)
            + (float)($request->lain_lain ?? 0);

        $totalPotongan = (float)($request->potongan_absen ?? 0)
            + (float)($request->cash_bon ?? 0)
            + (float)($request->potongan_bpjs ?? 0)
            + (float)($request->potongan_lain ?? 0);

        $totalGaji = $totalPenerimaan - $totalPotongan;

        Penggajian::create([
            'id_karyawan'       => $request->id_karyawan,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => $request->gaji_pokok ?? 0,
            'uang_makan'        => $request->uang_makan ?? 0,
            'tunjangan_jabatan' => $request->tunjangan_jabatan ?? 0,
            'insentif_kinerja'  => $request->insentif_kinerja ?? 0,
            'tunjangan_program' => $request->tunjangan_program ?? 0,
            'tunjangan_bpjs'    => $request->tunjangan_bpjs ?? 0,
            'bonus'             => $request->bonus ?? 0,
            'lain_lain'         => $request->lain_lain ?? 0,
            'total_penerimaan'  => $totalPenerimaan,
            'potongan_absen'    => $request->potongan_absen ?? 0,
            'cash_bon'          => $request->cash_bon ?? 0,
            'potongan_bpjs'     => $request->potongan_bpjs ?? 0,
            'potongan_lain'     => $request->potongan_lain ?? 0,
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
            'gaji_pokok'        => 'required|numeric|min:0',
            'uang_makan'        => 'nullable|numeric|min:0',
            'tunjangan_jabatan' => 'nullable|numeric|min:0',
            'insentif_kinerja'  => 'nullable|numeric|min:0',
            'tunjangan_program' => 'nullable|numeric|min:0',
            'tunjangan_bpjs'    => 'nullable|numeric|min:0',
            'bonus'             => 'nullable|numeric|min:0',
            'lain_lain'         => 'nullable|numeric|min:0',
            'potongan_absen'    => 'nullable|numeric|min:0',
            'cash_bon'          => 'nullable|numeric|min:0',
            'potongan_bpjs'     => 'nullable|numeric|min:0',
            'potongan_lain'     => 'nullable|numeric|min:0',
        ]);

        [$tahun, $bulan] = $this->parsePeriode($request->periode);

        $totalPenerimaan = (float)($request->gaji_pokok ?? 0)
            + (float)($request->uang_makan ?? 0)
            + (float)($request->tunjangan_jabatan ?? 0)
            + (float)($request->insentif_kinerja ?? 0)
            + (float)($request->tunjangan_program ?? 0)
            + (float)($request->tunjangan_bpjs ?? 0)
            + (float)($request->bonus ?? 0)
            + (float)($request->lain_lain ?? 0);

        $totalPotongan = (float)($request->potongan_absen ?? 0)
            + (float)($request->cash_bon ?? 0)
            + (float)($request->potongan_bpjs ?? 0)
            + (float)($request->potongan_lain ?? 0);

        $totalGaji = $totalPenerimaan - $totalPotongan;

        $gaji->update([
            'id_karyawan'       => $request->id_karyawan,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'gaji_pokok'        => $request->gaji_pokok ?? 0,
            'uang_makan'        => $request->uang_makan ?? 0,
            'tunjangan_jabatan' => $request->tunjangan_jabatan ?? 0,
            'insentif_kinerja'  => $request->insentif_kinerja ?? 0,
            'tunjangan_program' => $request->tunjangan_program ?? 0,
            'tunjangan_bpjs'    => $request->tunjangan_bpjs ?? 0,
            'bonus'             => $request->bonus ?? 0,
            'lain_lain'         => $request->lain_lain ?? 0,
            'total_penerimaan'  => $totalPenerimaan,
            'potongan_absen'    => $request->potongan_absen ?? 0,
            'cash_bon'          => $request->cash_bon ?? 0,
            'potongan_bpjs'     => $request->potongan_bpjs ?? 0,
            'potongan_lain'     => $request->potongan_lain ?? 0,
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

    public function reward()
    {
        $topKandidat = [
            (object)['nama' => 'Ahmad Rida', 'jabatan' => 'Staff Keuangan', 'skor' => 96],
            (object)['nama' => 'Siti Aminah', 'jabatan' => 'Staff IT', 'skor' => 92],
            (object)['nama' => 'Budi Santoso', 'jabatan' => 'Marketing', 'skor' => 89],
        ];

        $daftarReward = [
            (object)['id' => 1, 'nama' => 'Ahmad Rida', 'jabatan' => 'Staff Keuangan', 'skor' => 96, 'jenis_reward' => 'Bonus Rp 1.000.000', 'status' => 'Menunggu'],
            (object)['id' => 2, 'nama' => 'Siti Aminah', 'jabatan' => 'Staff IT', 'skor' => 92, 'jenis_reward' => 'Voucher Belanja', 'status' => 'Menunggu'],
            (object)['id' => 3, 'nama' => 'Budi Santoso', 'jabatan' => 'Marketing', 'skor' => 89, 'jenis_reward' => 'Sertifikat', 'status' => 'Disetujui'],
        ];

        return view('pimpinan.reward', compact('topKandidat', 'daftarReward'));
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
}
