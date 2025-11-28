@extends('layouts.app')

@section('content')

<x-header-illustration title="Daftar Transaksi" :image="'https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png'"/> 

<a href="{{ route('transactions.create') }}" 
   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
   + Tambah Transaksi Walk-in
</a>

<table class="w-full mt-4 text-sm text-left border border-gray-200 rounded-md">
    <thead class="bg-gray-100 border-b">
        <tr>
            <th class="p-2">No Order</th>
            <th class="p-2">Layanan</th>
            <th class="p-2">Berat</th>
            <th class="p-2">Total</th>
            <th class="p-2">Metode</th>
            <th class="p-2">Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr class="border-b hover:bg-gray-50">
            <td class="p-2">{{ $order->order_number }}</td>
            <td class="p-2">{{ $order->service->name }}</td>
            <td class="p-2">{{ $order->actual_weight }} kg</td>
            <td class="p-2">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            <td class="p-2">{{ $order->payment_method }}</td>
            <td class="p-2">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center p-4 text-gray-500">Belum ada transaksi.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
