<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua Role yang ada di sistem.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Ambil data user, filter berdasarkan pencarian jika param search ada
        $users = User::with('role')
            ->when($search, function($query, $search) {
                return $query->where('nama_lengkap', 'like', '%' . $search . '%')
                             ->orWhere('username', 'like', '%' . $search . '%')
                             ->orWhereHas('role', function($q) use ($search) {
                                 $q->where('nama_role', 'like', '%' . $search . '%');
                             });
            })
            ->get();

        // Ambil semua role untuk keperluan form jika dibutuhkan
        $roles = Role::all();
        return view('pimpinan.manajemen_role', compact('users', 'roles'));
    }

    /**
     * Menyimpan user dengan role tertentu ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama'     => 'required',
            'role'     => 'required', // Ini menerima TEKS dari dropdown (Pimpinan/Karyawan)
            'username' => 'required|unique:user,username',
            'password' => 'required|min:8'
        ]);

        // 2. Cari atau buat role berdasarkan nama role yang dipilih dari select option
        $role = Role::firstOrCreate(['nama_role' => $request->role]);

        // 3. Buat user baru dengan menghubungkannya ke role tersebut
        User::create([
        'nama_lengkap' => $request->nama,
        'username'     => $request->username,
        'password'     => Hash::make($request->password),
        'role_id'      => $role->role_id, // ID ini didapat otomatis oleh sistem
        'status_akun'  => 'aktif'
        ]);

        return redirect()->route('role.index')->with('success', 'Akun berhasil ditambahkan dengan role tersebut.');
    }

    /**
     * Menghapus user/akun (bukan menghapus role master-nya).
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('role.index')->with('error', 'Akun tidak ditemukan.');
        }

        $user->delete();

        return redirect()->route('role.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }
}