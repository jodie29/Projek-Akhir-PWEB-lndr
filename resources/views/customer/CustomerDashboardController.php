<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    /**
     * Membuat instance controller baru.
     * Menggunakan middleware 'auth:customer' untuk memastikan hanya pelanggan yang login yang dapat mengakses.
     */
    public function __construct()
    {
        // Pastikan pengguna sudah login menggunakan guard 'customer'
        $this->middleware('auth:customer');
    }

    /**
     * Menampilkan halaman dashboard pelanggan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil data pengguna yang sedang login
        $customer = Auth::guard('customer')->user();
        
        // Data placeholder/contoh untuk ditampilkan di dashboard view
        $totalOrders = 10;
        $activeOrders = 3;
        $lastOrderDate = '10 November 2025';

        // Mengirim data ke view 'customer/dashboard.blade.php'
        return view('customer.dashboard', compact('customer', 'totalOrders', 'activeOrders', 'lastOrderDate'));
    }
}