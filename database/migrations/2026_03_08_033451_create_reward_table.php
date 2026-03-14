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
        Schema::create('reward', function (Blueprint $table) {
            $table->id('id_reward');
            $table->foreignId('id_karyawan')->constrained('karyawan','id_karyawan');
            $table->foreignId('id_penilaian')->constrained('penilaian','id_penilaian');
            $table->date('tanggal_reward');
            $table->text('keterangan');
            $table->foreignId('diberikan_oleh')->constrained('user','id_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward');
    }
};
