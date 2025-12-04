<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function create()
    {
        $services = Service::where('active', true)->orderBy('name')->get();
        return view('admin.cashier.create', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'actual_weight' => 'required|numeric|min:0.1',
            'payment_method' => 'required|string|in:Tunai,QRIS,Bayar Nanti',
            'customer_name' => 'sometimes|string|max:255',
            'customer_phone' => 'sometimes|string|max:32',
            'customer_address' => 'sometimes|string|max:1024',
        ]);

        $service = Service::findOrFail($data['service_id']);
        $weight = (float) $data['actual_weight'];
        $shipping = 0; // walk-in cashier has no delivery cost
        $final = round($weight * $service->price_per_kg + $shipping, 2);

        $orderData = [
            'order_number' => 'PW-' . now()->format('YmdHis'),
            'customer_name' => $data['customer_name'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'pickup_address' => $data['customer_address'] ?? null,
            'address' => $data['customer_address'] ?? null,
            'service_id' => $service->id,
            'actual_weight' => $weight,
            'total_price' => $final,
            'payment_method' => $data['payment_method'],
            'is_walk_in' => true,
        ];

        if ((($data['payment_method'] ?? '') === 'Bayar Nanti')) {
            $orderData['status'] = 'pending';
        } else {
            $orderData['status'] = 'paid';
            $orderData['approved_by'] = Auth::id();
            $orderData['approved_at'] = now();
        }

        $order = Order::create($orderData);

        return redirect()->route('admin.cashier.create')->with('success', 'Transaksi kasir berhasil disimpan.');
    }
}
