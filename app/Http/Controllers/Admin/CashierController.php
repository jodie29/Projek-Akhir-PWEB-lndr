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
        ]);

        $service = Service::findOrFail($data['service_id']);
        $weight = (float) $data['actual_weight'];
        // Walk-in cashier: no delivery fee
        $shipping = 0;
        $final = round($weight * $service->price_per_kg + $shipping, 2);

        $orderData = [
            'order_number' => 'PW-' . now()->format('YmdHis'),
            'service_id' => $service->id,
            'actual_weight' => $weight,
            'total_price' => $final,
            'payment_method' => $data['payment_method'],
            'is_walk_in' => true,
        ];

        // If cashier selects 'Bayar Nanti', do not mark as paid
        if ($data['payment_method'] === 'Bayar Nanti') {
            $orderData['status'] = 'pending';
        } else {
            $orderData['status'] = 'paid';
            $orderData['approved_by'] = Auth::id();
            $orderData['approved_at'] = now();
        }

        $order = Order::create($orderData);

        return redirect()->route('admin.cashier.create')->with('success', 'Transaksi kasir berhasil disimpan (LUNAS).');
    }
}
