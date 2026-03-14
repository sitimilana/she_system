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
        Schema::create('penggajian', function (Blueprint $table) {
        $table->id('id_gaji');
        $table->foreignId('id_karyawan')->constrained('karyawan','id_karyawan');
        $table->integer('bulan');
        $table->integer('tahun');
        $table->double('gaji_pokok');
        $table->double('uang_makan');
        $table->double('tunjangan_jabatan');
        $table->double('bonus')->nullable();
        $table->double('insentif_kinerja')->nullable();
        $table->double('potongan_bpjs')->nullable();
        $table->double('potongan_lain')->nullable();
        $table->double('total_gaji');
        $table->date('tanggal_dibuat');
        $table->enum('status_slip',['draft','final']);
        $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggajian');
    }
};
