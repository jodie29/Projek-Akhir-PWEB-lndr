<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * Middleware ini berfungsi sebagai "guest" guard, mengalihkan pengguna yang sudah login
     * jika mereka mencoba mengakses rute yang hanya untuk "guest" (seperti /login).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|array|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // Cek apakah pengguna sudah login (terotentikasi) dengan guard ini
            if (Auth::guard($guard)->check()) {
                
                // Ambil data user yang sudah login
                $user = Auth::user();

                // PENTING: Pengalihan berdasarkan peran (role) user
                // Ini mencegah terjadinya login loop pada customer.
                if ($user && property_exists($user, 'role')) {
                    switch ($user->role) {
                        case 'admin':
                            return redirect()->route('admin.dashboard');
                        case 'courier':
                            return redirect()->route('courier.dashboard');
                        case 'customer':
                            return redirect()->route('customer.dashboard'); 
                        default:
                            // Jika role tidak dikenal, arahkan ke HOME default
                            return redirect(RouteServiceProvider::HOME);
                    }
                }
                
                // Fallback jika tidak ada role
                return redirect(RouteServiceProvider::HOME);
            }
        }

        // Lanjutkan ke request jika user belum login (biarkan mereka mengakses /login)
        return $next($request);
    }
}