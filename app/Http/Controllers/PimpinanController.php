<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Penggajian;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


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

    public function cuti()
    {
        $dataCuti = [
            [
                'id' => 1,
                'nama' => 'Ahmad Rida',
                'jabatan' => 'Staff Keuangan',
                'tgl_mulai' => '2026-03-10',
                'tgl_selesai' => '2026-03-12',
                'jenis' => 'Cuti Tahunan',
                'status' => 'Pending'
            ],
            [
                'id' => 2,
                'nama' => 'Siti Aminah',
                'jabatan' => 'Staff IT',
                'tgl_mulai' => '2026-03-05',
                'tgl_selesai' => '2026-03-06',
                'jenis' => 'Cuti Sakit',
                'status' => 'Disetujui'
            ],
        ];

        return view('pimpinan.manajemen_cuti', compact('dataCuti'));
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

    public function storeKaryawan(Request $request)
    {
        // 1. Validasi Input dari form website
        $request->validate([
            'nama_lengkap' => 'required',
            'username'     => 'required|unique:user,username',
            'password'     => 'required',
            // Pastikan ID Role Karyawan sudah benar (misal ID-nya 4 untuk Karyawan)
            'role_id'      => 'required|exists:roles,role_id' 
        ]);

        // 2. Buat Akun User (INI YANG MEMBUAT LOGIN BISA BERHASIL NANTINYA)
        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => Hash::make($request->password), // WAJIB DI-HASH!
            'role_id'      => $request->role_id,
            'status_akun'  => 'aktif'
        ]);

        // 3. Buat Profil Karyawan yang terhubung ke User
        Karyawan::create([
            'id_user'         => $user->id_user,
            'nama'            => $request->nama_lengkap,
            'status_karyawan' => 'aktif'
        ]);

        return back()->with('success', 'Akun Karyawan berhasil dibuat. Silakan login di Aplikasi Mobile!');
    }
    public function destroyKaryawan($id_user)
    {
        // 1. Cari data profil karyawan berdasarkan id_user
        $karyawan = Karyawan::where('id_user', $id_user)->first();
        
        // 2. Hapus profil karyawannya (jika ada)
        if ($karyawan) {
            $karyawan->delete();
        }

        // 3. Cari akun login-nya, lalu hapus!
        $user = User::find($id_user);
        if ($user) {
            $user->delete();
        }

        return back()->with('success', 'Akun Karyawan berhasil dihapus secara permanen tanpa merusak Role Master!');
    }

}
