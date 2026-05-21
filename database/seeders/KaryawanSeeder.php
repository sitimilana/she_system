<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Cuti;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $roleIdKaryawan = DB::table('roles')->where('nama_role', 'like', '%karyawan%')->value('role_id') ?? 4;

        // 1. Data Karyawan Real & Dummy
        $karyawanReal = [
            ['nama' => 'Lestari Indah', 'email' => 'lestariinjun@gmail.com', 'divisi' => 'akademik', 'status' => 'aktif', 'username' => 'lestari_injun'],
            ['nama' => 'Siti Milana Putri', 'email' => 'stymlnpltr@gmail.com', 'divisi' => 'admin umum', 'status' => 'aktif', 'username' => 'siti_milana'],
            ['nama' => 'Rian Setiawan', 'email' => 'englishclubkdr@gmail.com', 'divisi' => 'keuangan', 'status' => 'aktif', 'username' => 'rian_ec'],
            ['nama' => 'Milana Setyawati', 'email' => 'sitimilanapl@gmail.com', 'divisi' => 'akademik', 'status' => 'aktif', 'username' => 'milana_apl'],
            ['nama' => 'Muhammad Dwiki', 'email' => 'dwikireza64@gmail.com', 'divisi' => 'admin umum', 'status' => 'aktif', 'username' => 'dwiki_reza'],
            ['nama' => 'Arjuna Hartono', 'email' => 'arjunamobil0902@gmail.com', 'divisi' => 'marketing', 'status' => 'aktif', 'username' => 'arjuna_mobil'],
            ['nama' => 'Fitria Lestari', 'email' => 'kesekretariatanec25@gmail.com', 'divisi' => 'keuangan', 'status' => 'aktif', 'username' => 'fitria_ec'],
        ];

        $karyawanTambahan = [
            ['nama' => 'Budi Santoso', 'divisi' => 'akademik', 'status' => 'aktif', 'username' => 'budi_akad'],
            ['nama' => 'Dewi Lestari', 'divisi' => 'akademik', 'status' => 'aktif', 'username' => 'dewi_akad'],
            ['nama' => 'Eko Prasetyo', 'divisi' => 'akademik', 'status' => 'pending', 'username' => 'eko_pending'],
            ['nama' => 'Agus Setiawan', 'divisi' => 'admin umum', 'status' => 'aktif', 'username' => 'agus_umum'],
            ['nama' => 'Sri Wahyuni', 'divisi' => 'admin umum', 'status' => 'aktif', 'username' => 'sri_umum'],
            ['nama' => 'Hendra Wijaya', 'divisi' => 'admin umum', 'status' => 'pending', 'username' => 'hendra_pending'],
            ['nama' => 'Joko Susilo', 'divisi' => 'office boy', 'status' => 'aktif', 'username' => 'joko_ob'],
            ['nama' => 'Slamet Riyadi', 'divisi' => 'office boy', 'status' => 'aktif', 'username' => 'slamet_ob'],
            ['nama' => 'Andi Wijaya', 'divisi' => 'office boy', 'status' => 'aktif', 'username' => 'andi_ob'],
            ['nama' => 'Edi Sutrisno', 'divisi' => 'office boy', 'status' => 'aktif', 'username' => 'edi_ob'],
            ['nama' => 'Sugeng Redjo', 'divisi' => 'office boy', 'status' => 'aktif', 'username' => 'sugeng_ob'],
            ['nama' => 'Wawan Setiawan', 'divisi' => 'office boy', 'status' => 'aktif', 'username' => 'wawan_ob'],
            ['nama' => 'Gono Prastowo', 'divisi' => 'office boy', 'status' => 'pending', 'username' => 'gono_pending'],
            ['nama' => 'Ahmad Fauzi', 'divisi' => 'marketing', 'status' => 'aktif', 'username' => 'ahmad_mkt'],
            ['nama' => 'Dian Sastrowardoyo', 'divisi' => 'marketing', 'status' => 'aktif', 'username' => 'dian_mkt'],
            ['nama' => 'Bambang Pamungkas', 'divisi' => 'marketing', 'status' => 'aktif', 'username' => 'bambang_mkt'],
            ['nama' => 'Rizky Ramadhan', 'divisi' => 'marketing', 'status' => 'pending', 'username' => 'rizky_pending'],
            ['nama' => 'Lilis Suryani', 'divisi' => 'keuangan', 'status' => 'aktif', 'username' => 'lilis_keu'],
            ['nama' => 'Denny Caknan', 'divisi' => 'keuangan', 'status' => 'aktif', 'username' => 'denny_keu'],
            ['nama' => 'Mega Utami', 'divisi' => 'keuangan', 'status' => 'pending', 'username' => 'mega_pending'],
        ];

        $semuaKaryawanDummy = array_merge($karyawanReal, $karyawanTambahan);

        foreach ($semuaKaryawanDummy as $index => $data) {
            
            if (User::where('username', $data['username'])->exists()) continue;

            // 2. Generate Akun & Profil
            $user = User::create([
                'nama_lengkap' => $data['nama'],
                'username' => $data['username'],
                'password' => Hash::make('password123'),
                'status_akun' => $data['status'],
                'role_id' => $roleIdKaryawan,
            ]);

            $emailKaryawan = $data['email'] ?? ($data['username'] . '@gmail.com');
            $tanggalMasukCerdas = Carbon::now()->subMonths($index + 2)->subDays($index)->toDateString();

            $karyawan = Karyawan::create([
                'id_user' => $user->id_user,
                'nama' => $data['nama'],
                'email' => $emailKaryawan,
                'divisi' => $data['divisi'],
                'no_hp' => '08' . str_pad((string) rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'alamat' => 'Alamat dummy generate by seeder.',
                'status_karyawan' => $data['status'],
                'sisa_cuti' => 12,
                'tanggal_masuk' => $data['status'] === 'aktif' ? $tanggalMasukCerdas : null,
            ]);

            // 3. Generate Absensi & Perizinan (Jika Karyawan Aktif)
            if ($data['status'] === 'aktif') {
                $rentangHariPemicu = 30;
                
                for ($i = $rentangHariPemicu; $i >= 0; $i--) {
                    $hariEvaluasi = Carbon::now()->subDays($i);

                    if ($hariEvaluasi->isWeekend()) continue;

                    $angkaAcak = rand(1, 100);
                    $jamMasuk = null;
                    $jamPulang = null;
                    $statusAbsen = 'hadir';

                    if ($angkaAcak <= 80) {
                        // 80% Hadir Normal
                        $jamMasuk  = sprintf("07:%02d:%02d", rand(10, 55), rand(10, 59));
                        $jamPulang = sprintf("16:%02d:%02d", rand(0, 15), rand(10, 59));
                    } elseif ($angkaAcak <= 88) {
                        // 8% Terlambat
                        $jamMasuk  = sprintf("08:%02d:%02d", rand(1, 25), rand(10, 59));
                        $jamPulang = sprintf("16:%02d:%02d", rand(0, 10), rand(10, 59));
                        $statusAbsen = 'terlambat';
                    } elseif ($angkaAcak <= 96) {
                        // 8% Tidak Masuk Sah (Cuti / Izin / Sakit)
                        $jenisIzinArr = ['Cuti', 'Izin', 'Sakit'];
                        $jenisIzin = $jenisIzinArr[array_rand($jenisIzinArr)];
                        $statusAbsen = 'izin';

                        // CREATE DATA CUTI APPROVED (Syarat masuk ke absensi)
                        Cuti::create([
                            'id_karyawan' => $karyawan->id_karyawan,
                            'tanggal_pengajuan' => $hariEvaluasi->copy()->subDays(rand(1, 3))->toDateString(),
                            'tanggal_mulai' => $hariEvaluasi->toDateString(),
                            'tanggal_selesai' => $hariEvaluasi->toDateString(),
                            'jenis_cuti' => $jenisIzin,
                            'alasan' => 'Keperluan mendadak / musibah (Generate by Seeder).',
                            'berkas_bukti' => $jenisIzin === 'Sakit' ? 'berkas_cuti/dummy_surat_dokter.pdf' : null,
                            'status' => 'approved',
                            'keterangan_pimpinan' => 'Disetujui',
                            'disetujui_oleh' => 1 // Asumsi ID Pimpinan
                        ]);

                        // Kurangi sisa cuti jika jenisnya 'Cuti'
                        if ($jenisIzin === 'Cuti') {
                            $karyawan->decrement('sisa_cuti', 1);
                        }

                    } else {
                        // 4% Alfa (Mbolos)
                        $statusAbsen = 'alfa';
                    }

                    // CREATE DATA ABSENSI
                    Absensi::create([
                        'id_karyawan' => $karyawan->id_karyawan,
                        'tanggal' => $hariEvaluasi->toDateString(),
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'status' => $statusAbsen,
                        'foto_masuk' => $jamMasuk ? 'bukti_masuk_dummy.jpg' : null,
                        'foto_pulang' => $jamPulang ? 'bukti_pulang_dummy.jpg' : null,
                        'latitude_masuk' => $jamMasuk ? '-7.812345' : null,
                        'longitude_masuk' => $jamMasuk ? '112.012345' : null,
                        'latitude_pulang' => $jamPulang ? '-7.812345' : null,
                        'longitude_pulang' => $jamPulang ? '112.012345' : null,
                    ]);
                }

                // 4. Generate Ekstra Riwayat Cuti (Pending / Ditolak) untuk Testing UI
                $statusRandomTesting = ['pending_pimpinan', 'pending_kabag', 'rejected'];
                $randomTestStatus = $statusRandomTesting[array_rand($statusRandomTesting)];
                
                Cuti::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal_pengajuan' => Carbon::now()->addDays(rand(1, 5))->toDateString(),
                    'tanggal_mulai' => Carbon::now()->addDays(rand(6, 7))->toDateString(),
                    'tanggal_selesai' => Carbon::now()->addDays(rand(6, 7))->toDateString(),
                    'jenis_cuti' => 'Izin',
                    'alasan' => 'Ada acara keluarga di luar kota.',
                    'status' => $randomTestStatus,
                    'keterangan_pimpinan' => $randomTestStatus === 'rejected' ? 'Ditolak karena tidak melampirkan bukti yang jelas.' : null,
                    'disetujui_oleh' => $randomTestStatus === 'rejected' ? 1 : null
                ]);
            }
        }
    }
}