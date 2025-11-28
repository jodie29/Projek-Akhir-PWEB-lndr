<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

class OrderApprovalController extends Controller
{
    // List orders awaiting approval (status = 'pending')
    public function index()
    {
        // Include orders that are 'pending' but also those waiting for customer confirmation so admin can track them
        $query = Order::with('service')->whereIn('status', ['pending', 'awaiting_confirmation']);

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

        // Basic validation for payment_method (admin can set it here as override if necessary)
        $request->validate([
            'payment_method' => 'nullable|string|in:Bayar Nanti,Tunai,QRIS',
        ]);

        // If admin submitted an actual_weight with the approval form, use it (admin measured),
        // otherwise fall back to previously saved actual_weight.
        $servicePrice = $order->service->price_per_kg ?? 0;
        $actualWeight = (float) ($request->input('actual_weight') ?? $order->actual_weight);
        // If actual weight was provided in request, save it to the order
        if ($request->has('actual_weight') && is_numeric($request->input('actual_weight'))) {
            $order->actual_weight = (float) $request->input('actual_weight');
        }
        // jika pesanan walk-in (kasir cepat), tidak ada ongkir
        $shipping = ($order->is_walk_in ?? false) ? 0 : 6000; // ongkir flat untuk delivery

        $final = round($actualWeight * $servicePrice + $shipping, 2);

        $order->total_price = $final;
        // Simpan info auditor
        if (Auth::check()) {
            $order->approved_by = Auth::id();
        }
        $order->approved_at = now();

        // Ensure we have a payment_method set; if not, allow admin to specify it via form
        $order->payment_method = $request->input('payment_method') ?? $order->payment_method ?? 'Bayar Nanti';

        // Jika metode pembayaran adalah 'Bayar Nanti', simpan harga final
        // Admin bisa memilih untuk 'skip confirmation' agar tidak mengirim token dan langsung lanjutkan.
        if (($order->payment_method ?? '') === 'Bayar Nanti') {
            $order->customer_confirmed = false;
            $order->customer_confirmed_at = null;
            $skip = $request->boolean('skip_confirmation');
            if ($skip) {
                // Admin is bypassing customer confirmation intentionally
                $order->customer_confirmed = true;
                $order->customer_confirmed_at = now();
                $order->confirmation_token = null;
                $order->status = 'processing';
            } else {
                // Default behavior: create a token and await customer confirmation
                $order->confirmation_token = \Illuminate\Support\Str::random(60);
                $order->status = 'awaiting_confirmation';
            }
        } else {
            // metode langsung bayar (Tunai/QRIS) -> tandai lunas
            $order->status = 'paid';
        }

        $order->save();

        // If token created and admin didn't skip, include link in flash so admin can copy/send to the customer
        if (!empty($order->confirmation_token)) {
            $link = route('order.confirm.show', ['token' => $order->confirmation_token]);
            // Try to email the confirmation link to the customer. If sending fails we will still
            // show the link in the flash message for the admin to copy manually.
            try {
                if ($order->customer && $order->customer->email) {
                    Mail::to($order->customer->email)->send(new OrderConfirmationMail($order));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to send order confirmation email to customer for Order: ' . $order->id . '. Error: ' . $e->getMessage());
            }
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
