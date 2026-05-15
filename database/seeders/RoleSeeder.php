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
        $roles = [
            ['role_id' => 1,  'nama_role' => 'pimpinan'],
            ['role_id' => 11, 'nama_role' => 'Karyawan'],
            ['role_id' => 12, 'nama_role' => 'Kepala Bagian'],
            ['role_id' => 13, 'nama_role' => 'Akademik'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_id' => $role['role_id']],
                [
                    'nama_role' => $role['nama_role'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // ========================================================
        // 2. BUAT AKUN PIMPINAN 
        // ========================================================
        DB::table('user')->updateOrInsert(
            ['username' => 'pimpinan'],
            [
                'nama_lengkap' => 'Bapak Pimpinan',
                'password'     => Hash::make('pimpinan123'),
                'role_id'      => 1,
                'status_akun'  => 'aktif',
                'updated_at'   => now(),
                'created_at'   => now(),
            ]
        );

        $idPimpinan = DB::table('user')->where('username', 'pimpinan')->value('id_user');

        if ($idPimpinan) {
            DB::table('karyawan')->updateOrInsert(
                ['id_user' => $idPimpinan],
                [
                    'nama'            => 'Bapak Pimpinan',
                    'email'           => 'pimpinan@contoh.com',
                    'divisi'          => null,
                    'no_hp'           => '081111111111',
                    'alamat'          => 'Kota Kediri',
                    'sisa_cuti'       => 12,
                    'status_karyawan' => 'aktif',
                    'updated_at'      => now(),
                    'created_at'      => now(),
                ]
            );
        }

        // ========================================================
        // 3. BUAT AKUN KEPALA BAGIAN
        // ========================================================
        DB::table('user')->updateOrInsert(
            ['username' => 'kabag'],
            [
                'nama_lengkap' => 'Ibu Kepala Bagian',
                'password'     => Hash::make('kabag123'),
                'role_id'      => 12,
                'status_akun'  => 'aktif',
                'updated_at'   => now(),
                'created_at'   => now(),
            ]
        );

        $idKabag = DB::table('user')->where('username', 'kabag')->value('id_user');

        if ($idKabag) {
            DB::table('karyawan')->updateOrInsert(
                ['id_user' => $idKabag],
                [
                    'nama'            => 'Ibu Kepala Bagian',
                    'email'           => 'kabag@contoh.com',
                    'divisi'          => null,
                    'no_hp'           => '082222222222',
                    'alamat'          => 'Kota Kediri',
                    'sisa_cuti'       => 12,
                    'status_karyawan' => 'aktif',
                    'updated_at'      => now(),
                    'created_at'      => now(),
                ]
            );
        }

        // ========================================================
        // 4. BUAT AKUN UNTUK AKADEMIK (ROLE & DIVISI)
        // ========================================================
        DB::table('user')->updateOrInsert(
            ['username' => 'akademik'],
            [
                'nama_lengkap' => 'Staf Akademik',
                'password'     => Hash::make('akademik123'),
                'role_id'      => 13,
                'status_akun'  => 'aktif',
                'updated_at'   => now(),
                'created_at'   => now(),
            ]
        );

        $idAkademik = DB::table('user')->where('username', 'akademik')->value('id_user');

        if ($idAkademik) {
            DB::table('karyawan')->updateOrInsert(
                ['id_user' => $idAkademik],
                [
                    'nama'            => 'Staf Akademik',
                    'email'           => 'akademik@contoh.com',
                    'divisi'          => 'akademik',
                    'no_hp'           => '083333333333',
                    'alamat'          => 'Kota Kediri',
                    'sisa_cuti'       => 12,
                    'status_karyawan' => 'aktif',
                    'updated_at'      => now(),
                    'created_at'      => now(),
                ]
            );
        }
    }
}