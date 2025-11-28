<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderCollectionController extends Controller
{
    public function collectForm($id)
    {
        $order = Order::with('service')->findOrFail($id);
        return view('admin.orders.collect', compact('order'));
    }

    public function collect(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'collection_method' => 'required|string|in:Tunai,QRIS',
            'collected_amount' => 'required|numeric|min:0'
        ]);

        $order->collection_method = $data['collection_method'];
        $order->collected_amount = $data['collected_amount'];
        $order->collected_by = Auth::id();
        $order->collected_at = now();

        // Create transaction record for admin-collected payments (cashier)
        \App\Models\Transaction::create([
            'order_id' => $order->id,
            'type' => 'collection',
            'amount' => $data['collected_amount'],
            'method' => $data['collection_method'] ?? $data['payment_method'] ?? 'Tunai',
            'created_by' => Auth::id(),
            'collected_at' => now(),
            'notes' => 'Collected by admin: ' . Auth::id()
        ]);

        if ((float)$data['collected_amount'] >= (float)$order->total_price) {
            $order->payment_method = $data['collection_method'];
            $order->status = 'paid';
        }

        $order->save();

        return redirect()->route('admin.orders.awaiting_payment')->with('success', 'Pembayaran dicatat.');
    }
}
