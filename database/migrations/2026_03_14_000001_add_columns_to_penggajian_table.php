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
            $table->double('tunjangan_program')->nullable()->after('insentif_kinerja');
            $table->double('tunjangan_bpjs')->nullable()->after('tunjangan_program');
            $table->double('lain_lain')->nullable()->after('tunjangan_bpjs');
            $table->double('total_penerimaan')->nullable()->after('lain_lain');
            $table->double('potongan_absen')->nullable()->after('total_penerimaan');
            $table->double('cash_bon')->nullable()->after('potongan_absen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggajian', function (Blueprint $table) {
            $table->dropColumn([
                'tunjangan_program',
                'tunjangan_bpjs',
                'lain_lain',
                'total_penerimaan',
                'potongan_absen',
                'cash_bon',
            ]);
        });
    }
};
