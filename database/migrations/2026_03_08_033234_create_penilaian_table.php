<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id('id_penilaian');
        $table->foreignId('id_karyawan')->constrained('karyawan','id_karyawan');
        $table->integer('bulan');
        $table->integer('tahun');
        $table->integer('disiplin');
        $table->integer('produktivitas');
        $table->integer('tanggung_jawab');
        $table->integer('sikap_kerja');
        $table->integer('loyalitas');
        $table->integer('total_skor');
        $table->foreignId('dinilai_oleh')->constrained('user','id_user');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
