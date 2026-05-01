<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE user MODIFY COLUMN status_akun ENUM('pending', 'aktif', 'nonaktif') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE user MODIFY COLUMN status_akun ENUM('aktif', 'nonaktif') DEFAULT 'aktif'");
    }
};
