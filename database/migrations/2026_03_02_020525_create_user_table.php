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
        Schema::create('user', function (Blueprint $table) {
        $table->id('id_user');
        $table->string('nama_lengkap');
        $table->string('username')->unique();
        $table->string('password');
        $table->unsignedBigInteger('role_id');
        $table->enum('status_akun', ['aktif','nonaktif'])->default('aktif');
        $table->timestamps();

        $table->foreign('role_id')
              ->references('role_id')
              ->on('roles')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
