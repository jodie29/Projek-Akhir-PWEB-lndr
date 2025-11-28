@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Edit Order: {{ $order->order_number }}</h1>

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="bg-white shadow rounded p-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block font-medium">Total Harga</label>
            <input type="number" step="0.01" name="total_price" value="{{ old('total_price', $order->total_price) }}" class="w-full border px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block font-medium">Berat Aktual (kg)</label>
            <input type="number" step="0.1" name="actual_weight" value="{{ old('actual_weight', $order->actual_weight) }}" class="w-full border px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block font-medium">Metode Pembayaran</label>
            <select name="payment_method" class="w-full border px-3 py-2">
                <option {{ $order->payment_method == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                <option {{ $order->payment_method == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                <option {{ $order->payment_method == 'Bayar Nanti' ? 'selected' : '' }}>Bayar Nanti</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Status</label>
                <select name="status" class="w-full border px-3 py-2">
                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>pending</option>
                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>confirmed</option>
                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>paid</option>
                    @if(!($order->payment_method === 'Bayar Nanti' && !($order->customer_confirmed ?? false)))
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>processing</option>
                        <option value="ready_for_delivery" {{ $order->status == 'ready_for_delivery' ? 'selected' : '' }}>ready_for_delivery</option>
                    @endif
                <option value="rejected" {{ $order->status == 'rejected' ? 'selected' : '' }}>rejected</option>
            </select>
                @if(($order->payment_method ?? '') === 'Bayar Nanti' && !($order->customer_confirmed ?? false))
                    <p class="text-xs text-yellow-600 mt-1">Status "processing" hanya dapat dipilih setelah pelanggan mengonfirmasi harga.</p>
                @endif
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
