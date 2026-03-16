<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            $roleName = strtolower($user->role->nama_role);
            if ($roleName === 'pimpinan'){
                return redirect()->route('pimpinan.dashboard');
            } elseif ($roleName === 'kepala bagian') {
                return redirect()->route('kabag.dashboard');    
            } elseif ($roleName === 'akademik'){
                return redirect()->route('akademik.beranda');
            } elseif ($roleName === 'karyawan') {
                return redirect()->route('karyawan.dashboard');
            } else {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'username'=>'Akses ditolak. Karyawan harus login via Aplikasi Mobile.',
                ]);
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}