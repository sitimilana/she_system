<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Karyawan; 
use Illuminate\Support\Facades\Validator;
    
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
        if ($user->status_akun === 'pending') {
            return response()->json(['success' => false, 'message' => 'Akun menunggu persetujuan Pimpinan'], 403);
        }
        if ($user->status_akun !== 'aktif') {
            return response()->json(['success' => false, 'message' => 'Akun dinonaktifkan!'], 403);
        }

        // 5. Buat Token
        $token = $user->createToken('MobileAppToken')->plainTextToken;

        // 6. Ambil relasi data Karyawan berdasarkan ID User yang login
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();

        // 7. Kembalikan Response ke Android (Sudah Fix Menyertakan Tanggal Masuk)
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'token'   => $token,
            'data'    => [
                'id_user'       => $user->id_user,
                'username'      => $user->username,
                // Ambil nama dari tabel karyawan jika ada, jika tidak fallback ke nama/username di tabel users
                'nama_lengkap'  => $karyawan ? $karyawan->nama : $user->username,
                // Kirim divisi jika ada, jika tidak kirim string kosong
                'divisi'        => $karyawan ? $karyawan->divisi : '',
                // ✅ BERHASIL DITAMBAHKAN: Digunakan untuk batasan filter tahun di RiwayatPengajuanActivity Android
                'tanggal_masuk' => ($karyawan && $karyawan->tanggal_masuk) ? $karyawan->tanggal_masuk : $user->created_at->toDateString()
            ]
        ]);
    }
    public function changePassword(Request $request)
    {
        $user = $request->user(); // Mendapatkan user yang sedang login via Sanctum

        $validator = Validator::make($request->all(), [
            'password_lama'       => 'required',
            'password_baru'       => 'required|min:6|different:password_lama',
            'konfirmasi_password' => 'required|same:password_baru'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Cek apakah password lama benar
        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama salah!'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->password_baru);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah!'
        ], 200);
    }
    public function getProfil(Request $request)
    {
        $user = $request->user();
        $karyawan = \App\Models\Karyawan::where('id_user', $user->id_user)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'nama_lengkap'  => $user->nama_lengkap,
                'divisi'        => $karyawan->divisi,
                'email'         => $karyawan->email,
                'no_hp'         => $karyawan->no_hp,
                'alamat'        => $karyawan->alamat,
                'tanggal_masuk' => $karyawan->tanggal_masuk,
                'foto'          => $karyawan->foto // pastikan ada kolom ini di DB
            ]
        ]);
    }
}