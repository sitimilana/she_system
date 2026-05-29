<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Update latitude dan longitude untuk tanggal 25-26 Mei
$updated = DB::table('absensi')
    ->whereIn('tanggal', ['2026-05-25', '2026-05-26'])
    ->update([
        'latitude_masuk' => '-7.7509',
        'longitude_masuk' => '111.9946',
        'latitude_pulang' => '-7.7509',
        'longitude_pulang' => '111.9946'
    ]);

echo "✓ Berhasil menambahkan lokasi untuk $updated record\n";
echo "  Latitude Masuk: -7.7509\n";
echo "  Longitude Masuk: 111.9946\n";
echo "  Latitude Pulang: -7.7509\n";
echo "  Longitude Pulang: 111.9946\n";
echo "\n✓ Tanggal 25-26 Mei 2026 sudah memiliki data lokasi\n";
?>
