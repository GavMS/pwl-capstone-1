<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Cek apakah role user ada di daftar role yang diizinkan
        if (!in_array(auth()->user()->role, $roles)) {
            $userRole = auth()->user()->role;
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'organizer') {
                return redirect()->route('organizer.dashboard');
            }
            return redirect()->route('user.dashboard');
        }

        return $next($request); // Lanjutkan request jika role cocok
    }
}
