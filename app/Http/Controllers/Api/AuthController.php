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
        // 1. Cari user di database
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Username tidak ditemukan!'], 401);
        }

        // 2. PENGECEKAN GANDA (Sapu Jagat)
        // Mengecek apakah password cocok dengan Hash (Bcrypt) ATAU cocok dengan teks biasa
        $passwordCocok = Hash::check($request->password, $user->password) || $request->password === $user->password;

        if (!$passwordCocok) {
            return response()->json(['success' => false, 'message' => 'Password salah bosku!'], 401);
        }

        // 3. Jika cocok, Buat Token
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