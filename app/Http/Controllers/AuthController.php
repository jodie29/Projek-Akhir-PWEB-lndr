<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        // Jika user sudah login, arahkan langsung ke dashboard sesuai peran
        if (Auth::check()) {
            $user = Auth::user();
            return $this->redirectBasedOnRole($user->role);
        }
        return view('auth.login'); // Asumsi Anda memiliki view di resources/views/auth/login.blade.php
    }

    /**
     * Menampilkan halaman pendaftaran untuk pelanggan.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.register');
    }

    /**
     * Menangani proses pendaftaran pelanggan.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        // Create the user with role customer
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => 'customer',
        ]);

        // Log info for debugging
        Log::info('REGISTER new customer: ' . $user->email . ' (id: ' . $user->id . ')');

        // Auto-login the new user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')->with('success', 'Akun berhasil dibuat dan Anda telah masuk. Selamat datang!');
    }

    /**
     * Menangani proses login.
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Otentikasi
        try {
            if (Auth::attempt($credentials)) {
            // Otentikasi BERHASIL

            // Regenerate session untuk menghindari serangan fiksasi sesi
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Opsional: Logging untuk debugging
            Log::info('LOGIN SUKSES: ' . $user->email . ' dengan role: ' . $user->role);

            // 3. Pengalihan berdasarkan Peran
            return $this->redirectBasedOnRole($user->role);

            }

            // Otentikasi GAGAL
            Log::warning('LOGIN GAGAL: Upaya login untuk email: ' . $request->email . ' gagal.');
            
            return back()->withErrors([
                'email' => 'Email atau Password yang Anda masukkan tidak cocok dengan catatan kami.',
            ])->onlyInput('email');
        } catch (\RuntimeException $e) {
            // Ada kemungkinan hashed password di DB tidak dalam format bcrypt (mis. plaintext atau hash lain)
            // Include the attempted email so admin can debug quickly
            Log::error('LOGIN ERROR (Hasher): ' . $e->getMessage() . ' (email: ' . $request->input('email') . ')');
            return back()->withErrors([
                'email' => 'Terjadi masalah pada proses otentikasi untuk email yang Anda masukkan: pastikan password di database telah di-hash menggunakan bcrypt. Silahkan hubungi administrator atau jalankan: php artisan users:inspect-passwords',
            ])->onlyInput('email');
        }
    }
    
    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        // Log user info for debugging
        $user = Auth::user();
        Log::info('LOGOUT attempt by: ' . ($user->email ?? '(unknown)') . ' (id: ' . ($user->id ?? 'n/a') . ')');

        // Hapus otentikasi
        Auth::logout();

        // Hapus session dan regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Also clear the 'remember me' cookie to ensure user is fully logged out
        try {
            $recallerName = Auth::getRecallerName();
            if ($recallerName) {
                Cookie::queue(Cookie::forget($recallerName));
            }
        } catch (\Throwable $e) {
            Log::warning('Unable to clear recaller cookie on logout: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Helper untuk mengarahkan user ke dashboard yang benar.
     * @param string $role
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole(string $role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'courier':
                return redirect()->route('courier.dashboard');
            case 'customer':
                return redirect()->route('customer.dashboard');
            default:
                // Jika role tidak terdefinisi, logout dan kembalikan ke login
                Auth::logout();
                return redirect()->route('login')->with('error', 'Peran pengguna tidak valid. Silahkan hubungi administrator.');
        }
    }
}