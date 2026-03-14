<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Ambil nama role dari database dan jadikan huruf kecil semua
        $userRole = strtolower(Auth::user()->role->nama_role);

        // Jika role cocok, persilakan masuk
        if ($userRole === $role) {
            return $next($request);
        }

        // Jika tidak cocok, tendang kembali ke dashboard masing-masing atau beri error 403
        abort(403, 'Akses Ditolak. Anda tidak memiliki izin ke halaman ini.');
    }
}