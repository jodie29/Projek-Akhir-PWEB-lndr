<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Order; // Model Order untuk query riwayat pesanan customer
use App\Models\User; // Agar Intelephense / analyzer mengenali relasi user->orders()

class CustomerDashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk pelanggan.
     * Corresponds to: GET /customer/dashboard (customer.dashboard)
     */
    public function index()
    {
        // Ambil user yang sedang login
        /** @var User $user */
        $user = Auth::user();
        // --- Ambil data riil dari database ---
        $total_orders_count = $user->orders()->count();

        // Status yang dianggap aktif (tahap proses dan menunggu pembayaran)
        // Include additional statuses so active counter includes courier status updates
        $activeStatuses = ['pending', 'menunggu_jemput', 'in_progress', 'di_laundry', 'dijemput', 'diantar', 'confirmed', 'awaiting_payment'];
        $active_orders_count = $user->orders()->whereIn('status', $activeStatuses)->count();

        // Menunggu pembayaran: status yang menunggu konfirmasi / pembayaran
        $pending_payment_count = $user->orders()->whereIn('status', ['awaiting_payment', 'pending_payment'])->count();

        // Ambil 5 pesanan terbaru milik user untuk tabel recent_orders
        // Include orders that are awaiting customer confirmation as well
        $recent_orders = $user->orders()->with('service')
            ->latest()->take(5)->get();

        $user_name = $user->name;

        // Prefer a single dashboard view to avoid confusion between profile/dashboard and customer/dashboard
        return view('customer.dashboard.index', compact(
            'user_name',
            'total_orders_count',
            'active_orders_count',
            'pending_payment_count',
            'recent_orders'
        ));
    }
}