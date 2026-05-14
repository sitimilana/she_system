<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_kantor', function (Blueprint $table) {
            $table->decimal('radius', 8, 3)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan_kantor', function (Blueprint $table) {
            $table->integer('radius')->change();
        });
    }
};