<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderApprovalController extends Controller
{
    // List orders awaiting approval (status = 'pending')
    public function index()
    {
        $query = Order::with('service')->where('status', 'pending');

        // optional filter: ?confirmed=1 or ?confirmed=0
        $confirmed = request()->query('confirmed');
        if ($confirmed !== null && in_array($confirmed, ['0', '1'], true)) {
            $query->where('customer_confirmed', (bool) $confirmed);
        }

        $orders = $query->latest()->paginate(20);
        // preserve query params in pagination links
        $orders->appends(request()->query());

        return view('admin.orders.pending', compact('orders'));
    }

    // Show order details for review
    public function show($id)
    {
        $order = Order::with('service')->findOrFail($id);
        return view('admin.orders.review', compact('order'));
    }

    // List orders that have been confirmed by customer but not yet paid
    public function awaitingPayment()
    {
        $orders = Order::with('service')
            ->where('status', 'pending')
            ->where('customer_confirmed', true)
            ->latest()
            ->paginate(20);

        return view('admin.orders.awaiting_payment', compact('orders'));
    }

    // Approve order: compute final price and mark as paid
    public function approve(Request $request, $id)
    {
        $order = Order::with('service')->findOrFail($id);

        $servicePrice = $order->service->price_per_kg ?? 0;
        $actualWeight = (float) $order->actual_weight;
        // jika pesanan walk-in (kasir cepat), tidak ada ongkir
        $shipping = ($order->is_walk_in ?? false) ? 0 : 6000; // ongkir flat untuk delivery

        $final = round($actualWeight * $servicePrice + $shipping, 2);

        $order->total_price = $final;
        // Simpan info auditor
        if (Auth::check()) {
            $order->approved_by = Auth::id();
        }
        $order->approved_at = now();

        // Jika metode pembayaran adalah 'Bayar Nanti', simpan harga final dan buat token konfirmasi
        // Namun biarkan status tetap 'pending' — konfirmasi harga dilakukan oleh pelanggan.
        if (($order->payment_method ?? '') === 'Bayar Nanti') {
            $order->confirmation_token = \Illuminate\Support\Str::random(60);
            $order->customer_confirmed = false;
            $order->customer_confirmed_at = null;
        } else {
            // metode langsung bayar (Tunai/QRIS) -> tandai lunas
            $order->status = 'paid';
        }

        $order->save();

        // If token created, include link in flash so admin can copy/send to customer
        if (!empty($order->confirmation_token)) {
            $link = route('order.confirm.show', ['token' => $order->confirmation_token]);
            return redirect()->route('admin.orders.pending')->with('success', 'Order disetujui dan harga final disimpan. Link konfirmasi pelanggan: ' . $link);
        }

        return redirect()->route('admin.orders.pending')->with('success', 'Order disetujui dan harga final disimpan.');
    }

    // Reject order
    public function reject(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'rejected';
        $order->save();

        return redirect()->route('admin.orders.pending')->with('success', 'Order ditolak.');
    }

    // Mark that the customer has accepted/confirmed the price (used by kasir/kurir)
    // NOTE: customer confirmation is handled via tokenized public endpoint; admin should not mark customer confirmation.
}
