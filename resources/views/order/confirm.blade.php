@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Konfirmasi Harga Pesanan</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <p><strong>Nomor Pesanan:</strong> {{ $order->order_number }}</p>
            <p><strong>Layanan:</strong> {{ $order->service->name ?? '-' }}</p>
            <p><strong>Berat Aktual:</strong> {{ $order->actual_weight }} kg</p>
            <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            <p><strong>Status:</strong> {{ $order->status }}</p>

            @if(!$order->customer_confirmed)
                <form method="POST" action="{{ route('order.confirm.confirm', ['token' => $order->confirmation_token]) }}">
                    @csrf
                    <button class="btn btn-primary">Saya Setuju dengan Harga</button>
                </form>
            @else
                <div class="alert alert-info">Anda telah mengonfirmasi harga pada {{ $order->customer_confirmed_at }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Konfirmasi Harga Pesanan</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <p><strong>Nomor Pesanan:</strong> {{ $order->order_number }}</p>
            <p><strong>Layanan:</strong> {{ $order->service->name ?? '-' }}</p>
            <p><strong>Berat Aktual:</strong> {{ $order->actual_weight }} kg</p>
            <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            <p><strong>Status:</strong> {{ $order->status }}</p>

            @if(!$order->customer_confirmed)
                <form method="POST" action="{{ route('order.confirm.confirm', ['token' => $order->confirmation_token]) }}">
                    @csrf
                    <button class="btn btn-primary">Saya Setuju dengan Harga</button>
                </form>
            @else
                <div class="alert alert-info">Anda telah mengonfirmasi harga pada {{ $order->customer_confirmed_at }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
