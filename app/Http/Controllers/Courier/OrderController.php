<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Pastikan Log diimpor

class OrderController extends Controller
{
    /**
     * Menampilkan formulir pencatatan pembayaran (collection) untuk kurir.
     * Menggunakan Route Model Binding untuk Order.
     * (Route: courier.orders.pickup)
     */
    public function collectForm(Order $order)
    {
        // Safety: Make sure migration for courier_id is applied
        if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'courier_id')) {
            return redirect()->route('courier.dashboard')->with('error', 'Kolom `courier_id` belum tersedia. Silakan jalankan `php artisan migrate`.');
        }
        // Pastikan order ditugaskan ke kurir ini
        if ($order->courier_id !== Auth::id()) {
            return redirect()->route('courier.dashboard')->with('error', 'Pesanan ini tidak ditugaskan kepada Anda.');
        }

        // Pastikan order pada status yang memungkinkan pembayaran
        // (misal: "approved", "awaiting_collection", "ready_for_delivery", "dijemput" atau "diantar").
        if (!in_array($order->status, ['approved', 'awaiting_collection', 'ready_for_delivery', 'dijemput', 'diantar'])) {
            return redirect()->route('courier.dashboard')->with('error', 'Pembayaran untuk pesanan ini tidak dapat dicatat pada status saat ini (' . $order->status . ').');
        }

        $order->load('service', 'customer'); // Load relasi yang diperlukan

        return view('courier.orders.collect', compact('order'));
    }

    /**
     * Mencatat pembayaran yang diterima oleh kurir.
     * (Route: courier.orders.pickup.store)
     */
    public function collect(Request $request, Order $order)
    {
        // Safety: Make sure migration for courier_id is applied
        if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'courier_id')) {
            return redirect()->route('courier.dashboard')->with('error', 'Kolom `courier_id` belum tersedia. Silakan jalankan `php artisan migrate`.');
        }
        // Validasi Awal 1: Pastikan order ditugaskan ke kurir ini
        if ($order->courier_id !== Auth::id()) {
            // Log pesan jika ada upaya akses yang tidak sah
            Log::warning('Unauthorized access attempt to collect payment for Order: ' . $order->id . ' by User: ' . Auth::id());
            return back()->with('error', 'Akses ditolak: Pesanan ini tidak ditugaskan kepada Anda.');
        }
        
        // Validasi Awal 2: Order sudah dibayar?
        if (in_array($order->status, ['paid', 'delivered', 'selesai'])) {
            return back()->with('error', 'Pembayaran untuk pesanan #' . $order->id . ' sudah pernah dicatat.');
        }

        // Pengecekan Kritis: Pastikan total_price adalah nilai numerik yang valid
        $minAmount = is_numeric($order->total_price) && $order->total_price > 0 
                     ? $order->total_price 
                     : 0; // Fallback ke 0 jika tidak valid (seharusnya tidak terjadi)

        if ($minAmount == 0) {
            // Log jika total_price nol atau tidak valid, karena ini anomali data
            Log::error('Order ' . $order->id . ' has invalid or zero total_price: ' . $order->total_price);
            return back()->with('error', 'Kesalahan data: Total harga pesanan tidak valid.');
        }

        $validatedData = $request->validate([
            'collection_method' => 'required|string|in:Tunai,QRIS',
            'collected_amount' => 'required|numeric|min:' . $minAmount, // Gunakan nilai min yang sudah divalidasi
        ], [
            'collected_amount.min' => 'Jumlah yang dikumpulkan harus setidaknya sebesar total harga pesanan (Rp' . number_format($order->total_price, 0, ',', '.') . ').'
        ]);

        try {
            DB::beginTransaction();

            // 1. Catat detail koleksi pembayaran
            $order->collection_method = $validatedData['collection_method'];
            $order->collected_amount = $validatedData['collected_amount'];
            $order->collected_by = Auth::id();
            $order->collected_at = now();

            // Create transaction record in new transactions table
            \App\Models\Transaction::create([
                'order_id' => $order->id,
                'type' => 'collection',
                'amount' => $validatedData['collected_amount'],
                'method' => $validatedData['collection_method'],
                'created_by' => Auth::id(),
                'collected_at' => now(),
                'notes' => 'Collected by courier: ' . Auth::id()
            ]);
            
            // 2. Tandai sebagai Paid
            $order->payment_method = $validatedData['collection_method'];
            // Status diubah dari 'paid' menjadi 'selesai' untuk alur COD (COD handled by courier)
            $order->status = 'selesai'; 

            $order->save();

            DB::commit();

            // Log sukses untuk tujuan audit
            Log::info('Payment collected successfully for Order: ' . $order->id . ' by Courier: ' . Auth::id() . ' with method: ' . $order->collection_method . ' (status: ' . $order->status . ')');

            return redirect()->route('courier.dashboard')->with('success', 'Pembayaran untuk Pesanan #' . $order->id . ' berhasil dicatat dan status diubah menjadi SELESAI.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Perbaikan: Hapus backslash di depan Log::error, karena Log sudah diimpor di atas
            Log::error('Courier collection DB transaction error for Order: ' . $order->id . '. Message: ' . $e->getMessage()); 
            
            // Beri pesan yang lebih umum kepada user
            return back()->with('error', 'Gagal memproses pembayaran karena masalah internal server. Silakan hubungi admin. (Log ID: ' . $order->id . ')');
        }
    }

    /**
     * Claim an unassigned order and assign it to the logged-in courier.
     */
    public function claim(Order $order)
    {
        // Safety: Make sure migration for courier_id is applied
        if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'courier_id')) {
            return back()->with('error', 'Kolom `courier_id` belum tersedia di tabel `orders`. Jalankan `php artisan migrate`.');
        }
        if ($order->courier_id) {
            return back()->with('error', 'Pesanan ini sudah ditugaskan.');
        }

        $order->courier_id = Auth::id();
        // If the order status is empty or 'pending', ensure it marks as waiting
        $order->status = $order->status ?: 'menunggu_jemput';
        $order->save();
        // Add logging for auditing and debugging
        Log::info('Order claimed by courier: ' . $order->id . ' assigned to ' . Auth::id());

        return back()->with('success', 'Anda berhasil mengambil tugas untuk pesanan ' . $order->order_number);
    }

    /**
     * Mark the order as picked up by the courier (status: 'dijemput').
     */
    public function pickedUp(Order $order)
    {
        // Safety: Make sure migration for courier_id exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'courier_id')) {
            return back()->with('error', 'Kolom `courier_id` belum tersedia. Silakan jalankan `php artisan migrate`.');
        }
        // Authorization
        if ($order->courier_id !== Auth::id()) {
            return back()->with('error', 'Pesanan ini tidak ditugaskan kepada Anda.');
        }

        // Only allow picking up if it's waiting for pickup or ready for delivery
        if (! in_array($order->status, ['pending', 'menunggu_jemput', 'ready_for_delivery'])) {
            return back()->with('error', 'Order tidak dapat dijemput pada status saat ini: ' . $order->status);
        }

        $order->status = 'dijemput';
        $order->save();

        // Add logging for debugging
        Log::info('Order marked as picked up (dijemput): ' . $order->id . ' by courier ' . Auth::id());

        return back()->with('success', 'Pesanan ' . $order->order_number . ' berhasil ditandai sebagai DIJEMPUT.');
    }

    /**
     * Mark the order as delivered (status: 'diantar').
     */
    public function delivered(Order $order)
    {
        // Safety: Make sure migration for courier_id exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'courier_id')) {
            return back()->with('error', 'Kolom `courier_id` belum tersedia. Silakan jalankan `php artisan migrate`.');
        }
        // Authorization
        if ($order->courier_id !== Auth::id()) {
            return back()->with('error', 'Pesanan ini tidak ditugaskan kepada Anda.');
        }

        // Only allow deliver if it has been picked up
        if (! in_array($order->status, ['dijemput'])) {
            return back()->with('error', 'Order tidak dapat ditandai diantar pada status saat ini: ' . $order->status);
        }

        // Mark as being delivered
        $order->status = 'diantar';
        $order->save();

        // Add logging for debugging
        Log::info('Order marked as diantar: ' . $order->id . ' by courier ' . Auth::id());

        return back()->with('success', 'Pesanan ' . $order->order_number . ' berhasil ditandai sebagai DIANTAR.');
    }

    /**
     * Mark the order as completed (status: 'selesai') after delivery.
     */
    public function markAsSelesai(Order $order)
    {
        // Safety: Make sure migration for courier_id exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('orders', 'courier_id')) {
            return back()->with('error', 'Kolom `courier_id` belum tersedia. Silakan jalankan `php artisan migrate`.');
        }
        // Authorization
        if ($order->courier_id !== Auth::id()) {
            return back()->with('error', 'Pesanan ini tidak ditugaskan kepada Anda.');
        }

        // Only allow mark as selesai if it's being delivered
        if (! in_array($order->status, ['diantar'])) {
            return back()->with('error', 'Order tidak dapat ditandai selesai pada status saat ini: ' . $order->status);
        }

        // Mark as completed
        $order->status = 'selesai';
        $order->save();

        // Add logging for debugging
        Log::info('Order marked as selesai: ' . $order->id . ' by courier ' . Auth::id());

        return back()->with('success', 'Pesanan ' . $order->order_number . ' berhasil ditandai sebagai SELESAI.');
    }
}