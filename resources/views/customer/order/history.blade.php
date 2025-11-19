@extends('customer.layout.customer_app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Riwayat Semua Pesanan Anda</h1>
    
    <!-- Bagian Filter dan Pencarian -->
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6 border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <!-- Filter Status (Tabs/Buttons) -->
            <div class="flex space-x-2 overflow-x-auto pb-2">
                <button class="px-4 py-2 text-sm font-medium rounded-full bg-indigo-600 text-white shadow-md transition duration-150">Semua (10)</button>
                <button class="px-4 py-2 text-sm font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition duration-150">Aktif (2)</button>
                <button class="px-4 py-2 text-sm font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition duration-150">Selesai (8)</button>
                <button class="px-4 py-2 text-sm font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition duration-150">Dibatalkan (0)</button>
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
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                
                <!-- Placeholder Pesanan Aktif 1 -->
                <tr class="hover:bg-indigo-50 transition duration-100">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-700">#P00105</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">17 Nov 2025, 10:00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Cuci Kering + Setrika</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Dalam Proses (Dicuci)
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Belum Final</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 font-semibold transition duration-150">Lihat Detail</a>
                    </td>
                </tr>

                <!-- Placeholder Pesanan Aktif 2 -->
                <tr class="hover:bg-indigo-50 transition duration-100">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-700">#P00104</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">16 Nov 2025, 14:30</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Cuci Kering Kilat</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Menunggu Penjemputan
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Belum Final</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 font-semibold transition duration-150">Lihat Detail</a>
                    </td>
                </tr>

                <!-- Placeholder Pesanan Selesai 1 -->
                <tr class="hover:bg-indigo-50 transition duration-100">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-700">#P00103</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">10 Nov 2025, 08:00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Setrika Saja</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Selesai (Diantar)
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rp 45.000</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 font-semibold transition duration-150">Lihat Detail</a>
                    </td>
                </tr>
                
                <!-- Placeholder Pesanan Selesai 2 -->
                <tr class="hover:bg-indigo-50 transition duration-100">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-700">#P00102</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">05 Nov 2025, 12:00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Cuci Kering</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Selesai (Diambil)
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rp 52.500</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 font-semibold transition duration-150">Lihat Detail</a>
                    </td>
                </tr>
                
                <!-- Tambahkan lebih banyak baris pesanan di sini jika perlu -->
                
            </tbody>
        </table>

        <!-- Paginasi Sederhana -->
        <div class="px-6 py-4 border-t flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Menampilkan 1 hingga 10 dari 25 Pesanan
            </div>
            <div class="flex space-x-2">
                <button class="px-3 py-1 border rounded-lg text-sm text-gray-600 hover:bg-gray-100">Sebelumnya</button>
                <button class="px-3 py-1 border rounded-lg text-sm bg-indigo-600 text-white">1</button>
                <button class="px-3 py-1 border rounded-lg text-sm text-gray-600 hover:bg-gray-100">2</button>
                <button class="px-3 py-1 border rounded-lg text-sm text-gray-600 hover:bg-gray-100">3</button>
                <button class="px-3 py-1 border rounded-lg text-sm text-gray-600 hover:bg-gray-100">Berikutnya</button>
            </div>
        </div>
    </div>
</div>
@endsection