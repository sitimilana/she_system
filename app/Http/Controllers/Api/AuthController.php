<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Karyawan; // Pastikan model Karyawan di-import

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi request dari Android
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // 2. Cari user di database
        $user = User::where('username', $request->username)->first();

        // 3. Pengecekan Murni Laravel (Apakah user ada & apakah Hash Password cocok?)
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false, 
                'message' => 'Login Gagal: Username atau Password salah!'
            ], 401);
        }

        // 4. Jika cocok, cek status akun
        if ($user->status_akun !== 'aktif') {
            return response()->json(['success' => false, 'message' => 'Akun dinonaktifkan!'], 403);
        }

        // 5. Buat Token
        $token = $user->createToken('MobileAppToken')->plainTextToken;

        // 6. Ambil relasi data Karyawan berdasarkan ID User yang login
        // (Pastikan menggunakan $user->id atau $user->id_user sesuai dengan nama kolom primary key di tabel users Anda)
        $karyawan = Karyawan::where('id_user', $user->id)->first();

        // 7. Kembalikan Response ke Android
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'token'   => $token,
            'data'    => [
                'id_user'      => $user->id,
                'username'     => $user->username,
                // Ambil nama dari tabel karyawan jika ada, jika tidak fallback ke nama/username di tabel users
                'nama_lengkap' => $karyawan ? $karyawan->nama : $user->username,
                // Kirim divisi jika ada, jika tidak kirim string kosong
                'divisi'       => $karyawan ? $karyawan->divisi : '' 
            ]
        ]);
    }
}