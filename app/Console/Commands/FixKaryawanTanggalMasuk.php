<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;

class FixKaryawanTanggalMasuk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-karyawan-tanggal-masuk {--tanggal= : Tanggal untuk diisi (format: Y-m-d), default hari ini}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix karyawan yang tanggal_masuk nya NULL dengan default tanggal hari ini atau tanggal yang ditentukan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tanggal = $this->option('tanggal') ?? now()->toDateString();
        
        // Validasi format tanggal
        if (!\DateTime::createFromFormat('Y-m-d', $tanggal)) {
            $this->error('Format tanggal tidak valid. Gunakan format Y-m-d');
            return 1;
        }

        // Cari karyawan dengan tanggal_masuk NULL
        $karyawanNull = Karyawan::whereNull('tanggal_masuk')->get();
        
        if ($karyawanNull->isEmpty()) {
            $this->info('✓ Tidak ada karyawan dengan tanggal_masuk NULL.');
            return 0;
        }

        $this->info("Ditemukan {$karyawanNull->count()} karyawan dengan tanggal_masuk NULL");
        $this->line("Akan diisi dengan tanggal: {$tanggal}");

        if ($this->confirm('Lanjutkan proses update?')) {
            foreach ($karyawanNull as $k) {
                $k->update(['tanggal_masuk' => $tanggal]);
                $this->line("✓ {$k->nama} (ID: {$k->id_karyawan})");
            }
            
            $this->info("\n✓ Update selesai! {$karyawanNull->count()} karyawan berhasil diupdate.");
            return 0;
        }

        $this->warn('Proses dibatalkan.');
        return 1;
    }
}
