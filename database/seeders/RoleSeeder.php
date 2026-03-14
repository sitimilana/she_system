<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run(): void
{
    $roleId = DB::table('roles')->insertGetId([
        'nama_role' => 'pimpinan',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('user')->insert([
        'nama_lengkap' => 'Pimpinan',
        'username' => 'pimpinan',
        'password' => Hash::make('pimpinan123'),
        'role_id' => $roleId,
        'status_akun' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
}
