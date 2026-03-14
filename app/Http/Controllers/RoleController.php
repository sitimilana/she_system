<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('users')->get();
        return view('pimpinan.manajemen_role', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required',
            'role'     => 'required',
            'username' => 'required|unique:user,username',
            'password' => 'required'
        ]);

        // buat role baru
        $role = Role::create([
            'nama_role' => $request->role
        ]);

        // buat user untuk role tersebut
        User::create([
            'nama_lengkap' => $request->nama,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'role_id'      => $role->role_id,
            'status_akun'  => 'aktif'
        ]);

        return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function destroy($id)
    {
        Role::where('role_id', $id)->delete();
        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus');
    }
}