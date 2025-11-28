<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CourierDashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Kurir.
     */
    public function index()
    {
        /** @var \App\Models\User $courier */
        $courier = Auth::user();

        $pendingStatuses = ['menunggu_jemput', 'pending', 'ready_for_delivery'];
        // Additional statuses that can be claimed by couriers when unassigned
        $unassignedStatuses = array_merge($pendingStatuses, ['ready_for_delivery']);
      
        $inProcessStatuses = ['dijemput', 'diantar', 'di_laundry', 'in_laundry', 'processing'];
        $completedStatuses = ['selesai', 'delivered', 'paid'];

        $schemaMissing = ! Schema::hasColumn('orders', 'courier_id');
        if ($schemaMissing) {
            $pendingPickups = collect(); 
            $inProcessOrders = 0;
            $completedOrders = 0;
            $unassignedPickups = Order::whereIn('status', $pendingStatuses)
                                ->with('customer','service')
                                ->latest()
                                ->get();
            return view('courier.dashboard', [
                'pendingPickups' => $pendingPickups,
                'inProcessOrders' => $inProcessOrders,
                'completedOrders' => $completedOrders,
                'unassignedPickups' => $unassignedPickups,
                'transactionHistory' => collect(),
                'schemaMissing' => true,
            ]);
        }

        $pendingPickups = Order::where('courier_id', $courier->id)
                ->whereIn('status', $pendingStatuses)
                ->with('customer', 'service')
                    ->latest()
                    ->limit(5)
                    ->get();
        
        $inProcessOrdersList = Order::where('courier_id', $courier->id)
            ->whereIn('status', $inProcessStatuses)
            ->with('customer', 'service')
            ->latest()
            ->limit(5)
            ->get();
        $inProcessOrders = $inProcessOrdersList->count();
        
        $completedOrders = Order::where('courier_id', $courier->id)
                    ->whereIn('status', $completedStatuses)
                            ->count();

        $unassignedPickups = Order::whereNull('courier_id')
            ->whereIn('status', $unassignedStatuses)
                ->with('customer', 'service')
                ->latest()
                ->limit(5)
                ->get();

                $transactionHistory = collect();
                if (\Illuminate\Support\Facades\Schema::hasTable('transactions')) {
                    $transactionHistory = Transaction::where(function ($q) use ($courier) {
                                        $q->where('created_by', $courier->id)
                                            ->orWhereExists(function ($sub) use ($courier) {
                                                    $sub->select('id')->from('orders')->whereRaw('orders.id = transactions.order_id')->where('orders.courier_id', $courier->id);
                                            });
                                })
                                ->with('order')
                                ->latest('created_at')
                                ->limit(5)
                                ->get();
                }

        // Additional: recent orders assigned to this courier (any status) - limit 5
        $recentOrders = Order::where('courier_id', $courier->id)
            ->with('customer','service')
            ->latest()
            ->limit(5)
            ->get();

        // Last 6 months earnings for this courier
        $months = [];
        $courierMonthlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = \Carbon\Carbon::now()->subMonths($i);
            $months[] = $dt->format('M Y');
            $from = $dt->copy()->startOfMonth();
            $to = $dt->copy()->endOfMonth();
            $earn = 0;
            if (\Illuminate\Support\Facades\Schema::hasTable('transactions')) {
                $earn = \Illuminate\Support\Facades\DB::table('transactions')
                            ->whereBetween('created_at', [$from, $to])
                            ->where(function ($q) use ($courier) {
                                $q->where('created_by', $courier->id)
                                  ->orWhereExists(function ($sub) use ($courier) {
                                      $sub->select('id')->from('orders')->whereRaw('orders.id = transactions.order_id')->where('orders.courier_id', $courier->id);
                                  });
                            })->sum('amount');
            }
            if ($earn == 0) {
                // Fallback: sum orders with collected_at in the month assigned to this courier
                $earn = Order::where('courier_id', $courier->id)
                        ->whereNotNull('collected_at')
                        ->whereBetween('collected_at', [$from, $to])
                        ->sum(DB::raw('COALESCE(collected_amount, total_price)'));
            }
            $courierMonthlyEarnings[] = (int)$earn;
        }
        $hasCourierEarnings = array_sum($courierMonthlyEarnings) > 0;

        return view('courier.dashboard', [
            'pendingPickups' => $pendingPickups,
            'inProcessOrders' => $inProcessOrders,
            'inProcessOrdersList' => $inProcessOrdersList,
            'completedOrders' => $completedOrders,
            'unassignedPickups' => $unassignedPickups,
            'transactionHistory' => $transactionHistory,
            'schemaMissing' => false,
            'months' => $months,
            'courierMonthlyEarnings' => $courierMonthlyEarnings,
            'hasCourierEarnings' => $hasCourierEarnings,
        ]); 
    }
}