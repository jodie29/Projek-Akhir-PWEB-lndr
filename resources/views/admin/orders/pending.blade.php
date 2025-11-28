@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Tinjau Harga - Pesanan Pending</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded shadow">Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif

    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.orders.pending') }}" class="px-3 py-2 bg-gray-200 text-gray-800 rounded">Semua</a>
        <a href="{{ route('admin.orders.pending', ['confirmed' => 1]) }}" class="px-3 py-2 bg-green-100 text-green-800 rounded">Sudah Setuju</a>
        <a href="{{ route('admin.orders.pending', ['confirmed' => 0]) }}" class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded">Belum Setuju</a>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-3 py-2">No Order</th>
                <th class="border px-3 py-2">Layanan</th>
                <th class="border px-3 py-2 text-right">Berat Aktual (kg)</th>
                <th class="border px-3 py-2 text-right">Est. Harga</th>
                <th class="border px-3 py-2">Metode</th>
                <th class="border px-3 py-2">Pelanggan Setuju</th>
                <th class="border px-3 py-2">Tanggal Konfirmasi</th>
                <th class="border px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="border px-3 py-2">{{ $order->order_number }}</td>
                    <td class="border px-3 py-2">{{ $order->service->name ?? '-' }}</td>
                    <td class="border px-3 py-2 text-right">{{ $order->actual_weight ?? '-' }}</td>
                    <td class="border px-3 py-2 text-right">{{ $order->actual_weight ? 'Rp ' . number_format(($order->service->price_per_kg ?? 0) * $order->actual_weight, 0, ',', '.') : '-' }}</td>
                    <td class="border px-3 py-2">{{ $order->payment_method ?? '-' }}</td>
                    <td class="border px-3 py-2">
                        @if($order->customer_confirmed ?? false)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Ya</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Belum</span>
                        @endif
                    </td>
                    <td class="border px-3 py-2">
                        {{ $order->customer_confirmed_at ? $order->customer_confirmed_at->format('d-m-Y H:i') : '-' }}
                    </td>
                    <td class="border px-3 py-2">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.orders.review', $order->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Tinjau</a>
                            @if($order->status === 'awaiting_confirmation' && !empty($order->confirmation_token))
                                <form method="POST" action="{{ route('admin.orders.resend_confirmation', $order->id) }}" onsubmit="return confirm('Kirim ulang email konfirmasi ke pelanggan?')">
                                    @csrf
                                    <button class="px-3 py-1 bg-indigo-600 text-white rounded">Kirim Ulang Konfirmasi</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="border px-3 py-2">Tidak ada pesanan pending.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
