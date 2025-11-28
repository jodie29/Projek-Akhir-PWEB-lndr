<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Wajib di-import

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna (List, Read).
     * Rute: GET /admin/users
     */
    public function index()
    {
        // Ambil semua pengguna dari database
        $users = User::all();
        // Diasumsikan view berada di resources/views/admin/users/index.blade.php
        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir untuk membuat pengguna baru (Create - Form).
     * Rute: GET /admin/users/create
     */
    public function create()
    {
        // Diasumsikan view berada di resources/views/admin/users/create.blade.php
        return view('admin.users.create');
    }

    /**
     * Menyimpan pengguna baru ke database (Create - Store).
     * Rute: POST /admin/users
     */
    public function store(Request $request)
    {
        // Validasi data input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            // Email harus unik di tabel 'users'
            'email' => 'required|string|email|max:255|unique:users,email', 
            'password' => 'required|string|min:8|confirmed', 
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            // Role harus salah satu dari: admin, courier, customer
            'role' => ['required', 'string', Rule::in(['admin', 'courier', 'customer'])], 
        ]);

        // Buat pengguna baru
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk mengedit pengguna (Edit - Form).
     * Rute: GET /admin/users/{user}/edit
     */
    public function edit(User $user)
    {
        // Diasumsikan view berada di resources/views/admin/users/edit.blade.php
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna di database (Update).
     * Rute: PUT/PATCH /admin/users/{user}
     */
    public function update(Request $request, User $user)
    {
        // Aturan validasi dasar
        $rules = [
            'name' => 'required|string|max:255',
            // Email unik, kecuali untuk user yang sedang diedit
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], 
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'role' => ['required', 'string', Rule::in(['admin', 'courier', 'customer'])],
        ];

        // Validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $validatedData = $request->validate($rules);

        // Update data user
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->phone = $validatedData['phone'];
        $user->address = $validatedData['address'];
        $user->role = $validatedData['role'];
        
        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari database (Delete).
     * Rute: DELETE /admin/users/{user}
     */
    public function destroy(User $user)
    {
        // Perbaikan: Pencegahan Admin menghapus dirinya sendiri
        // Gunakan Auth::check() untuk memastikan user sedang login sebelum membandingkan ID.
        if (Auth::check() && Auth::user()->id === $user->id) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Hapus pengguna
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna berhasil dihapus.');
    }
}