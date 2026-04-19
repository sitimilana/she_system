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
        Schema::table('cuti', function (Blueprint $table) {
            $table->string('jenis_cuti')->after('alasan');
            $table->enum('status', ['pending_kabag', 'pending_pimpinan', 'approved', 'rejected'])
                  ->default('pending_kabag')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropColumn('jenis_cuti');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])
                  ->default('menunggu')
                  ->change();
        });
    }
};
