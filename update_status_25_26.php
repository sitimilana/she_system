<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Update tanggal 25 Mei: 24 hadir + 4 terlambat
$absensi25 = DB::table('absensi')
    ->where('tanggal', '2026-05-25')
    ->orderBy('id_absensi')
    ->get();

$count = 0;
foreach ($absensi25 as $absen) {
    if ($count < 24) {
        // 24 hadir - jam masuk sebelum 08:15
        DB::table('absensi')
            ->where('id_absensi', $absen->id_absensi)
            ->update([
                'status' => 'hadir',
                'jam_masuk' => '07:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'jam_pulang' => '17:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00'
            ]);
    } else {
        // 4 terlambat - jam masuk setelah 08:15
        DB::table('absensi')
            ->where('id_absensi', $absen->id_absensi)
            ->update([
                'status' => 'terlambat',
                'jam_masuk' => '09:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'jam_pulang' => '17:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00'
            ]);
    }
    $count++;
}

echo "✓ Tanggal 25 Mei: 24 hadir + 4 terlambat (berhasil diupdate)\n";

// Update tanggal 26 Mei: 26 hadir + 2 terlambat
$absensi26 = DB::table('absensi')
    ->where('tanggal', '2026-05-26')
    ->orderBy('id_absensi')
    ->get();

$count = 0;
foreach ($absensi26 as $absen) {
    if ($count < 26) {
        // 26 hadir - jam masuk sebelum 08:15
        DB::table('absensi')
            ->where('id_absensi', $absen->id_absensi)
            ->update([
                'status' => 'hadir',
                'jam_masuk' => '07:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'jam_pulang' => '17:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00'
            ]);
    } else {
        // 2 terlambat - jam masuk setelah 08:15
        DB::table('absensi')
            ->where('id_absensi', $absen->id_absensi)
            ->update([
                'status' => 'terlambat',
                'jam_masuk' => '09:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00',
                'jam_pulang' => '17:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00'
            ]);
    }
    $count++;
}

echo "✓ Tanggal 26 Mei: 26 hadir + 2 terlambat (berhasil diupdate)\n";
echo "\n✓ Total: 50 hadir + 6 terlambat untuk tanggal 25-26 Mei 2026\n";
?>
