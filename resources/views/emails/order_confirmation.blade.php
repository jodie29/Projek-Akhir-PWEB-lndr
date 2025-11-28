@extends('layouts.app')

@section('content')
<div style="font-family: Arial, sans-serif; color: #333;">
    <h2>Konfirmasi Harga untuk Pesanan: {{ $order->order_number }}</h2>
    <p>Halo {{ $order->customer->name ?? 'Pelanggan' }},</p>
    <p>Pihak laundry telah menghitung berat dan harga final untuk pesanan Anda:</p>
    <ul>
        <li>Berat aktual: {{ $order->actual_weight ?? '-' }} kg</li>
        <li>Total harga: Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</li>
        <li>Metode pembayaran: {{ $order->payment_method ?? '-' }}</li>
    </ul>
    <p>Silakan konfirmasi harga dengan mengklik tombol di bawah ini:</p>
    <p><a href="{{ route('order.confirm.show',['token'=>$order->confirmation_token]) }}" style="display:inline-block;padding:10px 16px;background:#1e3a8a;color:white;border-radius:6px;text-decoration:none;">Konfirmasi Harga</a></p>
    <p>Jika Anda tidak setuju dengan harga, silakan hubungi customer service atau batalkan pesanan Anda.</p>
    <p>Terima kasih,<br>Tim PowerWash Laundry</p>
</div>
@endsection
