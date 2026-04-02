<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua Role yang ada di sistem.
     */
    public function index()
    {
        // Mengambil semua role beserta jumlah user yang memiliki role tersebut (opsional untuk informasi tambahan di view)
        $roles = Role::with('users')->get();
        return view('pimpinan.manajemen_role', compact('roles'));
    }

    /**
     * Menyimpan kategori Role baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi murni HANYA untuk nama role
        $request->validate([
            // Pastikan nama role tidak boleh kosong dan tidak boleh duplikat di tabel roles
            'role' => 'required|unique:roles,nama_role' 
        ], [
            'role.required' => 'Nama Role wajib diisi.',
            'role.unique' => 'Nama Role ini sudah ada di sistem.'
        ]);

        // 2. Buat Role baru (TIDAK ADA LAGI KODE MEMBUAT USER DI SINI)
        Role::create([
            'nama_role' => $request->role
        ]);

        // 3. Kembalikan halaman dengan pesan sukses
        return redirect()->route('role.index')->with('success', 'Kategori Role Master berhasil ditambahkan.');
    }

    /**
     * Menghapus kategori Role.
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return redirect()->route('role.index')->with('error', 'Role tidak ditemukan.');
        }

        // Opsional: Cek apakah role ini sedang dipakai oleh user sebelum dihapus
        if ($role->users()->count() > 0) {
            return redirect()->route('role.index')->with('error', 'Gagal menghapus! Role ini masih digunakan oleh ' . $role->users()->count() . ' pengguna.');
        }

        $role->delete();

        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus.');
    }
}