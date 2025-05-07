<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan ada hak akses aktif di session
        if (!session()->has('active_hak_akses_id')) {
            return redirect('pilih-level');
        }
        
        $user_role = $request->user()->getRole(); // Ambil data hak_akses_kode dari hak akses aktif
    
        if (in_array($user_role, $roles)) { // Cek apakah hak_akses_kode user ada di dalam array roles
            return $next($request); // Jika ada, maka lanjutkan request
        }
    
        // Jika tidak punya role, maka tampilkan error 403
        abort(403, 'Forbidden. Kamu tidak punya akses ke halaman ini');
    }
}
