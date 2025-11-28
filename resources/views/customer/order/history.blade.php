@extends('customer.layout.customer_app')

@section('title', 'Riwayat Pesanan')

@section('content')
    <div class="max-w-7xl mx-auto">
    <x-header-illustration title="Riwayat Semua Pesanan Anda" :image="'https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png'"/>
    <div class="flex items-center justify-between mb-6 border-b pb-2">
        <h1 class="text-3xl font-bold text-gray-800">Riwayat Semua Pesanan Anda</h1>
        <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3 8 4-18 3 8h4"/></svg>
            Kembali ke Dashboard
        </a>
    </div>
    
    <!-- Bagian Filter dan Pencarian -->
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <!-- Filter Status (Tabs/Buttons) -->
            <div class="flex space-x-2 overflow-x-auto pb-2">
                <button class="px-4 py-2 text-sm font-medium rounded-full bg-indigo-600 text-white shadow-md transition duration-150">Semua ({{ $totalCount ?? 0 }})</button>
                <button class="px-4 py-2 text-sm font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition duration-150">Aktif ({{ $activeCount ?? 0 }})</button>
                <button class="px-4 py-2 text-sm font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition duration-150">Selesai ({{ $completedCount ?? 0 }})</button>
                <button class="px-4 py-2 text-sm font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition duration-150">Dibatalkan ({{ $cancelledCount ?? 0 }})</button>
            </div>
            
            <!-- Pencarian -->
            <div class="w-full md:w-auto">
                <div class="relative">
                    <input type="text" placeholder="Cari ID Pesanan..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Pesanan (Tabel Responsif) -->
    <div class="bg-white rounded-xl shadow-xl overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pickup</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total (Rp)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Tagihan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                
                @forelse($orders as $order)
                <tr class="border-b hover:bg-gray-50 transition duration-100">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-700">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $order->service->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $st = $order->status ?? 'pending';
                            $badge = 'bg-gray-100 text-gray-800';
                            if (in_array($st, ['paid','completed','selesai','delivered'])) { $badge = 'bg-green-100 text-green-700'; }
                            elseif (in_array($st, ['in_progress','di_laundry', 'dijemput', 'diantar'])) { $badge = 'bg-blue-100 text-blue-700'; }
                            elseif (in_array($st, ['confirmed','menunggu_konfirmasi'])) { $badge = 'bg-purple-100 text-purple-700'; }
                            elseif (in_array($st, ['pending','menunggu_jemput'])) { $badge = 'bg-orange-100 text-orange-800'; }
                            elseif (in_array($st, ['awaiting_payment','pending_payment'])) { $badge = 'bg-red-100 text-red-700'; }
                        @endphp
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->total_price && $order->total_price > 0 ? 'Rp ' . number_format($order->total_price, 0, ',', '.') : 'Belum Final' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->customer_confirmed_at ? $order->customer_confirmed_at->format('Y-m-d H:i') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('customer.order.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold transition duration-150">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-500 bg-gray-50">Anda belum memiliki pesanan aktif.</td>
                </tr>
                @endforelse
                
                <!-- Tambahkan lebih banyak baris pesanan di sini jika perlu -->
                
            </tbody>
        </table>

        <!-- Paginasi Sederhana -->
        <div class="px-6 py-4 border-t flex flex-col md:flex-row justify-between items-center gap-3">
            <div class="text-sm text-gray-600">
                Menampilkan {{ $orders->firstItem() ?? 0 }} hingga {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() ?? 0 }} Pesanan
            </div>
            <div class="mt-2 md:mt-0">{{ $orders->links() }}</div>
        </div>
    </div>
</div>
@endsection