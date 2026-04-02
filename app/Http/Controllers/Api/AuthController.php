<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }
}