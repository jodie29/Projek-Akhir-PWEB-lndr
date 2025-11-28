@extends('customer.layout.customer_app')

@section('title', 'Timeline Pesanan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Detail & Timeline Pesanan</h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('customer.order.history') }}" class="text-sm text-gray-600 hover:text-indigo-600">Kembali ke Riwayat</a>
            <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition duration-150">Dashboard</a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-lg font-semibold">Order: {{ $order->order_number }}</h2>
                <p class="text-sm text-gray-600">Layanan: {{ $order->service->name ?? '-' }}</p>
                <p class="text-sm text-gray-600">Status: <strong class="text-indigo-600">{{ $order->status }}</strong></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Total Harga:</p>
                <p class="text-xl font-bold">Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        @if(in_array($order->status, ['awaiting_confirmation', 'approved', 'pending']) && ! $order->customer_confirmed && Auth::id() === $order->customer_id)
            <div class="mt-4">
                <form method="POST" action="{{ route('customer.order.confirm_price', $order->id) }}" onsubmit="return confirm('Konfirmasi harga akan mengubah status menjadi Sedang Diproses. Lanjutkan?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Konfirmasi Harga</button>
                </form>
            </div>
        @endif

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Berat: {{ $order->actual_weight ?? '-' }} kg</p>
                <p class="text-sm text-gray-500">Metode Pembayaran: {{ $order->payment_method ?? '-' }}</p>
                <p class="text-sm text-gray-500">Alamat Pickup: {{ $order->customer->address ?? Auth::user()->address ?? ($order->pickup_address ?? '-') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Kurir: {{ $order->courier->name ?? 'Belum ditugaskan' }}</p>
                <p class="text-sm text-gray-500">Dibuat: {{ $order->created_at->format('Y-m-d H:i') }}</p>
                <p class="text-sm text-gray-500">Terakhir diperbarui: {{ $order->updated_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Timeline</h3>
        <ol class="relative border-l border-gray-200">
            @foreach($timeline as $event)
            <li class="mb-6 ml-4">
                <span class="absolute -left-2 top-2 w-4 h-4 rounded-full bg-indigo-600 border-2 border-white"></span>
                <div class="text-sm font-semibold text-gray-800">{{ $event['label'] }}</div>
                <div class="text-xs text-gray-500">{{ $event['time'] ? $event['time']->format('Y-m-d H:i') : '-' }}</div>
            </li>
            @endforeach
        </ol>
    </div>
</div>
@endsection
