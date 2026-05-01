<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = App\Models\User::create([
        'nama_lengkap' => 'Tes Karyawan',
        'username'     => 'tes_karyawan_'.time(),
        'password'     => bcrypt('123456'),
        'role_id'      => 3,
        'status_akun'  => 'pending',
    ]);

    App\Models\Karyawan::create([
        'id_user'         => $user->id_user,
        'nama'            => 'Tes Karyawan',
        'no_hp'           => '-',
        'email'           => '-',
        'alamat'          => '-',
        'status_karyawan' => 'aktif',
        'divisi'          => 'keuangan',
    ]);
    
    echo "Success inserting user!";
} catch (\Exception $e) {
    echo "Error inserting user: " . $e->getMessage();
}
