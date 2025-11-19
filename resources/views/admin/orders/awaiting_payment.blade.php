@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Pesanan Menunggu Pembayaran</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-3 py-2">No Order</th>
                <th class="border px-3 py-2">Layanan</th>
                <th class="border px-3 py-2 text-right">Berat (kg)</th>
                <th class="border px-3 py-2 text-right">Total</th>
                <th class="border px-3 py-2">Pelanggan Setuju</th>
                <th class="border px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="border px-3 py-2">{{ $order->order_number }}</td>
                    <td class="border px-3 py-2">{{ $order->service->name ?? '-' }}</td>
                    <td class="border px-3 py-2 text-right">{{ $order->actual_weight }}</td>
                    <td class="border px-3 py-2 text-right">Rp {{ number_format($order->total_price,0,',','.') }}</td>
                    <td class="border px-3 py-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Ya</span>
                    </td>
                    <td class="border px-3 py-2">
                        <a href="{{ route('admin.orders.collect', $order->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Catat Pembayaran</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="border px-3 py-2">Tidak ada pesanan menunggu pembayaran.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
