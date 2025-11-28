<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderConfirmationController extends Controller
{
    // Show public confirmation page for a token
    public function show($token)
    {
        $order = Order::where('confirmation_token', $token)->with('service')->firstOrFail();

        return view('order.confirm', compact('order'));
    }

    // Handle customer confirming the price
    public function confirm(Request $request, $token)
    {
        $order = Order::where('confirmation_token', $token)->firstOrFail();

        // mark as customer confirmed
        $order->customer_confirmed = true;
        $order->customer_confirmed_at = now();
        // If the order is currently pending/awaiting approval, move it to 'processing' (sedang diproses)
        if (in_array($order->status, ['pending', 'awaiting_collection', 'approved', 'awaiting_confirmation'])) {
            $order->status = 'processing';
        }
        // invalidate token so it can't be reused
        $order->confirmation_token = null;
        $order->save();

        // Log the status change and confirmation
        \Illuminate\Support\Facades\Log::info('Order confirmed by customer: ' . $order->id . ' - status: ' . $order->status);

        // Redirect to a thank-you page that doesn't rely on the token
        return redirect()->route('order.confirm.thanks', ['order' => $order->id])->with('success', 'Terima kasih — Anda telah mengonfirmasi harga.');
    }

    // Thank you page after confirmation — public, doesn't require token
    public function thanks($order)
    {
        $order = Order::findOrFail($order);
        return view('order.confirm_thanks', compact('order'));
    }
}
