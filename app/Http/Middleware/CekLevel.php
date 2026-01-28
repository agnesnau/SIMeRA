<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$levels): Response
    {
        // 1. Cek apakah user sudah login?
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // 2. Ambil level user yang sedang login
        // Pastikan di database tabel 'users' ada kolom 'level'
        $userLevel = auth()->user()->level; 

        // 3. Cek apakah level user termasuk yang diizinkan (ada di array $levels)
        // Contoh: level:admin,petugas -> $levels isinya ['admin', 'petugas']
        if (in_array($userLevel, $levels)) {
            return $next($request);
        }

        // 4. Kalau level tidak cocok, tendang ke Dashboard atau halaman lain
        return redirect('/dashboard')->with('error', 'Maaf, Anda tidak punya akses ke halaman tersebut.');
    }
}