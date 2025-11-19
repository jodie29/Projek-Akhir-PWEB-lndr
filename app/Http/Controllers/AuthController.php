<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // --- LOGIKA REDIRECTION BERDASARKAN ROLE BARU ---
            $user = Auth::user();

            if ($user->role === 'admin') {
                // Jika user adalah admin, arahkan ke dashboard admin
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'courier') {
                // Jika user adalah kurir, arahkan ke rute kurir. 
                // Ganti 'courier.dashboard' dengan rute yang sesuai untuk kurir
                return redirect()->intended(route('courier.orders.collect', ['order' => 1])); 
            } elseif ($user->role === 'customer') {
                // Jika user adalah customer, arahkan ke halaman utama atau halaman customer
                return redirect()->intended(route('customer.dashboard')); 
            }

            // Default fallback jika role tidak dikenal
            return redirect()->intended('/');
            // --- AKHIR LOGIKA REDIRECTION BERDASARKAN ROLE ---

        }

        return back()->with('error', 'Email atau password salah')->withInput(['email' => $request->email]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}