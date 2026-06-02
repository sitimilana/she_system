#!/usr/bin/env php
<?php
// Quick verification script

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n╔════════════════════════════════════════════════════════╗\n";
echo "║  📊 VERIFIKASI DATA PENGGAJIAN & PENILAIAN (Jan-Mei)  ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

// Summary penggajian per bulan
echo "📌 PENGGAJIAN PER BULAN:\n";
$penggajianPerBulan = DB::table('penggajian')
    ->where('tahun', 2026)
    ->whereIn('bulan', [1,2,3,4,5])
    ->select('bulan', DB::raw('count(*) as total'), DB::raw('sum(total_gaji) as total_pembayaran'))
    ->groupBy('bulan')
    ->orderBy('bulan')
    ->get();

$bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'];
foreach ($penggajianPerBulan as $row) {
    $bulan = $bulanNames[$row->bulan - 1];
    $total = $row->total;
    $pembayaran = 'Rp ' . number_format($row->total_pembayaran, 0, ',', '.');
    echo "   {$bulan} 2026: {$total} records | Total: {$pembayaran}\n";
}

// Summary penilaian per bulan
echo "\n📈 PENILAIAN KINERJA PER BULAN:\n";
$penilaianPerBulan = DB::table('penilaian')
    ->where('tahun', 2026)
    ->whereIn('bulan', [1,2,3,4,5])
    ->select('bulan', 
        DB::raw('count(*) as total'),
        DB::raw('round(avg(total_skor), 2) as rata_rata_skor'),
        DB::raw('max(total_skor) as skor_tertinggi'),
        DB::raw('min(total_skor) as skor_terendah')
    )
    ->groupBy('bulan')
    ->orderBy('bulan')
    ->get();

foreach ($penilaianPerBulan as $row) {
    $bulan = $bulanNames[$row->bulan - 1];
    $total = $row->total;
    $rataRata = $row->rata_rata_skor;
    $tertinggi = $row->skor_tertinggi;
    $terendah = $row->skor_terendah;
    echo "   {$bulan} 2026: {$total} records | Rata-rata: {$rataRata} | Range: {$terendah}-{$tertinggi}\n";
}

// Total count
echo "\n📊 TOTAL DATA:\n";
$totalPenggajian = DB::table('penggajian')->where('tahun', 2026)->whereIn('bulan', [1,2,3,4,5])->count();
$totalPenilaian = DB::table('penilaian')->where('tahun', 2026)->whereIn('bulan', [1,2,3,4,5])->count();
echo "   Total Penggajian: {$totalPenggajian}\n";
echo "   Total Penilaian: {$totalPenilaian}\n";

echo "\n✅ Verifikasi selesai!\n\n";
