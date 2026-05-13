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
        Schema::table('penggajian', function (Blueprint $table) {
            // Menambahkan tunjangan_leader setelah tunjangan_jabatan
            $table->integer('tunjangan_leader')->default(0)->after('tunjangan_jabatan');
            
            // Menambahkan cash_bon_2 setelah cash_bon
            $table->integer('cash_bon_2')->default(0)->after('cash_bon');
            
            // Menambahkan total_potongan setelah potongan_lain
            $table->integer('total_potongan')->default(0)->after('potongan_lain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggajian', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn([
                'tunjangan_leader',
                'cash_bon_2',
                'total_potongan'
            ]);
        });
    }
};