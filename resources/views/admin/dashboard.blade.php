<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PowerWash Laundry</title>

    <!-- Memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Konfigurasi Tailwind (Menggunakan Inter font) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'laundry-blue': '#1e40af', // Darker blue for primary theme
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans"> 

    <!-- Header / Navbar -->
    <nav class="bg-blue-600 shadow-lg sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand Title -->
                <span class="text-xl font-bold text-white tracking-wider">PowerWash ADMIN</span>
                
                <div class="flex items-center">
                    <!-- Menampilkan nama user yang sedang login -->
                    @auth
                        <span id="user-welcome" class="text-white text-sm mr-4 hidden md:block">{{ Auth::user()->name }}</span>
                    @else
                        <span id="user-welcome" class="text-white text-sm mr-4 hidden md:block">Administrator</span>
                    @endauth
                    
                    <!-- Tombol Logout (form POST ke route logout) -->
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline" onsubmit="localStorage.removeItem('pw_active_session_v1'); sessionStorage.removeItem('pw_tab_token_v1');">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-1 px-3 rounded-lg transition duration-150 shadow-md">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-1 px-3 rounded-lg transition duration-150 shadow-md">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Utama Dashboard -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 shadow-2xl rounded-2xl p-6 sm:p-10">
                
                <!-- Component: Header Illustration (server-side rendering) -->
                <div id="header-illustration" class="flex flex-col md:flex-row items-center justify-between mb-10 pb-6 border-b border-blue-200">
                    <div class="text-left w-full md:w-3/4">
                        <h1 class="text-4xl font-bold text-blue-800 mb-2">Dashboard Administrator</h1>
                        <p class="text-lg text-gray-600">Selamat, Anda berhasil masuk sebagai Administrator! Halaman ini adalah pusat kontrol utama Anda.</p>
                    </div>
                    <div class="hidden md:block w-1/4">
                        <img src="https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png" alt="Ilustrasi Laundry" class="h-24 w-auto mx-auto object-contain">
                    </div>
                </div>
                <!-- End Component: Header Illustration -->

                <!-- Area Navigasi Cepat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                    
                    <!-- Kartu: Kasir Cepat (Walk-in) -->
                    <a href="{{ route('admin.cashier.create') }}" class="group block p-5 lg:p-7 bg-red-100/80 border-0 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-105 hover:bg-red-200/80">
                        <p class="text-xl font-semibold text-red-700 group-hover:text-red-900">Kasir Cepat (Walk-in)</p>
                        <p class="text-xs sm:text-sm text-red-500 mt-2 group-hover:text-red-700">Buat pesanan langsung untuk pelanggan walk-in (bayar di tempat).</p>
                    </a>
                    
                    <!-- Kartu 1: Manajemen Layanan -->
                    <a href="{{ route('admin.services.index') }}" class="group block p-5 lg:p-7 bg-green-100/80 border-0 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-105 hover:bg-green-200/80">
                        <p class="text-xl font-semibold text-green-700 group-hover:text-green-900">Manajemen Layanan</p>
                        <p class="text-xs sm:text-sm text-green-500 mt-2 group-hover:text-green-700">Tambah, edit, atau hapus layanan laundry.</p>
                    </a>
                    
                    <!-- Kartu 2: Daftar Pesanan -->
                    <a href="{{ route('admin.orders.index') }}" class="group block p-5 lg:p-7 bg-yellow-100/80 border-0 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-105 hover:bg-yellow-200/80">
                        <p class="text-xl font-semibold text-yellow-700 group-hover:text-yellow-900">Daftar Semua Pesanan</p>
                        <p class="text-xs sm:text-sm text-yellow-500 mt-2 group-hover:text-yellow-700">Monitoring status dan detail seluruh pesanan dari Customer.</p>
                    </a>
                    
                    <!-- Kartu 3: Kelola Akun -->
                    <a href="{{ route('admin.users.index') }}" class="group block p-5 lg:p-7 bg-purple-100/80 border-0 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-105 hover:bg-purple-200/80">
                        <p class="text-xl font-semibold text-purple-700 group-hover:text-purple-900">Kelola Pengguna</p>
                        <p class="text-xs sm:text-sm text-purple-500 mt-2 group-hover:text-purple-700">Atur akun Admin, Kurir, dan Pelanggan yang terdaftar.</p>
                    </a>
                </div>

                <!-- Recent orders table -->
                <div class="mt-12">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Pesanan Terbaru (5 Terakhir)</h3>
                    <div class="bg-white/80 rounded-2xl shadow-md overflow-x-auto">
                        <table class="min-w-full table-auto text-left text-sm">
                            <thead class="bg-gray-100 text-gray-600 uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">No Order</th>
                                    <th class="px-4 py-3">Layanan</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table-body">
                                @forelse($orders as $order)
                                    <tr class="hover:bg-blue-50/40 transition cursor-pointer">
                                        <td class="px-4 py-3 font-medium text-blue-600">{{ $order->order_number }}</td>
                                        <td class="px-4 py-3">{{ $order->service->name ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusLabel = ucfirst(str_replace('_', ' ', $order->status ?? '-'));
                                                $badgeClass = 'text-gray-800 bg-gray-200';
                                                if (stripos($statusLabel, 'selesai') !== false) $badgeClass = 'text-green-800 bg-green-200';
                                                elseif (stripos($statusLabel, 'proses') !== false) $badgeClass = 'text-blue-800 bg-blue-200';
                                                elseif (stripos($statusLabel, 'menunggu') !== false) $badgeClass = 'text-red-800 bg-red-200';
                                            @endphp
                                            <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $badgeClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-gray-500 text-sm">{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">Belum ada pesanan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Statistik Utama -->
                <div class="mt-12 border-t-2 border-blue-200 pt-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Statistik Utama</h2>
                    <div id="stat-cards-container" class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="p-5 rounded-xl shadow-lg bg-blue-50/80 hover:shadow-xl transition duration-300">
                            <p class="text-4xl font-extrabold text-blue-700 mb-1">{{ $total_orders_month ?? 0 }}</p>
                            <p class="text-sm font-medium text-gray-600">Total Pesanan Bulan Ini</p>
                        </div>
                        <div class="p-5 rounded-xl shadow-lg bg-red-50/80 hover:shadow-xl transition duration-300">
                            <p class="text-4xl font-extrabold text-red-700 mb-1">{{ $waiting_pickup ?? 0 }}</p>
                            <p class="text-sm font-medium text-gray-600">Menunggu Penjemputan</p>
                        </div>
                        <div class="p-5 rounded-xl shadow-lg bg-yellow-50/80 hover:shadow-xl transition duration-300">
                            <p class="text-4xl font-extrabold text-yellow-700 mb-1">Rp {{ number_format($total_revenue_month ?? 0, 0, ',', '.') }}</p>
                            <p class="text-sm font-medium text-gray-600">Total Pendapatan Bulan Ini</p>
                        </div>
                        <div class="p-5 rounded-xl shadow-lg bg-green-50/80 hover:shadow-xl transition duration-300">
                            <p class="text-4xl font-extrabold text-green-700 mb-1">{{ $active_couriers ?? 0 }}</p>
                            <p class="text-sm font-medium text-gray-600">Kurir Aktif</p>
                        </div>
                    </div>
                </div>
                
                <!-- Chart: Revenue last 6 months -->
                <div class="mt-12 border-t-2 border-blue-200 pt-8">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-3">Pendapatan 6 Bulan Terakhir</h3>
                    <div class="bg-white p-6 rounded-2xl shadow-xl">
                        <canvas id="revenueChart" class="w-full h-64"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;
            const labels = @json($months ?? []);
            const data = @json($monthlyRevenue ?? []);
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        backgroundColor: '#fbbf24',
                        borderColor: '#fbbf24',
                        data: data,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                    return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>