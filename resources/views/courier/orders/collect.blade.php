@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Catat Pembayaran - {{ $order->order_number }}</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow rounded p-4 mb-4">
        <p><strong>Layanan:</strong> {{ $order->service->name ?? '-' }}</p>
        <p><strong>Berat:</strong> {{ $order->actual_weight }} kg</p>
        <p><strong>Total yang harus dibayar:</strong> Rp {{ number_format($order->total_price,0,',','.') }}</p>
    </div>

    <form action="{{ route('courier.orders.collect.store', $order->id) }}" method="POST" class="bg-white shadow rounded p-4">
        @csrf

        <div class="mb-4">
            <label class="block font-medium">Metode Pembayaran</label>
            <select name="collection_method" class="w-full border px-3 py-2" required>
                <option value="Tunai">Tunai</option>
                <option value="QRIS">QRIS</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Jumlah yang dikumpulkan (Rp)</label>
            <input type="number" step="0.01" name="collected_amount" class="w-full border px-3 py-2" value="{{ old('collected_amount', $order->total_price) }}" required>
        </div>

        {{-- Konfirmasi pelanggan hanya boleh dilakukan oleh pelanggan sendiri melalui link konfirmasi --}}

        <div class="flex gap-3 justify-end">
            <a href="javascript:history.back()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan & Catat</button>
        </div>
    </form>
</div>
@endsection
