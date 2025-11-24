<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Service;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama admin.
     */
    public function index()
    {
        // Ambil data order terbaru dengan relasi service
        $orders = Order::with('service')->latest()->limit(10)->get();

        // Statistik sederhana
        $total_orders = Order::count();
        $income_today = Order::where('status', 'paid')
            ->where(function ($query) {
                $query->whereDate('approved_at', Carbon::today())
                      ->orWhereDate('collected_at', Carbon::today());
            })
            ->sum('total_price');
        $total_services = Service::count();

        return view('admin.dashboard', compact('orders', 'total_orders', 'income_today', 'total_services'));
    }
}