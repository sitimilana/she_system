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

        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false, 
                'message' => 'Login Gagal: Username atau Password salah!'
            ], 401);
        }

        if ($user->status_akun === 'pending') {
            return response()->json(['success' => false, 'message' => 'Akun menunggu persetujuan Pimpinan'], 403);
        }
        if ($user->status_akun !== 'aktif') {
            return response()->json(['success' => false, 'message' => 'Akun dinonaktifkan!'], 403);
        }
        $token = $user->createToken('MobileAppToken')->plainTextToken;
        $isFirstLogin = Hash::check('shekediri123', $user->password);
        $karyawan = Karyawan::where('id_user', $user->id_user)->first();
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'token'   => $token,
            'data'    => [
                'id_user'        => $user->id_user,
                'username'       => $user->username,
                'nama_lengkap'   => $karyawan ? $karyawan->nama : $user->username,
                'divisi'         => $karyawan ? $karyawan->divisi : '',
                'tanggal_masuk'  => ($karyawan && $karyawan->tanggal_masuk) ? $karyawan->tanggal_masuk : $user->created_at->toDateString(),
                'is_first_login' => $isFirstLogin // Penanda wajib ganti password
            ]
        ]);
    }
    
    public function changePassword(Request $request)
    {
        $user = $request->user();

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

        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama salah!'
            ], 400);
        }

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
                'foto'          => ($karyawan && $karyawan->foto) ? asset('storage/' . $karyawan->foto) : null
            ]
        ]);
    }
    
    public function updateFoto(Request $request)
    {
        $user = $request->user();
        $karyawan = \App\Models\Karyawan::where('id_user', $user->id_user)->first();

        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            if ($karyawan->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($karyawan->foto);
            }

            $path = $request->file('foto')->store('profil_foto', 'public');
            $karyawan->foto = $path;
            $karyawan->save();

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui!',
                'foto_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada file gambar yang diunggah.'], 400);
    }
}