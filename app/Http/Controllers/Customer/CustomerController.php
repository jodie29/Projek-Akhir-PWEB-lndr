<?php

namespace App\Http\Controllers\Customer; 

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use App\Models\Service;
use App\Models\Order;

class CustomerController extends Controller
{
    /**
     * Tampilkan Dashboard utama Pelanggan.
     */
    public function dashboard()
    {
        
        return view('customer.dashboard');
    }

    /**
     * Tampilkan formulir untuk membuat pesanan baru (Pemesanan & Opsi Logistik).
     */
    public function createOrder()
    {
        
        $services = Service::where('active', true)->get();
        $default_address = Auth::user()->address ?? null;
        return view('customer.order.create', compact('services', 'default_address'));
    }

    /**
     * Proses penyimpanan pesanan baru.
     */
    public function storeOrder(Request $request)
    {
        
        $data = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            
            'payment_method' => 'nullable|string|in:Bayar Nanti,Tunai,QRIS',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $service = Service::find($data['service_id']);
        if (!$service) {
            return back()->withErrors(['service_id' => 'Layanan tidak ditemukan. Silakan hubungi admin.']);
        }

        
        $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

        
        $order = new Order();
        $order->order_number = $orderNumber;
        $order->customer_id = Auth::id();
        
        $order->customer_phone = Auth::user()->phone ?? null;
        $order->pickup_address = $data['address'] ?? Auth::user()->address ?? null;
        $order->address = $data['address'] ?? Auth::user()->address ?? null;
        $order->service_id = $service->id;
        
        $order->actual_weight = null;
        $order->total_price = 0;
        $order->payment_method = $data['payment_method'] ?? 'Bayar Nanti';
        $order->status = 'pending';
        $order->is_walk_in = false;
        
        if (! $order->is_walk_in) {
            if (Schema::hasColumn('orders', 'courier_id')) {
            
            $courier = User::where('role', 'courier')
                ->withCount(['assignedOrders as active_pickup_count' => function($q) {
                    $q->whereIn('status', ['pending', 'menunggu_jemput', 'in_progress', 'di_laundry']);
                }])
                ->orderBy('active_pickup_count', 'asc')
                ->first();

            if ($courier) {
                $order->courier_id = $courier->id;
                \Illuminate\Support\Facades\Log::info('Order ' . $orderNumber . ' assigned to courier: ' . $courier->id);
            }
            } 
            else {
                \Illuminate\Support\Facades\Log::warning('Skipping courier assignment: orders.courier_id column not found. Run migrations?');
            }
        }
        
        $order->save();

        
        if (!empty($order->courier_id)) {
            $courierName = $courier->name ?? 'Kurir';
            return redirect()->route('customer.order.show', ['order' => $order->id])->with('success', 'Pesanan berhasil dibuat! Kurir ' . $courierName . ' akan menjemput pesanan Anda.');
        }

        
        return redirect()->route('customer.order.show', ['order' => $order->id])->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi penjemputan.');
    }

    /**
     * Tampilkan riwayat transaksi lengkap dan arsip digital (e-invoice).
     */
    public function history()
    {
        
        /** @var \App\Models\User $user */
        $user = Auth::user();

        
        $orders = Order::where('customer_id', $user->id)
            ->with('service', 'courier')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        
        $totalCount = Order::where('customer_id', $user->id)->count();
        
        $activeCount = Order::where('customer_id', $user->id)->whereIn('status', ['pending', 'menunggu_jemput', 'in_progress', 'processing', 'di_laundry', 'dijemput', 'diantar', 'confirmed', 'awaiting_payment'])->count();
        $completedCount = Order::where('customer_id', $user->id)->whereIn('status', ['selesai', 'completed', 'paid', 'delivered'])->count();
        $cancelledCount = Order::where('customer_id', $user->id)->whereIn('status', ['rejected', 'cancelled'])->count();

        return view('customer.order.history', compact('orders', 'totalCount', 'activeCount', 'completedCount', 'cancelledCount'));
    }

