@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-semibold mb-6">Transaksi Walk-in</h2>

<form action="{{ route('transactions.store') }}" method="POST" class="bg-white shadow rounded-xl p-6 max-w-2xl">
    @csrf

    <!-- Pilih Layanan -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Layanan</label>
        <select name="service_id" id="service_id" class="w-full border rounded-md p-2" required>
            <option value="">-- Pilih Layanan --</option>
            @foreach($services as $s)
                <option value="{{ $s->id }}" data-price="{{ $s->price_per_kg }}">
                    {{ $s->name }} — Rp {{ number_format($s->price_per_kg, 0, ',', '.') }}/kg
                </option>
            @endforeach
        </select>
    </div>

    <!-- Input Berat -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Berat Aktual (kg)</label>
        <input type="number" step="0.1" min="0.1" name="actual_weight" id="actual_weight"
               class="w-full border rounded-md p-2" placeholder="Contoh: 3.5" required>
    </div>

    <!-- Total Harga -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Total Harga</label>
        <input type="text" id="total_price" name="total_price" readonly
               class="w-full border rounded-md p-2 bg-gray-100 font-semibold text-blue-700">
    </div>

    <!-- Metode Pembayaran -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
        <select name="payment_method" class="w-full border rounded-md p-2" required>
            <option value="">-- Pilih Metode --</option>
            <option value="Tunai">Tunai</option>
            <option value="QRIS">QRIS</option>
        </select>
    </div>

    <!-- Tombol Submit -->
    <div class="flex justify-end gap-4">
        <button type="reset" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">Reset</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Simpan Transaksi</button>
    </div>
</form>

<!-- Script Hitung Otomatis -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const serviceSelect = document.getElementById('service_id');
    const weightInput = document.getElementById('actual_weight');
    const totalField = document.getElementById('total_price');

    function hitungTotal() {
        const hargaPerKg = parseFloat(serviceSelect.selectedOptions[0]?.dataset.price || 0);
        const berat = parseFloat(weightInput.value) || 0;
        const total = hargaPerKg * berat;
        totalField.value = total ? 'Rp ' + total.toLocaleString('id-ID') : '';
    }

    serviceSelect.addEventListener('change', hitungTotal);
    weightInput.addEventListener('input', hitungTotal);
});
</script>
@endsection
