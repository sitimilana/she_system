<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE karyawan MODIFY COLUMN status_karyawan ENUM('pending', 'aktif', 'keluar') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE karyawan MODIFY COLUMN status_karyawan ENUM('aktif', 'keluar') DEFAULT 'aktif'");
    }
};
