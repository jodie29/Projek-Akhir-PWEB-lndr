@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Kasir Cepat (Walk-in)</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.cashier.store') }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf

        <div class="mb-4">
            <label class="block font-medium">Pilih Layanan</label>
            <select name="service_id" id="service_id" class="w-full border px-3 py-2" required>
                <option value="">-- Pilih Layanan --</option>
                @foreach($services as $s)
                    <option value="{{ $s->id }}" data-price="{{ $s->price_per_kg }}">{{ $s->name }} — Rp {{ number_format($s->price_per_kg,0,',','.') }}/kg</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Berat Aktual (kg)</label>
            <input type="number" step="0.01" min="0.1" name="actual_weight" id="actual_weight" class="w-full border px-3 py-2" required>
        </div>

        <!-- Walk-in cashier: delivery not applicable -->

        <div class="mb-4">
            <label class="block font-medium">Metode Pembayaran</label>
            <select name="payment_method" id="payment_method" class="w-full border px-3 py-2" required>
                <option value="Tunai">Tunai</option>
                <option value="QRIS">QRIS</option>
                <option value="Bayar Nanti">Bayar Nanti (Catat, Bayar diambil)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Total Harga</label>
            <input type="text" id="total_price" readonly class="w-full border px-3 py-2 bg-gray-100 font-semibold">
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded">Kembali</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan & Lunas</button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const service = document.getElementById('service_id');
        const weight = document.getElementById('actual_weight');
        const delivery = document.getElementById('delivery');
        const totalPrice = document.getElementById('total_price');

        function calc() {
            const price = parseFloat(service.selectedOptions[0]?.dataset.price || 0);
            const w = parseFloat(weight.value) || 0;
            const ship = 0; // walk-in has no delivery fee
            const total = price * w + ship;
            totalPrice.value = total ? 'Rp ' + Math.round(total).toLocaleString('id-ID') : '';
        }

        service.addEventListener('change', calc);
        weight.addEventListener('input', calc);
        delivery.addEventListener('change', calc);
    });
    </script>
</div>
@endsection
