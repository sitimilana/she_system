<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Absensi;
use Carbon\Carbon;

class DummyAbsensiSeeder extends Seeder
{
    public function run()
    {
        $karyawans = Karyawan::where('status_karyawan', 'aktif')->get();

        if ($karyawans->isEmpty()) {
            $this->command->error("Gagal: Tidak ada karyawan aktif. Silakan input karyawan dulu lewat web!");
            return;
        }

        // Daftar Foto (Pastikan foto ini ada di storage Anda)
        $daftarFoto = [
            'berkas_absensi/dummy_1.jpg',
            'berkas_absensi/dummy_2.jpg',
            'berkas_absensi/dummy_3.jpg',
        ];

        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();
        $jumlahData = 0;
        $jumlahDilewati = 0;

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            
            // Lewati Hari Minggu
            if ($date->isSunday()) {
                continue; 
            }

            foreach ($karyawans as $karyawan) {
                // FITUR KEAMANAN: Cek apakah karyawan ini sudah absen di tanggal ini?
                $sudahAbsen = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                                     ->where('tanggal', $date->format('Y-m-d'))
                                     ->exists();

                // Jika sudah ada datanya, lewati! (Mencegah absen ganda jika Seeder terpencet 2x)
                if ($sudahAbsen) {
                    $jumlahDilewati++;
                    continue;
                }

                $peluang = rand(1, 100);
                $status = 'hadir';
                $jamMasuk = null;
                $jamPulang = null;
                
                $fotoAcakMasuk = $daftarFoto[array_rand($daftarFoto)];
                $fotoAcakPulang = $daftarFoto[array_rand($daftarFoto)]; 

                if ($peluang <= 80) {
                    $status = 'hadir';
                    $jamMasuk = $date->copy()->setTime(7, rand(30, 59), rand(0, 59))->format('H:i:s');
                    $jamPulang = $date->copy()->setTime(17, rand(0, 59), rand(0, 59))->format('H:i:s');
                } elseif ($peluang <= 90) {
                    $status = 'terlambat';
                    $jamMasuk = $date->copy()->setTime(rand(8, 9), rand(10, 59), rand(0, 59))->format('H:i:s');
                    $jamPulang = $date->copy()->setTime(17, rand(0, 59), rand(0, 59))->format('H:i:s');
                } elseif ($peluang <= 95) {
                    $status = rand(0, 1) ? 'izin' : 'sakit';
                } else {
                    $status = 'alpha';
                }

                Absensi::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal'     => $date->format('Y-m-d'),
                    'jam_masuk'   => $jamMasuk,
                    'jam_pulang'  => $jamPulang,
                    'status'      => $status,
                    'foto_masuk'  => ($status == 'hadir' || $status == 'terlambat') ? $fotoAcakMasuk : null, 
                    'foto_pulang' => ($status == 'hadir' || $status == 'terlambat') ? $fotoAcakPulang : null,
                    'lokasi_masuk'=> ($status == 'hadir' || $status == 'terlambat') ? '-7.8488, 112.0123' : null,
                    'lokasi_pulang'=> ($status == 'hadir' || $status == 'terlambat') ? '-7.8488, 112.0123' : null,
                ]);

                $jumlahData++;
            }
        }

        $this->command->info("Selesai! {$jumlahData} absensi baru ditambahkan. {$jumlahDilewati} data dilewati (karena sudah ada).");
    }
}