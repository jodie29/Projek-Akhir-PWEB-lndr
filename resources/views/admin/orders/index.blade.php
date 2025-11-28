@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Manajemen Pesanan</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded shadow">Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-3 py-2">No Order</th>
                <th class="border px-3 py-2">Layanan</th>
                <th class="border px-3 py-2">Status</th>
                <th class="border px-3 py-2">Total</th>
                <th class="border px-3 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="border px-3 py-2">{{ $order->order_number }}</td>
                    <td class="border px-3 py-2">{{ $order->service->name ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ $order->status }}</td>
                    <td class="border px-3 py-2">Rp {{ number_format($order->total_price ?? 0,0,',','.') }}</td>
                    <td class="border px-3 py-2">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Lihat</a>
                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="px-3 py-1 bg-yellow-400 text-white rounded">Edit</a>
                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-600 text-white rounded" onclick="return confirm('Hapus order ini?')">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="border px-3 py-2">Tidak ada order.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
