<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Halaman login (ganti welcome jadi login)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman dashboard admin (setelah login)
Route::get('/admin/dashboard', [AdminController::class, 'index'])
    ->middleware('auth')
    ->name('admin.dashboard');

// CRUD transaksi
Route::get('/admin/transactions/create', [AdminController::class, 'create'])->name('transactions.create');
Route::post('/admin/transactions/store', [AdminController::class, 'store'])->name('transactions.store');
