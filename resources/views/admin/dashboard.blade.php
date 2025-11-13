@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-semibold mb-4">Dashboard Admin</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-blue-500 text-white rounded-xl p-5 shadow-md">
        <h3 class="text-lg">Total Transaksi</h3>
        <p class="text-3xl font-bold mt-2">{{ $total_orders ?? 0 }}</p>
    </div>
    <div class="bg-green-500 text-white rounded-xl p-5 shadow-md">
        <h3 class="text-lg">Pemasukan Hari Ini</h3>
        <p class="text-3xl font-bold mt-2">Rp {{ number_format($income_today ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="bg-yellow-500 text-white rounded-xl p-5 shadow-md">
        <h3 class="text-lg">Layanan Aktif</h3>
        <p class="text-3xl font-bold mt-2">{{ $total_services ?? 0 }}</p>
    </div>
</div>

<div class="bg-white shadow rounded-xl p-5">
    <h3 class="text-xl font-semibold mb-4">Riwayat Transaksi Terbaru</h3>

    <table class="w-full text-sm text-left border border-gray-200 rounded-md">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="p-2">No Order</th>
                <th class="p-2">Layanan</th>
                <th class="p-2">Berat</th>
                <th class="p-2">Total</th>
                <th class="p-2">Metode</th>
                <th class="p-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-2">{{ $order->order_number }}</td>
                <td class="p-2">{{ $order->service->name }}</td>
                <td class="p-2">{{ $order->actual_weight }} kg</td>
                <td class="p-2">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                <td class="p-2">{{ $order->payment_method }}</td>
                <td class="p-2">
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $order->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center p-4 text-gray-500">Belum ada transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
