<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Cek data absensi tanggal 25-26 Mei 2026
$data = DB::table('absensi')
    ->whereIn('tanggal', ['2026-05-25', '2026-05-26'])
    ->select('tanggal', 'status', DB::raw('count(*) as jumlah'))
    ->groupBy('tanggal', 'status')
    ->orderBy('tanggal')
    ->get();

echo "Status Absensi Tanggal 25-26 Mei 2026:\n";
echo "========================================\n";
foreach ($data as $row) {
    echo "Tanggal: {$row->tanggal} | Status: {$row->status} | Jumlah: {$row->jumlah}\n";
}

// Total per tanggal
echo "\nTotal Per Tanggal:\n";
$total = DB::table('absensi')
    ->whereIn('tanggal', ['2026-05-25', '2026-05-26'])
    ->select('tanggal', DB::raw('count(*) as total'))
    ->groupBy('tanggal')
    ->get();

foreach ($total as $row) {
    echo "Tanggal {$row->tanggal}: {$row->total} record\n";
}
?>
