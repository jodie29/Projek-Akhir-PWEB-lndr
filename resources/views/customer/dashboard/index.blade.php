@extends('customer.layout.customer_app')

@section('title', 'Dashboard Pelanggan')

@section('content')
<div class="space-y-8">
    <x-header-illustration title="Selamat Datang di PowerWash Laundry!" :image="'https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png'"/>

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card label="Pesanan Aktif" :value="$active_orders_count ?? 0" bgClass="bg-white" textClass="text-indigo-700" hint="Pesanan yang sedang diproses saat ini." />
        <x-stat-card label="Riwayat Pesanan" :value="$total_orders_count ?? 0" bgClass="bg-white" textClass="text-green-700" hint="Jumlah total pesanan yang telah selesai." />
        <x-stat-card label="Poin Loyalitas" :value="$loyalty_points ?? 0" bgClass="bg-white" textClass="text-yellow-700" hint="Gunakan poin untuk diskon menarik!" />
    </div>

    <!-- Panggilan Aksi Cepat -->
    <div class="bg-white p-6 rounded-xl shadow-xl border border-blue-200">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-bolt text-blue-500 mr-2"></i> Aksi Cepat
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('customer.order.create') }}" class="bg-blue-600 text-white p-4 rounded-lg shadow-md hover:bg-blue-700 transition duration-150 text-center flex flex-col items-center justify-center">
                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                <span class="font-semibold">Buat Pesanan Baru</span>
                <span class="text-xs opacity-80">Jadwalkan penjemputan sekarang</span>
            </a>
            <a href="{{ route('customer.order.history') }}" class="bg-gray-100 text-gray-800 p-4 rounded-lg shadow-md hover:bg-gray-200 transition duration-150 text-center flex flex-col items-center justify-center">
                <i class="fas fa-receipt fa-2x mb-2"></i>
                <span class="font-semibold">Lihat Riwayat & Detail</span>
                <span class="text-xs opacity-80">Lacak semua transaksi Anda</span>
            </a>
            <a href="{{ route('customer.profile.edit') }}" class="bg-purple-600 text-white p-4 rounded-lg shadow-md hover:bg-purple-700 transition duration-150 text-center flex flex-col items-center justify-center">
                <i class="fas fa-cog fa-2x mb-2"></i>
                <span class="font-semibold">Atur Profil Anda</span>
                <span class="text-xs opacity-80">Perbarui alamat dan preferensi</span>
            </a>
        </div>
    </div>

    <!-- Pesanan Terbaru (5 Terakhir) -->
    <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-list-alt text-gray-500 mr-2"></i> {{ count($recent_orders ?? []) }} Pesanan Terbaru
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total (Rp)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recent_orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->service->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($order->total_price ?? 0, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('customer.order.show', ['order' => $order->id]) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-4 text-sm text-gray-500">Belum ada pesanan terbaru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-right mt-4">
            <a href="{{ route('customer.order.history') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Lihat Semua Riwayat Pesanan &rarr;
            </a>
        </div>
    </div>
</div>
@endsection