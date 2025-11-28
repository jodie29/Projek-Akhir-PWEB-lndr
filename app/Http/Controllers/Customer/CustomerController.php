<?php

namespace App\Http\Controllers\Customer; 

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User; // Pastikan model User diimport
use Illuminate\Support\Facades\Schema; // Untuk pengecekan keberadaan kolom
use App\Models\Service;
use App\Models\Order;

class CustomerController extends Controller
{
    /**
     * Tampilkan Dashboard utama Pelanggan.
     */
    public function dashboard()
    {
        // Logika untuk mengambil ringkasan status pesanan pelanggan
        // Contoh: $recentOrders = Auth::user()->orders()->latest()->take(5)->get();
        return view('customer.dashboard');
    }

    /**
     * Tampilkan formulir untuk membuat pesanan baru (Pemesanan & Opsi Logistik).
     */
    public function createOrder()
    {
        // Tampilkan daftar layanan aktif sehingga bisa dipilih pelanggan
        $services = Service::where('active', true)->get();
        $default_address = Auth::user()->address ?? null;
        return view('customer.order.create', compact('services', 'default_address'));
    }

    /**
     * Proses penyimpanan pesanan baru.
     */
    public function storeOrder(Request $request)
    {
        // Validasi input dasar
        $data = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            // Allow payment_method to be optional because actual total will be computed after weight is known
            'payment_method' => 'nullable|string|in:Bayar Nanti,Tunai,QRIS',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        // Temukan service yang sesuai berdasarkan ID
        $service = Service::find($data['service_id']);
        if (!$service) {
            return back()->withErrors(['service_id' => 'Layanan tidak ditemukan. Silakan hubungi admin.']);
        }

        // Buat nomor order yang unik
        $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

        // Simpan order ke database
        $order = new Order();
        $order->order_number = $orderNumber;
        $order->customer_id = Auth::id();
        $order->service_id = $service->id;
        // actual_weight will be set by admin/kurir during approval/pickup
        $order->actual_weight = null;
        $order->total_price = 0; // dihitung saat approval/penimbangan nanti
        // If not selected by the customer, default to 'Bayar Nanti' for compatibility
        // with database schema. UI will show this as a preference, and the final
        // invoice is still generated after admin weighing and/or customer confirmation.
        $order->payment_method = $data['payment_method'] ?? 'Bayar Nanti';
        $order->status = 'pending';
        $order->is_walk_in = false;
        // Jika pesanan membutuhkan pickup (bukan walk-in), coba assign kurir
        if (! $order->is_walk_in) {
            // Safety check: pastikan migration menambahkan kolom `courier_id` ke tabel orders
            if (Schema::hasColumn('orders', 'courier_id')) {
            // Cari kurir dengan jumlah tugas terendah (bereputasi 'least-loaded')
            $courier = User::where('role', 'courier')
                ->withCount(['assignedOrders as active_pickup_count' => function($q) {
                    // Hitung status yang berarti masih ditangani oleh kurir
                    $q->whereIn('status', ['pending', 'menunggu_jemput', 'in_progress', 'di_laundry']);
                }])
                ->orderBy('active_pickup_count', 'asc')
                ->first();

            if ($courier) {
                $order->courier_id = $courier->id;
                // Log sederhana agar admin/dev bisa men-trace assignment (notification optional)
                \Illuminate\Support\Facades\Log::info('Order ' . $orderNumber . ' assigned to courier: ' . $courier->id);
            }
            } // end Schema::hasColumn safety check
            else {
                \Illuminate\Support\Facades\Log::warning('Skipping courier assignment: orders.courier_id column not found. Run migrations?');
            }
        }
        // Simpan catatan pickup / alamat di tabel users atau di order jika nanti ingin ditambahkan
        $order->save();

        // Jika sudah berhasil disimpan dan ada kurir, beri tahu pengguna (flash) bahwa kurir ditugaskan
        if (!empty($order->courier_id)) {
            $courierName = $courier->name ?? 'Kurir';
            return redirect()->route('customer.order.show', ['order' => $order->id])->with('success', 'Pesanan berhasil dibuat! Kurir ' . $courierName . ' akan menjemput pesanan Anda.');
        }

        // Redirect ke timeline / detail order pelanggan
        return redirect()->route('customer.order.show', ['order' => $order->id])->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi penjemputan.');
    }

