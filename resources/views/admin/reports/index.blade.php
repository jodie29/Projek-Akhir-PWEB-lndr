@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-semibold mb-4">Laporan Keuangan</h2>

<!-- Filter -->
<form method="GET" action="{{ route('reports.index') }}" class="flex gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
        <input type="date" name="start_date" value="{{ request('start_date') }}"
               class="border rounded-md p-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
        <input type="date" name="end_date" value="{{ request('end_date') }}"
               class="border rounded-md p-2">
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded-md mt-6 hover:bg-blue-700">
        Tampilkan
    </button>
</form>

<!-- Ringkasan -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-green-500 text-white rounded-xl p-5 shadow-md">
        <h3 class="text-lg">Total Transaksi</h3>
        <p class="text-3xl font-bold mt-2">{{ $total_orders ?? 0 }}</p>
    </div>
    <div class="bg-blue-500 text-white rounded-xl p-5 shadow-md">
        <h3 class="text-lg">Total Pemasukan</h3>
        <p class="text-3xl font-bold mt-2">Rp {{ number_format($total_income ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="bg-yellow-500 text-white rounded-xl p-5 shadow-md">
        <h3 class="text-lg">Pendapatan QRIS</h3>
        <p class="text-3xl font-bold mt-2">Rp {{ number_format($income_qris ?? 0, 0, ',', '.') }}</p>
    </div>
</div>

<!-- Tabel Rekap -->
<div class="bg-white shadow rounded-xl p-5">
    <h3 class="text-xl font-semibold mb-4">Detail Rekap per Layanan</h3>

    <table class="w-full text-sm text-left border border-gray-200 rounded-md">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="p-2">Layanan</th>
                <th class="p-2">Jumlah Transaksi</th>
                <th class="p-2">Total Berat (kg)</th>
                <th class="p-2">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report_details as $item)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-2">{{ $item->service_name }}</td>
                <td class="p-2">{{ $item->total_orders }}</td>
                <td class="p-2">{{ $item->total_weight }}</td>
                <td class="p-2">Rp {{ number_format($item->total_income, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center p-4 text-gray-500">Tidak ada data laporan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
