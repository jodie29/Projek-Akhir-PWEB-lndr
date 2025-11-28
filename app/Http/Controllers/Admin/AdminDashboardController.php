<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama admin.
     */
    public function index()
    {
        // Ambil data order terbaru dengan relasi service
        // Show latest 5 orders in the admin dashboard; include awaiting_confirmation so admin can see them.
        $orders = Order::with('service')
            ->latest()
            ->limit(5)
            ->get();

        // Statistik sederhana
        $total_orders = Order::count();
        $total_orders_month = Order::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();

        // Menunggu penjemputan — treat 'pending' status as waiting
        $waiting_pickup = Order::where('status', 'pending')->count();

                // Income today: include orders that are paid, or orders collected today, or orders that have collected_at today.
                // Use collected_amount when present (courier recorded payment) else fall back to total_price.
                // Prefer using transactions to compute today's income; if no transactions, fall back to order-based logic
                        $transactionToday = 0;
                        if (\Illuminate\Support\Facades\Schema::hasTable('transactions')) {
                                $transactionToday = DB::table('transactions')
                                        ->whereDate('created_at', Carbon::today())
                                        ->sum('amount');
                        }

                if ($transactionToday > 0) {
                        $income_today = $transactionToday;
                } else {
                        $income_today = Order::where(function ($q) {
                                        $q->where('status', 'paid')
                                            ->whereDate('approved_at', Carbon::today());
                                })
                                ->orWhere(function ($q) {
                                        $q->whereNotNull('collected_at')
                                            ->whereDate('collected_at', Carbon::today());
                                })
                                ->sum(DB::raw('COALESCE(collected_amount, total_price)'));
                }

                // Total revenue month: include orders that were paid/approved this month, or collected this month.
                // Prefer summing transactions table for revenue when available; fallback to previous logic
                        $transactionTotal = 0;
                        if (\Illuminate\Support\Facades\Schema::hasTable('transactions')) {
                                $transactionTotal = DB::table('transactions')
                                        ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                                        ->sum('amount');
                        }

                if ($transactionTotal > 0) {
                        $total_revenue_month = $transactionTotal;
                } else {
                        $total_revenue_month = Order::where(function ($q) {
                                                $q->where('status', 'paid')
                                                    ->whereBetween('approved_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                                        })
                                        ->orWhere(function ($q) {
                                                $q->whereNotNull('collected_at')
                                                    ->whereBetween('collected_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                                        })
                                        ->sum(DB::raw('COALESCE(collected_amount, total_price)'));
                }

                // --- Chart\x x e3: last 6 months revenue ---
                $months = [];
                $monthlyRevenue = [];
                for ($i = 5; $i >= 0; $i--) {
                        $dt = Carbon::now()->subMonths($i);
                        $months[] = $dt->format('M Y');
                        $from = $dt->copy()->startOfMonth();
                        $to = $dt->copy()->endOfMonth();
                        $monthSum = 0;
                        if (\Illuminate\Support\Facades\Schema::hasTable('transactions')) {
                                $monthSum = DB::table('transactions')
                                                        ->whereBetween('created_at', [$from, $to])
                                                        ->sum('amount');
                        }
                        if ($monthSum == 0) {
                                // fallback: sum via orders
                                $monthSum = Order::where(function ($q) use ($from, $to) {
                                                                        $q->where('status', 'paid')
                                                                                ->whereBetween('approved_at', [$from, $to]);
                                                                })
                                                                ->orWhere(function ($q) use ($from, $to) {
                                                                        $q->whereNotNull('collected_at')
                                                                                ->whereBetween('collected_at', [$from, $to]);
                                                                })
                                                                ->sum(DB::raw('COALESCE(collected_amount, total_price)'));
                        }
                        $monthlyRevenue[] = (int)$monthSum;
                }
                $hasMonthlyRevenue = array_sum($monthlyRevenue) > 0;

        $total_services = Service::count();
        $active_couriers = User::where('role', 'courier')->count();

        return view('admin.dashboard', compact('orders', 'total_orders', 'total_orders_month', 'waiting_pickup', 'income_today', 'total_revenue_month', 'total_services', 'active_couriers', 'months', 'monthlyRevenue', 'hasMonthlyRevenue'));
    }
}