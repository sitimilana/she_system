<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE cuti MODIFY COLUMN jenis_cuti ENUM('cuti', 'izin', 'sakit', 'cuti kehamilan')");
    }

    public function down()
    {
        DB::statement("ALTER TABLE cuti MODIFY COLUMN jenis_cuti ENUM('cuti', 'izin', 'sakit')");
    }
};