<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Service;



class AdminController extends Controller
{
    public function dashboard()
    {
        $orders = Order::latest()->take(5)->get();
        $total_orders = Order::count();
        $income_today = Order::whereDate('created_at', today())->sum('total_price');
        $total_services = Service::count();

        return view('admin.dashboard', compact('orders', 'total_orders', 'income_today', 'total_services'));
    }

    public function transactions()
    {
        $orders = Order::latest()->with('service')->get();
        return view('admin.transactions.index', compact('orders'));
    }

    public function createTransaction()
    {
        $services = Service::where('active', true)->get();
        return view('admin.transactions.create', compact('services'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'actual_weight' => 'required|numeric|min:0.1',
            'payment_method' => 'required|string'
        ]);

        $service = Service::find($request->service_id);
        $total = $service->price_per_kg * $request->actual_weight;

        Order::create([
            'order_number' => 'PW-' . now()->format('YmdHis'),
            'service_id' => $service->id,
            'actual_weight' => $request->actual_weight,
            'total_price' => $total,
            'payment_method' => $request->payment_method,
            'status' => 'paid',
        ]);

        return redirect()->route('admin.transactions')->with('success', 'Transaksi berhasil disimpan!');
    }
}
