@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Review Order: {{ $order->order_number }}</h1>

    <div class="bg-white shadow rounded p-4 mb-4">
        <p><strong>Layanan:</strong> {{ $order->service->name ?? '-' }}</p>
        <p><strong>Berat Aktual:</strong> {{ $order->actual_weight }} kg</p>
        <p><strong>Harga per kg:</strong> Rp {{ number_format($order->service->price_per_kg ?? 0, 0, ',', '.') }}</p>
        <p><strong>Ongkir (flat):</strong> Rp 6.000</p>
        <p class="mt-2"><strong>Perkiraan Harga (tanpa ongkir):</strong> Rp {{ number_format(($order->service->price_per_kg ?? 0) * $order->actual_weight, 0, ',', '.') }}</p>
        @php
            $shipping = ($order->is_walk_in ?? false) ? 0 : 6000;
        @endphp
        <p class="mt-2 text-lg"><strong>Harga Final{{ $shipping ? ' (dengan ongkir)' : '' }}:</strong> Rp {{ number_format(round(($order->service->price_per_kg ?? 0) * $order->actual_weight + $shipping, 2), 0, ',', '.') }}</p>
        @if($order->is_walk_in ?? false)
            <div class="mt-2 p-2 bg-green-50 text-green-800 rounded">Ini pesanan walk-in (kasir) — tidak dikenakan ongkir.</div>
        @endif
        <p class="mt-2"><strong>Metode Pembayaran:</strong> {{ $order->payment_method ?? '-' }}</p>
        @if(($order->payment_method ?? '') === 'Bayar Nanti')
            <div class="mt-2 p-2 bg-yellow-50 text-yellow-800 rounded">
                Ini pesanan "Bayar Nanti" — setujui harga saja; pembayaran akan dicatat saat pengambilan.
            </div>
        @endif
    </div>

    <div class="flex gap-3">
        <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST">
            @csrf
            <button class="px-4 py-2 bg-green-600 text-white rounded" onclick="return confirm('Setujui order dan simpan harga final?')">Setujui</button>
        </form>

        <form action="{{ route('admin.orders.reject', $order->id) }}" method="POST">
            @csrf
            <button class="px-4 py-2 bg-red-600 text-white rounded" onclick="return confirm('Tolak order ini?')">Tolak</button>
        </form>

        {{-- Konfirmasi pelanggan hanya bisa dilakukan oleh pelanggan melalui link token yang dikirim/admin copy --}}

        <a href="{{ route('admin.orders.pending') }}" class="px-3 py-2 bg-gray-200 text-gray-800 rounded">Kembali</a>
    </div>
</div>
@endsection
