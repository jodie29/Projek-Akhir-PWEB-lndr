<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

class OrderController extends Controller
{
    // Display a listing of the orders for admin management
    public function index()
    {
        $orders = Order::with('service')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    // Display the specified resource (re-use review view)
    public function show($id)
    {
        $order = Order::with('service')->findOrFail($id);
        return view('admin.orders.review', compact('order'));
    }

    // Show the form for editing the specified order
    public function edit($id)
    {
        $order = Order::with('service')->findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    // Update the specified order in storage
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'total_price' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'status' => 'nullable|string',
            'actual_weight' => 'nullable|numeric|min:0',
        ]);

        // Prevent admin from force-setting status to "processing" for orders that
        // require customer confirmation (Bayar Nanti) and have not yet been confirmed.
        if (array_key_exists('status', $data) && $data['status'] === 'processing') {
            // If the order payment is 'Bayar Nanti' and customer hasn't confirmed, block this.
            if (($order->payment_method ?? '') === 'Bayar Nanti' && ! ($order->customer_confirmed ?? false)) {
                return back()->withErrors(['status' => 'Tidak dapat mengubah status menjadi "processing" sebelum pelanggan mengonfirmasi harga.'])->withInput();
            }
        }

        $order->fill($data);
        // If admin provided a new actual_weight, optionally recalculate total price using service price
        if (isset($data['actual_weight'])) {
            $order->actual_weight = (float) $data['actual_weight'];
            $servicePrice = $order->service->price_per_kg ?? 0;
            $shipping = ($order->is_walk_in ?? false) ? 0 : 6000;
            $order->total_price = round($order->actual_weight * $servicePrice + $shipping, 2);
        }
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Order berhasil diperbarui.');
    }

    // Remove the specified order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order dihapus.');
    }

    /**
     * Mark an order as ready for delivery, assign to courier if needed.
     */
    public function markReadyForDelivery(\Illuminate\Http\Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Only admin can perform this; middleware ensures admin role.
        // If the admin passed a request param `auto_assign=1`, assign the courier automatically.
        // Otherwise, leave courier_id as NULL so couriers can claim it themselves.
        $autoAssign = $request->input('auto_assign');
        if ($autoAssign && empty($order->courier_id)) {
            $courier = \App\Models\User::where('role', 'courier')
                ->withCount(['assignedOrders as active_pickup_count' => function($q) {
                    $q->whereIn('status', ['pending', 'menunggu_jemput', 'in_progress', 'di_laundry', 'ready_for_delivery']);
                }])
                ->orderBy('active_pickup_count', 'asc')
                ->first();
            if ($courier) {
                $order->courier_id = $courier->id;
            }
        }

        $order->status = 'ready_for_delivery';
        $order->save();

        \Illuminate\Support\Facades\Log::info('Order ' . $order->id . ' marked ready for delivery by admin. Assigned courier: ' . ($order->courier_id ?? 'none'));

        return back()->with('success', 'Order diberi tanda siap untuk dikirim.');
    }

    /**
     * Resend order confirmation email to customer (admin action).
     */
    public function resendConfirmation($id)
    {
        $order = Order::findOrFail($id);
        if (empty($order->confirmation_token)) {
            return back()->with('error', 'Pesanan ini tidak memiliki token konfirmasi atau tidak memerlukan konfirmasi.');
        }
        try {
            if ($order->customer && $order->customer->email) {
                Mail::to($order->customer->email)->send(new OrderConfirmationMail($order));
            }
            \Illuminate\Support\Facades\Log::info('Resent order confirmation email for order: ' . $order->id);
            return back()->with('success', 'Email konfirmasi berhasil dikirim ulang ke pelanggan.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to resend order confirmation email: ' . $order->id . '. Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email konfirmasi. Silakan cek konfigurasi email.');
        }
    }
}
