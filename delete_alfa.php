<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Hapus ALFA tanggal 27-28 Mei 2026
$deleted = DB::table('absensi')
    ->where('status', 'alfa')
    ->whereIn('tanggal', ['2026-05-27', '2026-05-28'])
    ->delete();

echo "✓ Berhasil menghapus $deleted record ALFA untuk tanggal 27-28 Mei 2026\n";
?>
