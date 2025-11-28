<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * Fungsi utama: Middleware ini akan dijalankan ketika pengguna mengakses
     * Route::middleware('auth'). Jika pengguna belum login, ia akan diarahkan
     * ke rute yang didefinisikan di sini.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        // Cek apakah request yang masuk adalah request Ajax (JSON).
        if ($request->expectsJson()) {
            // Jika request Ajax, kembalikan null (Laravel akan memberi response 401 Unauthorized)
            return null; 
        } else {
            // Jika request standar (browser), arahkan ke rute dengan nama 'login'
            return route('login');
        }
    }
}