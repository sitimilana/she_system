<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cuti', function (Blueprint $table) {
            $table->id('id_cuti');
            $table->foreignId('id_karyawan')->constrained('karyawan','id_karyawan');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('alasan');
            $table->string('berkas_bukti')->nullable();
            $table->enum('status',['menunggu','disetujui','ditolak']);
            $table->foreignId('disetujui_oleh')->nullable()->constrained('user','id_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};
