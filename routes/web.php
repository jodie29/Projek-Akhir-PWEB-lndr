<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// --- Imports untuk Admin ---
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CourierController as AdminCourierController;
use App\Http\Controllers\Admin\OrderApprovalController;
use App\Http\Controllers\Admin\OrderCollectionController;
use App\Http\Controllers\Admin\CashierController;
// BARU DITAMBAHKAN (Asumsi Anda punya Controller ini)
use App\Http\Controllers\Admin\OrderController; 
use App\Http\Controllers\Admin\UserController; 

// --- Imports untuk Kurir ---
use App\Http\Controllers\Courier\CourierDashboardController;
use App\Http\Controllers\Courier\OrderController as CourierOrderController;

// --- Imports untuk Customer ---
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\OrderConfirmationController;

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
    
    // Dashboard Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Transaksi Walk-in (Kasir) - Lama (Opsional, jika masih dipakai)
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    
    // Kelola Layanan
    Route::resource('services', ServiceController::class);
    
    // Kelola Kurir (Menggunakan AdminCourierController alias)
    Route::resource('couriers', AdminCourierController::class); 

    // BARU DITAMBAHKAN: MANAJEMEN PENGGUNA (ADMIN, CUSTOMER, COURIER)
    Route::resource('users', UserController::class); // Untuk list, edit, delete semua role

    // ==========================================================
    // RUTE SPESIFIK ORDER (PENTING: Diletakkan SEBELUM Route::resource('orders'))
    // ==========================================================

    // Order approval / price confirmation
    Route::get('orders/pending', [OrderApprovalController::class, 'index'])->name('orders.pending');
    Route::get('orders/awaiting-payment', [OrderApprovalController::class, 'awaitingPayment'])->name('orders.awaiting_payment');
    
    // Rute Aksi pada Order
    Route::get('orders/{order}/review', [OrderApprovalController::class, 'show'])->name('orders.review');
    Route::post('orders/{order}/approve', [OrderApprovalController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/reject', [OrderApprovalController::class, 'reject'])->name('orders.reject');
    
    // Admin record payment when customer pays on pickup
    Route::get('orders/{order}/collect', [OrderCollectionController::class, 'collectForm'])->name('orders.collect');
    Route::post('orders/{order}/collect', [OrderCollectionController::class, 'collect'])->name('orders.collect.store');
    
    // BARU DITAMBAHKAN: MANAJEMEN PESANAN LENGKAP (ORDER INDEX, SHOW, EDIT)
    // Sekarang diletakkan di bawah rute spesifik
    Route::resource('orders', OrderController::class)->except(['create', 'store']); 
    // Admin: mark order ready for delivery
    Route::post('orders/{order}/ready-for-delivery', [OrderController::class, 'markReadyForDelivery'])->name('orders.ready_for_delivery');
    // Admin: resend customer confirmation email
    Route::post('orders/{order}/resend-confirmation', [OrderController::class, 'resendConfirmation'])->name('orders.resend_confirmation');
    
    // ==========================================================
    // FITUR KASIR CEPAT (WALK-IN)
    // ==========================================================
    Route::get('cashier/create', [CashierController::class, 'create'])->name('cashier.create');
    Route::post('cashier', [CashierController::class, 'store'])->name('cashier.store');

});

/*
|--------------------------------------------------------------------------
| Courier Routes (Protected - role:courier)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:courier'])->prefix('courier')->name('courier.')->group(function () {
    // Dashboard Kurir
    Route::get('/dashboard', [CourierDashboardController::class, 'index'])->name('dashboard');
    
    // Order Collect (Menggunakan alias CourierOrderController)
    // PENTING: Mengganti nama rute untuk menghindari kebingungan dengan admin.orders.collect
    Route::get('orders/{order}/collect', [CourierOrderController::class, 'collectForm'])->name('orders.pickup'); 
    Route::post('orders/{order}/collect', [CourierOrderController::class, 'collect'])->name('orders.pickup.store');
    // Claim order (ambil tugas) - couriers dapat mengambil tugas pesanan yang belum ditugaskan
    Route::post('orders/{order}/claim', [CourierOrderController::class, 'claim'])->name('orders.claim');
    // Courier marks: pick up and mark as delivered
    Route::post('orders/{order}/picked-up', [CourierOrderController::class, 'pickedUp'])->name('orders.picked_up');
    Route::post('orders/{order}/delivered', [CourierOrderController::class, 'delivered'])->name('orders.delivered');
});

/*
|--------------------------------------------------------------------------
| Customer Routes (Protected - role:customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/order/create', [CustomerController::class, 'createOrder'])->name('order.create');
    Route::post('/order', [CustomerController::class, 'storeOrder'])->name('order.store');
    Route::get('/order/history', [CustomerController::class, 'history'])->name('order.history');
    Route::get('/order/{order}', [CustomerController::class, 'showTimeline'])->name('order.show');
    Route::get('/profile/edit', [CustomerController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
    // Customer: confirm price directly from authenticated timeline
    Route::post('/order/{order}/confirm', [CustomerController::class, 'confirmPrice'])->name('order.confirm_price');
});

/*
|--------------------------------------------------------------------------
| Public routes for order confirmation (token-based)
|--------------------------------------------------------------------------
*/
// Show confirmation page to customer (public, no auth)
Route::get('/order/confirm/{token}', [OrderConfirmationController::class, 'show'])->name('order.confirm.show');
// Handle confirmation POST (customer confirms price)
Route::post('/order/confirm/{token}', [OrderConfirmationController::class, 'confirm'])->name('order.confirm.confirm');
// Thank you page route (after confirmation invalidates token)
Route::get('/order/confirm/thanks/{order}', [OrderConfirmationController::class, 'thanks'])->name('order.confirm.thanks');