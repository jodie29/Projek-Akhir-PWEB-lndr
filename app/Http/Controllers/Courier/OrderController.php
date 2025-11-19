<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Show payment collection form for courier
    public function collectForm($id)
    {
        $order = Order::with('service')->findOrFail($id);
        return view('courier.orders.collect', compact('order'));
    }

    // Record collection (Tunai/QRIS) by courier
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

        // Courier should not mark customer confirmation; customer confirms via token link.

        // If payment completes the total, mark as paid
        if ((float)$data['collected_amount'] >= (float)$order->total_price) {
            $order->payment_method = $data['collection_method'];
            $order->status = 'paid';
        }

        $order->save();

        return redirect()->route('courier.orders.collect', $order->id)->with('success', 'Pembayaran tercatat.');
    }
}
