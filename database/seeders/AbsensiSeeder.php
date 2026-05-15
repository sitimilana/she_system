<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = Karyawan::where('status_karyawan', 'aktif')->get();

        if ($karyawans->isEmpty()) {
            return;
        }

        Storage::disk('public')->makeDirectory('absensi');

        $today = Carbon::today();
        $year = (int) $today->format('Y');
        $startDate = Carbon::create($year, 1, 1);
        $karyawanIds = $karyawans->pluck('id_karyawan')->values();
        $totalKaryawan = $karyawanIds->count();

        if ($totalKaryawan === 0) {
            return;
        }

        $date = $startDate->copy();
        while ($date->lessThanOrEqualTo($today)) {
            $seedBase = (int) $date->format('z');
            $izinIndexes = [
                $seedBase % $totalKaryawan,
                ($seedBase + 7) % $totalKaryawan,
            ];
            $terlambatIndex = ($seedBase + 3) % $totalKaryawan;
            $alphaIndex = ($date->day % 20 === 0) ? (($seedBase + 11) % $totalKaryawan) : null;

            foreach ($karyawanIds as $index => $idKaryawan) {
                $status = 'hadir';
                if ($alphaIndex !== null && $index === $alphaIndex) {
                    $status = 'alfa';
                } elseif (in_array($index, $izinIndexes, true)) {
                    $status = 'izin';
                } elseif ($index === $terlambatIndex) {
                    $status = 'terlambat';
                }

                $jamMasuk = null;
                $jamPulang = null;
                if ($status === 'hadir') {
                    $jamMasuk = ($index % 4 === 0) ? '08:05:00' : '08:00:00';
                    $jamPulang = '16:00:00';
                } elseif ($status === 'terlambat') {
                    $jamMasuk = '08:40:00';
                    $jamPulang = '16:05:00';
                }

                $fotoMasuk = null;
                $fotoPulang = null;
                if (in_array($status, ['hadir', 'terlambat'], true)) {
                    $dateStamp = $date->format('Ymd');
                    $fotoMasuk = "absensi/{$idKaryawan}_{$dateStamp}_masuk.jpg";
                    $fotoPulang = "absensi/{$idKaryawan}_{$dateStamp}_pulang.jpg";

                    Storage::disk('public')->put($fotoMasuk, 'seed foto masuk');
                    Storage::disk('public')->put($fotoPulang, 'seed foto pulang');
                }

                Absensi::updateOrCreate(
                    [
                        'id_karyawan' => $idKaryawan,
                        'tanggal' => $date->toDateString(),
                    ],
                    [
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'latitude_masuk' => '-7.8169',
                        'longitude_masuk' => '112.0116',
                        'latitude_pulang' => '-7.8171',
                        'longitude_pulang' => '112.0118',
                        'foto_masuk' => $fotoMasuk,
                        'foto_pulang' => $fotoPulang,
                        'status' => $status,
                    ]
                );
            }

            $date->addDay();
        }
    }
}