    /**
     * Tampilkan detail perkembangan pesanan (Timeline Status Detail).
     * Pelanggan melihat Status Detail dan Notifikasi Harga Final di sini.
     */
    public function showTimeline($orderId)
    {
        
        if (! Auth::check()) {
            abort(401);
        }

        /** @var User $user */
        $user = Auth::user();

        
        $order = Order::where('customer_id', $user->id)
            ->with('service', 'courier')
            ->findOrFail($orderId);

        
        $timeline = [];
        $timeline[] = ['label' => 'Dibuat', 'time' => $order->created_at];
        if ($order->approved_at) {
            $timeline[] = ['label' => 'Disetujui', 'time' => $order->approved_at];
        }
        if ($order->customer_confirmed_at) {
            
            $label = ($order->payment_method === 'Bayar Nanti') ? 'Tagihan Dibuat' : 'Dikonfirmasi Pelanggan';
            $timeline[] = ['label' => $label, 'time' => $order->customer_confirmed_at];
        }
        if ($order->status === 'awaiting_confirmation') {
            $timeline[] = ['label' => 'Menunggu Konfirmasi Pelanggan', 'time' => $order->approved_at ?? $order->updated_at];
        }
        
        if (in_array($order->status, ['processing', 'di_laundry', 'in_laundry'])) {
            $timeline[] = ['label' => 'Sedang Diproses', 'time' => $order->customer_confirmed_at ?? $order->updated_at];
        }
        if ($order->status === 'ready_for_delivery') {
            $timeline[] = ['label' => 'Siap Dikirim', 'time' => $order->updated_at];
        }
        if ($order->collected_at) {
            $timeline[] = ['label' => 'Dibayar / Diambil', 'time' => $order->collected_at];
        }
        if ($order->status === 'dijemput') {
            $timeline[] = ['label' => 'Dijemput', 'time' => $order->updated_at];
        }
        if ($order->status === 'diantar') {
            $timeline[] = ['label' => 'Diantar', 'time' => $order->updated_at];
        }
        if ($order->status === 'selesai' || $order->status === 'delivered') {
            $timeline[] = ['label' => 'Selesai', 'time' => $order->updated_at];
        }

        return view('customer.timeline', compact('order', 'timeline'));
    }

    /**
     * Confirm the price for an order (authenticated customer flow)
     * POST /customer/order/{order}/confirm
     */
    public function confirmPrice(Request $request, $orderId)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengonfirmasi harga.');
        }

        $order = Order::where('id', $orderId)->where('customer_id', Auth::id())->firstOrFail();

        
        if (! in_array($order->status, ['awaiting_confirmation', 'approved', 'pending'])) {
            return back()->with('error', 'Pesanan tidak berada pada status yang memungkinkan konfirmasi harga.');
        }

        
        if ($order->customer_confirmed) {
            return back()->with('info', 'Anda sudah mengonfirmasi harga untuk pesanan ini.');
        }

        $order->customer_confirmed = true;
        $order->customer_confirmed_at = now();

        $order->confirmation_token = null;

        
        if (in_array($order->status, ['awaiting_confirmation', 'approved', 'pending'])) {
            $order->status = 'processing';
        }
        $order->save();

        \Illuminate\Support\Facades\Log::info('Customer confirmed order price: ' . $order->id . ' by user: ' . Auth::id());

        return redirect()->route('customer.order.show', ['order' => $order->id])->with('success', 'Terima kasih — harga pesanan telah dikonfirmasi dan pesanan akan segera diproses.');
    }

    /**
     * Tampilkan formulir edit profil.
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('customer.profile.edit', compact('user'));
    }

    /**
     * Update profil pengguna.
     */
    public function updateProfile(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        
        /** @var User $user */ 

        
        $user->update($request->only(['name', 'email', 'phone', 'address']));

        return redirect()->route('customer.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}