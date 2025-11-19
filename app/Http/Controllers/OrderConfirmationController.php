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
        // invalidate token so it can't be reused
        $order->confirmation_token = null;
        $order->save();

        return redirect()->route('order.confirm.show', ['token' => $token])->with('success', 'Terima kasih — Anda telah mengonfirmasi harga.');
    }
}
