@extends('customer.layout.customer_app')

@section('title', 'Dashboard Pelanggan')

@section('content')
<div class="space-y-8">
    <h1 class="text-3xl font-bold text-gray-800 border-b pb-2">Selamat Datang di PowerWash Laundry!</h1>

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kartu 1: Pesanan Aktif -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-indigo-100 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Pesanan Aktif</p>
                    <!-- Menggunakan data dummy. Ganti dengan variabel dari controller (e.g., $active_orders_count) -->
                    <p class="text-4xl font-extrabold text-gray-900 mt-1">
                        {{ $active_orders_count ?? 2 }}
                    </p>
                </div>
                <div class="text-indigo-500 bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-box-open fa-2x"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Pesanan yang sedang diproses saat ini.</p>
        </div>

        <!-- Kartu 2: Total Riwayat Pesanan -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-green-100 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 uppercase tracking-wider">Riwayat Pesanan</p>
                    <!-- Menggunakan data dummy. Ganti dengan variabel dari controller (e.g., $total_orders_count) -->
                    <p class="text-4xl font-extrabold text-gray-900 mt-1">
                        {{ $total_orders_count ?? 15 }}
                    </p>
                </div>
                <div class="text-green-500 bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Jumlah total pesanan yang telah selesai.</p>
        </div>

        <!-- Kartu 3: Poin Loyalitas -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-yellow-100 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600 uppercase tracking-wider">Poin Loyalitas</p>
                    <!-- Menggunakan data dummy. Ganti dengan variabel dari controller (e.g., $loyalty_points) -->
                    <p class="text-4xl font-extrabold text-gray-900 mt-1">
                        {{ $loyalty_points ?? 450 }}
                    </p>
                </div>
                <div class="text-yellow-500 bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-star fa-2x"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Gunakan poin untuk diskon menarik!</p>
        </div>
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

    <!-- Pesanan Terbaru (Data Placeholder) -->
    <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-list-alt text-gray-500 mr-2"></i> 3 Pesanan Terbaru
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
                    <!-- Iterasi data pesanan terbaru di sini -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#P00103</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Cuci Kering + Setrika</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Sedang Dicuci
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45.000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#P00102</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Cuci Kering (Kilat)</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Siap Diambil
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">60.000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#P00101</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Setrika Saja</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Selesai
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">30.000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
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