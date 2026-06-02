<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Penggajian;
use App\Models\Penilaian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenggajianPenilaianSeeder extends Seeder
{
    // Konstanta gaji
    const GAJI_POKOK_STANDAR = 5000000;
    const TUNJ_JABATAN_STANDAR = 500000;
    const TUNJ_BPJS_STANDAR = 231000;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawans = Karyawan::where('status_karyawan', 'aktif')->get();
        
        // 5 bulan terakhir: Januari - Mei 2026
        $bulanTarget = [1, 2, 3, 4, 5];
        $tahun = 2026;

        $this->command->info('🔄 Membuat data Penggajian dan Penilaian Kinerja...');
        $this->command->info('📅 Period: Januari - Mei 2026');
        $this->command->info('👥 Total Karyawan: ' . $karyawans->count());
        $this->command->newLine();

        $penggajianCount = 0;
        $penilaianCount = 0;
        $skippedCount = 0;

        foreach ($bulanTarget as $bulan) {
            $this->command->info("Processing Bulan: " . $this->getBulanName($bulan));
            
            foreach ($karyawans as $karyawan) {
                // ============================================================
                // VALIDASI: Pastikan karyawan sudah join sebelum atau pada bulan ini
                // ============================================================
                $tanggalMasuk = Carbon::parse($karyawan->tanggal_masuk ?? $karyawan->created_at);
                $awalBulan = Carbon::create($tahun, $bulan, 1)->startOfDay();
                
                if ($tanggalMasuk->gt($awalBulan)) {
                    // Karyawan belum join pada bulan ini, skip
                    continue;
                }

                // ============================================================
                // CEGAH DUPLIKASI
                // ============================================================
                $penggajianExists = Penggajian::where('id_karyawan', $karyawan->id_karyawan)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->exists();

                $penilaianExists = Penilaian::where('id_karyawan', $karyawan->id_karyawan)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->exists();

                if ($penggajianExists && $penilaianExists) {
                    $skippedCount++;
                    continue;
                }

                // ============================================================
                // BUAT DATA PENGGAJIAN
                // ============================================================
                if (!$penggajianExists) {
                    $gajiPokok = $this->getGajiPokok($karyawan);
                    
                    // Hitung bonus & insentif berdasarkan penilaian (jika ada)
                    $penilaian = Penilaian::where('id_karyawan', $karyawan->id_karyawan)
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->first();
                    
                    $insentifKinerja = 0;
                    $bonus = 0;
                    
                    if ($penilaian) {
                        if ($penilaian->total_skor >= 90) {
                            $insentifKinerja = 150000;
                        } elseif ($penilaian->total_skor >= 80) {
                            $insentifKinerja = 100000;
                        } elseif ($penilaian->total_skor >= 70) {
                            $insentifKinerja = 50000;
                        }
                    }

                    // Hitung uang makan & potongan absen
                    $hariKerja = $this->getHariKerjaPerBulan($tahun, $bulan, $karyawan);
                    $uangMakan = $hariKerja['hadir'] * 20000;
                    
                    $hariPotong = $hariKerja['alfa'] + $hariKerja['izin'];
                    if ($hariKerja['cuti'] > 1) {
                        $hariPotong += ($hariKerja['cuti'] - 1);
                    }
                    $potonganAbsen = $hariPotong * 70000;

                    $totalPenerimaan = $gajiPokok 
                        + $uangMakan 
                        + self::TUNJ_JABATAN_STANDAR 
                        + $insentifKinerja 
                        + 0 // tunjangan_program
                        + $bonus
                        + 0; // lain_lain

                    $totalPotongan = $potonganAbsen 
                        + 0 // cash_bon
                        + 0 // cash_bon_2
                        + self::TUNJ_BPJS_STANDAR
                        + 0; // potongan_lain

                    $totalGaji = $totalPenerimaan - $totalPotongan;

                    Penggajian::create([
                        'id_karyawan'       => $karyawan->id_karyawan,
                        'bulan'             => $bulan,
                        'tahun'             => $tahun,
                        'gaji_pokok'        => $gajiPokok,
                        'uang_makan'        => $uangMakan,
                        'tunjangan_jabatan' => self::TUNJ_JABATAN_STANDAR,
                        'insentif_kinerja'  => $insentifKinerja,
                        'tunjangan_program' => 0,
                        'tunjangan_bpjs'    => 0,
                        'bonus'             => $bonus,
                        'lain_lain'         => 0,
                        'total_penerimaan'  => $totalPenerimaan,
                        'potongan_absen'    => $potonganAbsen,
                        'cash_bon'          => 0,
                        'cash_bon_2'        => 0,
                        'potongan_bpjs'     => self::TUNJ_BPJS_STANDAR,
                        'potongan_lain'     => 0,
                        'total_potongan'    => $totalPotongan,
                        'total_gaji'        => $totalGaji,
                        'tanggal_dibuat'    => Carbon::create($tahun, $bulan, 1)->toDateString(),
                        'status_slip'       => 'final',
                    ]);

                    $penggajianCount++;
                }

                // ============================================================
                // BUAT DATA PENILAIAN KINERJA
                // ============================================================
                if (!$penilaianExists) {
                    // Random score antara 65-100
                    $disiplin = rand(70, 100);
                    $produktivitas = rand(65, 95);
                    $tanggungJawab = rand(70, 100);
                    $sikapKerja = rand(65, 95);
                    $loyalitas = rand(70, 100);

                    $totalSkor = ($disiplin + $produktivitas + $tanggungJawab + $sikapKerja + $loyalitas) / 5;
                    $totalSkor = round($totalSkor, 2);

                    // Jika total skor <= 70, kemungkinan ada catatan negatif
                    $catatanEvaluasi = null;
                    if ($totalSkor <= 70) {
                        $catatanEvaluasi = $this->generateCatatan('kurang');
                    } elseif ($totalSkor >= 90) {
                        $catatanEvaluasi = $this->generateCatatan('sangat_baik');
                    }

                    // Get ID pimpinan/supervisor (ambil user dengan role pimpinan/kepala_bagian)
                    $penilaiId = DB::table('user')
                        ->join('roles', 'user.role_id', '=', 'roles.role_id')
                        ->whereIn('roles.nama_role', ['Pimpinan', 'pimpinan', 'Kepala Bagian', 'kepala_bagian'])
                        ->where('user.status_akun', 'aktif')
                        ->pluck('user.id_user')
                        ->random();

                    Penilaian::create([
                        'id_karyawan'        => $karyawan->id_karyawan,
                        'bulan'              => $bulan,
                        'tahun'              => $tahun,
                        'disiplin'           => $disiplin,
                        'produktivitas'      => $produktivitas,
                        'tanggung_jawab'     => $tanggungJawab,
                        'sikap_kerja'        => $sikapKerja,
                        'loyalitas'          => $loyalitas,
                        'total_skor'         => $totalSkor,
                        'catatan_evaluasi'   => $catatanEvaluasi,
                        'dinilai_oleh'       => $penilaiId,
                    ]);

                    $penilaianCount++;
                }
            }
        }

        $this->command->newLine();
        $this->command->info('✅ Seeder Completed!');
        $this->command->info("📊 Penggajian records created: {$penggajianCount}");
        $this->command->info("📈 Penilaian records created: {$penilaianCount}");
        $this->command->info("⏭️ Skipped (already exists): {$skippedCount}");
    }

    private function getBulanName(int $bulan): string
    {
        $bulanNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        return $bulanNames[$bulan] ?? '';
    }

    private function getGajiPokok(Karyawan $karyawan): int
    {
        // Bisa disesuaikan berdasarkan divisi/jabatan
        // Untuk sekarang gunakan standar
        return self::GAJI_POKOK_STANDAR;
    }

    private function getHariKerjaPerBulan(int $tahun, int $bulan, Karyawan $karyawan): array
    {
        // Ambil data dari tabel absensi untuk bulan tersebut
        $absensi = DB::table('absensi')
            ->where('id_karyawan', $karyawan->id_karyawan)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'hadir'   => $absensi['hadir'] ?? 0,
            'terlambat' => $absensi['terlambat'] ?? 0,
            'alfa'    => $absensi['alfa'] ?? 0,
            'izin'    => $absensi['izin'] ?? 0,
            'cuti'    => $absensi['cuti'] ?? 0,
            'sakit'   => $absensi['sakit'] ?? 0,
        ];
    }

    private function generateCatatan(string $level): string
    {
        $catatanKurang = [
            'Perlu peningkatan dalam kedisiplinan dan kehadiran',
            'Produktivitas belum sesuai target yang ditetapkan',
            'Perlu fokus lebih pada penyelesaian tugas tepat waktu',
            'Tingkatkan komitmen dan dedikasi terhadap pekerjaan',
        ];

        $catatanSangatBaik = [
            'Performa luar biasa, terus pertahankan!',
            'Dedikasi dan loyalitas yang tinggi terhadap perusahaan',
            'Produktivitas exceeds expectations, excellent work!',
            'Karyawan teladan, terus berkembang dan berinovasi',
        ];

        if ($level === 'kurang') {
            return $catatanKurang[array_rand($catatanKurang)];
        } else {
            return $catatanSangatBaik[array_rand($catatanSangatBaik)];
        }
    }
}
