<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        
        // Hapus data lama untuk ID 67 dari 1 Mei - 18 Juni
        DB::table('absensi')
            ->where('id_karyawan', 67)
            ->whereBetween('tanggal', ['2026-05-01', '2026-06-18'])
            ->delete();

        $data = [
            // --- BULAN MEI 2026 ---
            // Minggu 1
            ['id_karyawan' => 67, 'tanggal' => '2026-05-01', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            
            // Minggu 2
            ['id_karyawan' => 67, 'tanggal' => '2026-05-04', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-05', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-06', 'status' => 'terlambat', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-07', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-08', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            
            // Minggu 3 (Diubah menjadi 'alfa')
            ['id_karyawan' => 67, 'tanggal' => '2026-05-11', 'status' => 'alfa', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-12', 'status' => 'alfa', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-13', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-14', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-15', 'status' => 'terlambat', 'created_at' => $now, 'updated_at' => $now],
            
            // Minggu 4
            ['id_karyawan' => 67, 'tanggal' => '2026-05-18', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-19', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-20', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-21', 'status' => 'terlambat', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-22', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            
            // Minggu 5
            ['id_karyawan' => 67, 'tanggal' => '2026-05-25', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-26', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-27', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-28', 'status' => 'terlambat', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-05-29', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],

            // --- BULAN JUNI 2026 ---
            // Minggu 1
            ['id_karyawan' => 67, 'tanggal' => '2026-06-01', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-02', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-03', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-04', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-05', 'status' => 'terlambat', 'created_at' => $now, 'updated_at' => $now],

            // Minggu 2
            ['id_karyawan' => 67, 'tanggal' => '2026-06-08', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-09', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-10', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-11', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-12', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],

            // Minggu 3
            ['id_karyawan' => 67, 'tanggal' => '2026-06-15', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-16', 'status' => 'terlambat', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-17', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
            ['id_karyawan' => 67, 'tanggal' => '2026-06-18', 'status' => 'hadir', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('absensi')->insert($data);
    }
}