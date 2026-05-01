<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $request = Illuminate\Http\Request::create('/kepala-bagian/karyawan', 'POST', [
        'nama_lengkap' => 'Dev Test',
        'username' => 'devtest_'.time(),
        'password' => '123456',
        'role_id' => 11, // Assume 11 is karyawan
        'divisi' => 'keuangan'
    ]);
    
    $controller = app()->make(\App\Http\Controllers\KepalaBagianController::class);
    $response = $controller->store($request);
    echo "Success! Response: " . get_class($response);
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Validation Error: ";
    print_r($e->errors());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
