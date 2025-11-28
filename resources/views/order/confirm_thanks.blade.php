@extends('layouts.app')

@section('content')
<div class="container py-8">
    <h1>Terima Kasih</h1>
    <p>Terima kasih, harga pesanan Anda telah dikonfirmasi. Pesanan akan segera diproses oleh pihak laundry.</p>
    <div class="card mt-4 p-4 bg-white rounded">
        <p><strong>Nomor Pesanan:</strong> {{ $order->order_number }}</p>
        <p><strong>Layanan:</strong> {{ $order->service->name ?? '-' }}</p>
        <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        <p><strong>Status:</strong> {{ $order->status }}</p>
    </div>
</div>
@endsection
