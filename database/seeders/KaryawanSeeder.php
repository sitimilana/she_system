<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanSeeds = [
            [
                'nama' => 'Adi Pratama',
                'username' => 'adi.pratama',
                'email' => 'adi.pratama@contoh.com',
                'divisi' => 'keuangan',
                'no_hp' => '0812345601',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Siti Nuraini',
                'username' => 'siti.nuraini',
                'email' => 'siti.nuraini@contoh.com',
                'divisi' => 'admin umum',
                'no_hp' => '0812345602',
                'alamat' => 'Blitar',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Rizky Kurniawan',
                'username' => 'rizky.kurniawan',
                'email' => 'rizky.kurniawan@contoh.com',
                'divisi' => 'marketing',
                'no_hp' => '0812345603',
                'alamat' => 'Nganjuk',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Dewi Lestari',
                'username' => 'dewi.lestari',
                'email' => 'dewi.lestari@contoh.com',
                'divisi' => 'akademik',
                'no_hp' => '0812345604',
                'alamat' => 'Tulungagung',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Agus Setiawan',
                'username' => 'agus.setiawan',
                'email' => 'agus.setiawan@contoh.com',
                'divisi' => 'office boy',
                'no_hp' => '0812345605',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Nina Putri',
                'username' => 'nina.putri',
                'email' => 'nina.putri@contoh.com',
                'divisi' => 'admin umum',
                'no_hp' => '0812345606',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Fajar Hidayat',
                'username' => 'fajar.hidayat',
                'email' => 'fajar.hidayat@contoh.com',
                'divisi' => 'marketing',
                'no_hp' => '0812345607',
                'alamat' => 'Jombang',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Rina Oktaviani',
                'username' => 'rina.oktaviani',
                'email' => 'rina.oktaviani@contoh.com',
                'divisi' => 'akademik',
                'no_hp' => '0812345608',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Hendra Saputra',
                'username' => 'hendra.saputra',
                'email' => 'hendra.saputra@contoh.com',
                'divisi' => 'keuangan',
                'no_hp' => '0812345609',
                'alamat' => 'Mojokerto',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Lia Permata',
                'username' => 'lia.permata',
                'email' => 'lia.permata@contoh.com',
                'divisi' => 'admin umum',
                'no_hp' => '0812345610',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Yoga Prabowo',
                'username' => 'yoga.prabowo',
                'email' => 'yoga.prabowo@contoh.com',
                'divisi' => 'marketing',
                'no_hp' => '0812345611',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Tia Maharani',
                'username' => 'tia.maharani',
                'email' => 'tia.maharani@contoh.com',
                'divisi' => 'akademik',
                'no_hp' => '0812345612',
                'alamat' => 'Malang',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Bayu Nugroho',
                'username' => 'bayu.nugroho',
                'email' => 'bayu.nugroho@contoh.com',
                'divisi' => 'office boy',
                'no_hp' => '0812345613',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Novi Handayani',
                'username' => 'novi.handayani',
                'email' => 'novi.handayani@contoh.com',
                'divisi' => 'keuangan',
                'no_hp' => '0812345614',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Eka Firmansyah',
                'username' => 'eka.firmansyah',
                'email' => 'eka.firmansyah@contoh.com',
                'divisi' => 'marketing',
                'no_hp' => '0812345615',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Maya Sari',
                'username' => 'maya.sari',
                'email' => 'maya.sari@contoh.com',
                'divisi' => 'admin umum',
                'no_hp' => '0812345616',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Doni Kurnia',
                'username' => 'doni.kurnia',
                'email' => 'doni.kurnia@contoh.com',
                'divisi' => 'akademik',
                'no_hp' => '0812345617',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Rafi Ramadhan',
                'username' => 'rafi.ramadhan',
                'email' => 'rafi.ramadhan@contoh.com',
                'divisi' => 'keuangan',
                'no_hp' => '0812345618',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Tari Wulandari',
                'username' => 'tari.wulandari',
                'email' => 'tari.wulandari@contoh.com',
                'divisi' => 'admin umum',
                'no_hp' => '0812345619',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Fikri Maulana',
                'username' => 'fikri.maulana',
                'email' => 'fikri.maulana@contoh.com',
                'divisi' => 'marketing',
                'no_hp' => '0812345620',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Anisa Safitri',
                'username' => 'anisa.safitri',
                'email' => 'anisa.safitri@contoh.com',
                'divisi' => 'akademik',
                'no_hp' => '0812345621',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Rizal Fadli',
                'username' => 'rizal.fadli',
                'email' => 'rizal.fadli@contoh.com',
                'divisi' => 'office boy',
                'no_hp' => '0812345622',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Putri Ayu',
                'username' => 'putri.ayu',
                'email' => 'putri.ayu@contoh.com',
                'divisi' => 'admin umum',
                'no_hp' => '0812345623',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
            [
                'nama' => 'Ilham Fauzi',
                'username' => 'ilham.fauzi',
                'email' => 'ilham.fauzi@contoh.com',
                'divisi' => 'keuangan',
                'no_hp' => '0812345624',
                'alamat' => 'Kediri',
                'sisa_cuti' => 1,
            ],
            [
                'nama' => 'Nabila Rahma',
                'username' => 'nabila.rahma',
                'email' => 'nabila.rahma@contoh.com',
                'divisi' => 'marketing',
                'no_hp' => '0812345625',
                'alamat' => 'Kediri',
                'sisa_cuti' => 2,
            ],
        ];

        foreach ($karyawanSeeds as $seed) {
            User::updateOrInsert(
                ['username' => $seed['username']],
                [
                    'nama_lengkap' => $seed['nama'],
                    'password' => Hash::make('karyawan123'),
                    'role_id' => 11,
                    'status_akun' => 'aktif',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $user = User::where('username', $seed['username'])->first();

            if ($user) {
                Karyawan::updateOrInsert(
                    ['id_user' => $user->id_user],
                    [
                        'nama' => $seed['nama'],
                        'email' => $seed['email'],
                        'divisi' => $seed['divisi'],
                        'no_hp' => $seed['no_hp'],
                        'alamat' => $seed['alamat'],
                        'foto' => null,
                        'status_karyawan' => 'aktif',
                        'sisa_cuti' => $seed['sisa_cuti'],
                        'gaji_pokok' => 0,
                        'tunjangan_jabatan' => 0,
                        'tunjangan_bpjs' => 0,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
