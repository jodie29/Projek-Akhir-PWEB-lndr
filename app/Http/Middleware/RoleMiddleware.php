<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            // Jika tidak terotentikasi, arahkan ke halaman login
            return redirect()->route('login')->with('error', 'Akses ditolak. Silahkan login.');
        }
        
        $allowedRoles = $roles;

        if (count($roles) === 1) {
            // Memisahkan string roles (misal 'admin,customer' atau 'courier') menjadi array
            $allowedRoles = explode(',', $roles[0]);
        }
        
        // Pastikan role pengguna (di kolom 'role' database) ada di dalam array $allowedRoles
        if (! in_array($user->role, $allowedRoles)) {
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Akses peran tidak diizinkan.'], 403);
            }

            // Jika user sudah login tetapi role tidak sesuai, arahkan ke root URL (yang merupakan halaman login)
            return redirect('/')->with('error', 'Akses ditolak untuk peran ini.');
        }

        return $next($request);
    }
}