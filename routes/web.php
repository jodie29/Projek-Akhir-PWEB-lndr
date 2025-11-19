<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController; // Asumsi Anda membuat DashboardController
use App\Http\Controllers\Admin\TransactionController; // Untuk Transaksi
use App\Http\Controllers\Admin\ServiceController; // Untuk Kelola Layanan

/*
|--------------------------------------------------------------------------
| Public & Auth Routes
|--------------------------------------------------------------------------
*/

// Halaman login
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
// Route Logout harus menggunakan POST
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard (Ganti dari AdminController ke DashboardController, lebih bersih)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD Transaksi Walk-in (Kasir)
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    
    // Kelola Layanan
    // Resource routes untuk kelola layanan (CRUD)
    Route::resource('services', ServiceController::class);
    // Kelola Kurir
    Route::resource('couriers', \App\Http\Controllers\Admin\CourierController::class);
    // Order approval / price confirmation
    Route::get('orders/pending', [\App\Http\Controllers\Admin\OrderApprovalController::class, 'index'])->name('orders.pending');
    Route::get('orders/{order}/review', [\App\Http\Controllers\Admin\OrderApprovalController::class, 'show'])->name('orders.review');
    Route::post('orders/{order}/approve', [\App\Http\Controllers\Admin\OrderApprovalController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/reject', [\App\Http\Controllers\Admin\OrderApprovalController::class, 'reject'])->name('orders.reject');
    // Daftar pesanan menunggu pembayaran (konfirmasi harga sudah dilakukan untuk 'Bayar Nanti')
    Route::get('orders/awaiting-payment', [\App\Http\Controllers\Admin\OrderApprovalController::class, 'awaitingPayment'])->name('orders.awaiting_payment');
    // Admin record payment when customer pays on pickup
    Route::get('orders/{order}/collect', [\App\Http\Controllers\Admin\OrderCollectionController::class, 'collectForm'])->name('orders.collect');
    Route::post('orders/{order}/collect', [\App\Http\Controllers\Admin\OrderCollectionController::class, 'collect'])->name('orders.collect.store');
    
    // Kasir Cepat (Walk-in)
    Route::get('cashier', [\App\Http\Controllers\Admin\CashierController::class, 'create'])->name('cashier.create');
    Route::post('cashier', [\App\Http\Controllers\Admin\CashierController::class, 'store'])->name('cashier.store');

});

/*
|--------------------------------------------------------------------------
| Courier Routes (Protected - role:courier)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:courier'])->prefix('courier')->name('courier.')->group(function () {
    Route::get('orders/{order}/collect', [\App\Http\Controllers\Courier\OrderController::class, 'collectForm'])->name('orders.collect');
    Route::post('orders/{order}/collect', [\App\Http\Controllers\Courier\OrderController::class, 'collect'])->name('orders.collect.store');
});

// Route sebelumnya yang menggunakan AdminController (optional, bisa dihapus jika menggunakan struktur di atas)
// Route::get('/admin/transactions/create', [AdminController::class, 'create'])->name('transactions.create');
// Route::post('/admin/transactions/store', [AdminController::class, 'store'])->name('transactions.store');