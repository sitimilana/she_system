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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            $table->unsignedBigInteger('id_user');
            $table->string('nama');
            $table->string('jabatan');
            $table->string('divisi');
            $table->string('no_hp');
            $table->string('email');
            $table->text('alamat');
            $table->string('foto')->nullable();
            $table->enum('status_karyawan',['aktif','keluar']);
            $table->timestamps();
            $table->foreign('id_user')
                ->references('id_user')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
