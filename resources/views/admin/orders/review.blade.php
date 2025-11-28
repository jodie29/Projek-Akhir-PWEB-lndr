@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Review Order: {{ $order->order_number }}</h1>

    <div class="bg-white shadow rounded p-4 mb-4">
        <p><strong>Layanan:</strong> {{ $order->service->name ?? '-' }}</p>
        <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block font-medium">Berat Aktual (kg)</label>
                <input type="number" step="0.1" name="actual_weight" value="{{ old('actual_weight', $order->actual_weight) }}" class="w-48 border px-3 py-2">
                <p class="text-sm text-gray-500 mt-1">Masukkan berat aktual hasil penimbangan oleh admin/kurir di sini (jika belum ada).</p>
            </div>
            <div class="mb-3">
                <label class="block font-medium">Metode Pembayaran (pilih jika perlu)</label>
                <select name="payment_method" class="w-48 border px-3 py-2">
                    <option value="" {{ empty($order->payment_method) ? 'selected' : '' }}>-- Tidak ditentukan --</option>
                    <option value="Bayar Nanti" {{ ($order->payment_method ?? '') === 'Bayar Nanti' ? 'selected' : '' }}>Bayar Nanti</option>
                    <option value="Tunai" {{ ($order->payment_method ?? '') === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ ($order->payment_method ?? '') === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Pilih metode pembayaran jika pelanggan belum memilih. Jika kosong, default ke 'Bayar Nanti'.</p>
            </div>
            @if(($order->payment_method ?? '') === 'Bayar Nanti')
                <div class="mb-3">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="skip_confirmation" value="1" class="form-checkbox h-4 w-4 text-indigo-600">
                        <span class="text-sm">Lewati konfirmasi pelanggan (admin sudah setuju; prosesi akan langsung dilanjutkan)</span>
                    </label>
                </div>
            @endif
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
            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded" onclick="return confirm('Setujui order dan simpan harga final?')">Setujui</button>
        </form>

        <form action="{{ route('admin.orders.reject', $order->id) }}" method="POST">
            @csrf
            <button class="px-4 py-2 bg-red-600 text-white rounded" onclick="return confirm('Tolak order ini?')">Tolak</button>
        </form>

        {{-- Konfirmasi pelanggan hanya bisa dilakukan oleh pelanggan melalui link token yang dikirim/admin copy --}}
        @if(!empty($order->confirmation_token))
            <div class="mt-4 p-3 bg-indigo-50 rounded">
                <p class="text-sm text-gray-700">Link konfirmasi pelanggan (copy/send ke pelanggan):</p>
                <div class="flex gap-2 items-center">
                    <input id="confirmation-link" type="text" readonly class="w-full border rounded px-2 py-1 text-sm" value="{{ route('order.confirm.show', ['token' => $order->confirmation_token]) }}">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('confirmation-link').value)" class="px-3 py-1 bg-indigo-600 text-white rounded">Salin</button>
                </div>
                <div class="mt-2">
                    <form method="POST" action="{{ route('admin.orders.resend_confirmation', $order->id) }}" onsubmit="return confirm('Kirim ulang email konfirmasi ke pelanggan?')">
                        @csrf
                        <button class="px-3 py-1 bg-indigo-600 text-white rounded">Kirim Ulang Email Konfirmasi</button>
                    </form>
                </div>
            </div>
        @endif

        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 bg-gray-200 text-gray-800 rounded">Kembali ke Dashboard</a>
        @if(!in_array($order->status, ['ready_for_delivery', 'diantar', 'selesai', 'delivered']))
            <form method="POST" action="{{ route('admin.orders.ready_for_delivery', $order->id) }}" onsubmit="return confirm('Tandai order ini siap untuk dikirim?')">
                @csrf
                <div class="flex items-center gap-3 mb-2">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="auto_assign" value="1" class="form-checkbox h-4 w-4 text-indigo-600">
                        <span class="text-sm">Tugaskan otomatis ke kurir (opsional)</span>
                    </label>
                </div>
                <button class="px-3 py-2 bg-teal-600 text-white rounded">Tandai Siap Antar</button>
            </form>
        @endif
        <a href="{{ route('admin.orders.pending') }}" class="px-3 py-2 bg-gray-200 text-gray-800 rounded">Kembali</a>
        <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 bg-gray-200 text-gray-800 rounded">Semua Pesanan</a>
    </div>
</div>
@endsection
