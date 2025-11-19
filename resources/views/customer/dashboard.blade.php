@extends('layouts.customer_app')

@section('content')
<h2 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-2">Selamat Datang, {{ $user_name ?? 'Pelanggan' }}!</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-indigo-600 text-white rounded-xl p-5 shadow-xl transform transition duration-300 hover:scale-[1.02]">
        <h3 class="text-lg opacity-80">Pesanan Aktif (Proses)</h3>
        <p class="text-4xl font-extrabold mt-2">{{ $active_orders_count ?? 0 }}</p>
    </div>
    <div class="bg-yellow-500 text-white rounded-xl p-5 shadow-xl transform transition duration-300 hover:scale-[1.02]">
        <h3 class="text-lg opacity-80">Menunggu Pembayaran</h3>
        <p class="text-4xl font-extrabold mt-2">{{ $pending_payment_count ?? 0 }}</p>
    </div>
    <div class="bg-green-600 text-white rounded-xl p-5 shadow-xl transform transition duration-300 hover:scale-[1.02]">
        <h3 class="text-lg opacity-80">Total Riwayat Pesanan</h3>
        <p class="text-4xl font-extrabold mt-2">{{ $total_orders_count ?? 0 }}</p>
    </div>
</div>

<div class="mb-8 p-4 bg-white rounded-xl shadow-lg">
    <h3 class="text-xl font-semibold mb-3 text-gray-700">Buat Pesanan Baru</h3>
    <div class="flex flex-wrap items-center gap-4">
        <a href="{{ route('customer.order.create') }}" class="px-5 py-3 bg-blue-600 text-white rounded-lg font-semibold text-lg hover:bg-blue-700 transition shadow-md flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Pesan Layanan Laundry
        </a>
    </div>
</div>

<div class="bg-white shadow-xl rounded-xl p-6">
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Status Pesanan Aktif Terbaru</h3>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border border-gray-200 rounded-md">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-3">No Order</th>
                    <th class="p-3">Layanan</th>
                    <th class="p-3">Tanggal Pesan</th>
                    <th class="p-3">Total (Estimasi)</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_orders as $order)
                <tr class="border-b hover:bg-gray-50 transition duration-100">
                    <td class="p-3 font-medium text-gray-900">{{ $order['order_number'] }}</td>
                    <td class="p-3">{{ $order['service']['name'] }}</td>
                    <td class="p-3">{{ $order['order_date'] }}</td>
                    <td class="p-3 font-semibold">Rp {{ number_format($order['total_price'] ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3">
                        @php
                            $st = $order['status'] ?? 'pending';
                            $badge = 'bg-gray-100 text-gray-800';
                            if ($st === 'paid' || $st === 'completed') { $badge = 'bg-green-100 text-green-700'; }
                            elseif ($st === 'in_progress') { $badge = 'bg-blue-100 text-blue-700'; }
                            elseif ($st === 'confirmed') { $badge = 'bg-purple-100 text-purple-700'; }
                            elseif ($st === 'pending') { $badge = 'bg-orange-100 text-orange-800'; }
                            elseif ($st === 'awaiting_payment') { $badge = 'bg-red-100 text-red-700'; }
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $badge }} font-medium whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $st)) }}</span>
                    </td>
                    <td class="p-3">
                        <a href="{{ route('customer.order.show', $order['order_number']) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-500 bg-gray-50">Anda belum memiliki pesanan aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(($total_orders_count ?? 0) > 0)
    <div class="mt-4 text-right">
        <a href="{{ route('customer.order.history') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">Lihat Semua Riwayat Pesanan &rarr;</a>
    </div>
    @endif
</div>
@endsection
```eof