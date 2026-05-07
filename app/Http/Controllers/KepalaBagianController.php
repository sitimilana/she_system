<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Cuti;
use App\Models\Penilaian;
use App\Models\User;
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

    public function karyawan()
    {
        $dataKaryawan = User::with('karyawan')
        ->whereHas('role', function ($query) {
            $query->where('nama_role', 'Karyawan')->orWhere('nama_role', 'karyawan');
        })->get();
        $roles = \App\Models\Role::all();

        return view('kepala_bagian.kelola_karyawan', compact('dataKaryawan', 'roles'));
    }

    public function penilaian()
    {
        $karyawan = Karyawan::where('status_karyawan', 'Aktif')->get();
        $riwayatPenilaian = Penilaian::with('karyawan')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();

        $karyawan = Karyawan::where('status_karyawan', 'aktif')
            ->select('id_karyawan', 'nama')
            ->orderBy('nama')
            ->get();

        return view('kepala_bagian.penilaian_kinerja', compact('karyawan', 'riwayatPenilaian'));
    }

    // INI ADALAH FUNGSI PENILAIAN YANG BARU (Hanya 1 fungsi saja)
    public function storePenilaian(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'periode'     => ['required', 'regex:/^\d{4}-\d{2}$/'], // Menangkap bulan & tahun dari form
            'disiplin'    => 'required|integer|min:1|max:5',
            'produktivitas' => 'required|integer|min:1|max:5',
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'sikap_kerja' => 'required|integer|min:1|max:5',
            'loyalitas'   => 'required|integer|min:1|max:5',
            'catatan_evaluasi' => 'nullable|string',
        ]);

        // Pecah periode menjadi tahun dan bulan
        [$tahun, $bulan] = explode('-', $validated['periode']);

        // Pembobotan persentase
        $bobot = [
            'disiplin' => 0.20,
            'produktivitas' => 0.30,
            'tanggung_jawab' => 0.20,
            'sikap_kerja' => 0.15,
            'loyalitas' => 0.15,
        ];

        // Hitung skor dari skala 1-5
        $skorTertimbang = 0;
        foreach ($bobot as $indikator => $persentase) {
            $skorTertimbang += ((int)$validated[$indikator]) * $persentase;
        }
        
        // Skor tertimbang maksimal adalah 5. Kita kalikan 20 agar skalanya jadi 1-100
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
            'total_skor' => $skorAkhir, // Menyimpan nilai 1-100
            'catatan_evaluasi' => $validated['catatan_evaluasi'] ?? null,
            'dinilai_oleh' => auth()->user()->id_user,
        ]);

        return redirect()->route('kabag.penilaian')->with('success', 'Penilaian kinerja skala kuisioner berhasil disimpan dan dikonversi!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|unique:user,username',
            'password'     => 'required|min:6',
            'role_id'      => 'required|exists:role,role_id', // Pastikan nama tabel role sesuai
            'no_hp'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'alamat'       => 'nullable|string',
            'divisi'       => 'nullable|in:keuangan,admin umum,akademik,marketing,office boy',
        ]);

        $user = User::create([
            'nama_lengkap'       => $request->nama_lengkap,
            'username'           => $request->username,
            'password'           => bcrypt($request->password),
            'password_sementara' => $request->password,
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
        ]);

        // Opsional: Jika Anda memakai fitur email yang sebelumnya kita bahas
        if ($request->email) {
            try {
                Mail::to($request->email)->send(new KaryawanBaruMail($user, $request->password));
            } catch (\Exception $e) {
                // Abaikan error email jika gagal
            }
        }

        return redirect()->route('kabag.karyawan')
         ->with('success', 'Karyawan berhasil didaftarkan dan menunggu persetujuan Pimpinan.');
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
            ->with('success', 'Pengajuan cuti disetujui dan diteruskan ke Pimpinan.');
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
}