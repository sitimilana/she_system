<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Menambahkan kolom gaji pokok dan tunjangan tetap di tabel karyawan
            $table->double('gaji_pokok')->default(0)->after('status_karyawan');
            $table->double('tunjangan_jabatan')->default(0)->after('gaji_pokok');
            $table->double('tunjangan_bpjs')->default(0)->after('tunjangan_jabatan');
        });
    }

    public function down()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Untuk menghapus (rollback) jika terjadi kesalahan
            $table->dropColumn(['gaji_pokok', 'tunjangan_jabatan', 'tunjangan_bpjs']);
        });
    }
};