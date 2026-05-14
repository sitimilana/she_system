<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================================
        // 1. KEMBALIKAN 4 ROLE UTAMA PERSIS SEPERTI DATABASE ASLI
        // ========================================================
        DB::table('roles')->insert([
            ['role_id' => 1,  'nama_role' => 'pimpinan',      'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 11, 'nama_role' => 'Karyawan',      'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 12, 'nama_role' => 'Kepala Bagian', 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 13, 'nama_role' => 'Akademik',      'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========================================================
        // 2. BUAT AKUN PIMPINAN 
        // ========================================================
        $idPimpinan = DB::table('user')->insertGetId([
            'nama_lengkap' => 'Bapak Pimpinan', // <-- Nama cukup di tabel user
            'username'     => 'pimpinan',
            'password'     => Hash::make('pimpinan123'),
            'role_id'      => 1, 
            'status_akun'  => 'aktif',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('karyawan')->insert([
            'id_user'         => $idPimpinan,
            'nama'            => 'Bapak Pimpinan',
            'email'           => 'pimpinan@contoh.com',
            'divisi'          => null, // Bukan divisi pada enum
            'no_hp'           => '081111111111',
            'alamat'          => 'Kota Kediri',
            'sisa_cuti'       => 12,
            'status_karyawan' => 'aktif',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ========================================================
        // 3. BUAT AKUN KEPALA BAGIAN
        // ========================================================
        $idKabag = DB::table('user')->insertGetId([
            'nama_lengkap' => 'Ibu Kepala Bagian', // <-- Nama cukup di tabel user
            'username'     => 'kabag',
            'password'     => Hash::make('kabag123'),
            'role_id'      => 12, 
            'status_akun'  => 'aktif',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('karyawan')->insert([
            'id_user'         => $idKabag,
            'nama'            => 'Ibu Kepala Bagian',
            'email'           => 'kabag@contoh.com',
            'divisi'          => null, // Bukan divisi pada enum
            'no_hp'           => '082222222222',
            'alamat'          => 'Kota Kediri',
            'sisa_cuti'       => 12,
            'status_karyawan' => 'aktif',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ========================================================
        // 4. BUAT AKUN UNTUK AKADEMIK (ROLE & DIVISI)
        // ========================================================
        $idAkademik = DB::table('user')->insertGetId([
            'nama_lengkap' => 'Staf Akademik',
            'username'     => 'akademik',
            'password'     => Hash::make('akademik123'),
            'role_id'      => 13, // Menggunakan role Akademik
            'status_akun'  => 'aktif',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('karyawan')->insert([
            'id_user'         => $idAkademik,
            'nama'            => 'Staf Akademik',
            'email'           => 'akademik@contoh.com',
            'divisi'          => 'akademik', // Akademik juga bertindak sebagai divisi
            'no_hp'           => '083333333333',
            'alamat'          => 'Kota Kediri',
            'sisa_cuti'       => 12,
            'status_karyawan' => 'aktif',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }
}