    /**
     * Tampilkan riwayat transaksi lengkap dan arsip digital (e-invoice).
     */
    public function history()
    {
        // Ambil riwayat pesanan yang sesungguhnya dari database untuk pelanggan yang sedang login
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $orders = $user->orders()->with('service', 'courier')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Counters for filters
        $totalCount = $user->orders()->count();
        // Include courier-specific statuses so customers can see progress after status changes
        $activeCount = $user->orders()->whereIn('status', ['pending', 'menunggu_jemput', 'in_progress', 'processing', 'di_laundry', 'dijemput', 'diantar', 'confirmed', 'awaiting_payment'])->count();
        $completedCount = $user->orders()->whereIn('status', ['selesai', 'completed', 'paid', 'delivered'])->count();
        $cancelledCount = $user->orders()->whereIn('status', ['rejected', 'cancelled'])->count();

        return view('customer.order.history', compact('orders', 'totalCount', 'activeCount', 'completedCount', 'cancelledCount'));
    }

    /**
     * Tampilkan detail perkembangan pesanan (Timeline Status Detail).
     * Pelanggan melihat Status Detail dan Notifikasi Harga Final di sini.
     */
    public function showTimeline($orderId)
    {
        // Pastikan user sudah login (route sudah dilindungi oleh middleware 'auth')
        if (! Auth::check()) {
            abort(401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Lebih aman memanggil Order::where('customer_id', Auth::id()) daripada relying on
        // $user->orders() for static analysis and to avoid surprises if relation is not present.
        $order = Order::with('service', 'courier')
            ->where('customer_id', $user->id)
            ->findOrFail($orderId);

        // Membangun event timeline sederhana berdasarkan atribut order
        $timeline = [];
        $timeline[] = ['label' => 'Dibuat', 'time' => $order->created_at];
        if ($order->approved_at) {
            $timeline[] = ['label' => 'Disetujui', 'time' => $order->approved_at];
        }
        if ($order->customer_confirmed_at) {
            // Jelaskan bahwa konfirmasi pelanggan juga merupakan titik di mana 'tagihan' / invoice
            // dianggap dibuat jika metode pembayaran 'Bayar Nanti'. Jika metode bukan Bayar Nanti,
            // beri label generik.
            $label = ($order->payment_method === 'Bayar Nanti') ? 'Tagihan Dibuat' : 'Dikonfirmasi Pelanggan';
            $timeline[] = ['label' => $label, 'time' => $order->customer_confirmed_at];
        }
        if ($order->status === 'awaiting_confirmation') {
            $timeline[] = ['label' => 'Menunggu Konfirmasi Pelanggan', 'time' => $order->approved_at ?? $order->updated_at];
        }
        // Insert 'Sedang Diproses' once the customer confirms and we changed the order status
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

        // Only allow confirmation when awaiting_confirmation or other statuses where confirmation is expected
        if (! in_array($order->status, ['awaiting_confirmation', 'approved', 'pending'])) {
            return back()->with('error', 'Pesanan tidak berada pada status yang memungkinkan konfirmasi harga.');
        }

        // If already confirmed by customer, return success
        if ($order->customer_confirmed) {
            return back()->with('info', 'Anda sudah mengonfirmasi harga untuk pesanan ini.');
        }

        $order->customer_confirmed = true;
        $order->customer_confirmed_at = now();
        $order->confirmation_token = null; // invalidate token

        // Move order to processing if appropriate
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
        // 1. Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        // 2. Type Hinting untuk menghilangkan error Intelephense
        /** @var User $user */ 

        // 3. Update data yang diizinkan oleh $fillable di Model User.php
        $user->update($request->only(['name', 'email', 'phone', 'address']));

        return redirect()->route('customer.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}