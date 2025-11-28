<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PowerWash Laundry</title>

    <!-- 1. LINK GOOGLE FONTS (WAJIB ADA AGAR FONT INTER MUNCUL) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- 2. Memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- 3. Konfigurasi Tailwind (Agar otomatis menggunakan Inter) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Style Manual (Opsional - Editor mungkin masih menandai ini merah, tapi di browser akan benar) -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans"> 
    <!-- Class 'font-sans' memastikan Tailwind menggunakan font yang dikonfigurasi di atas -->

    <!-- Header / Navbar -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                    <span class="text-xl font-bold text-white">PowerWash ADMIN</span>
                </div>
                <div class="flex items-center">
                    <!-- Menampilkan nama user yang sedang login -->
                    <span class="text-white text-sm mr-4 hidden md:block">Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}</span>
                    <!-- Tombol Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="inline" onsubmit="localStorage.removeItem('pw_active_session_v1'); sessionStorage.removeItem('pw_tab_token_v1');">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-1 px-3 rounded-lg transition duration-150 shadow-md">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Utama Dashboard -->

    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 shadow-2xl rounded-2xl p-10">
                <x-header-illustration title="Dashboard Administrator" :subtitle="'Selamat, Anda berhasil masuk sebagai Administrator! Halaman ini adalah pusat kontrol utama Anda.'" :image="'https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png'"/>

                <!-- Area Navigasi Cepat -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Kartu: Kasir Cepat (Walk-in) -->
                    <a href="{{ route('admin.cashier.create') }}" class="group block p-7 bg-red-100/80 border-0 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-105 hover:bg-red-200/80">
                        <p class="text-xl font-semibold text-red-700 group-hover:text-red-900">Kasir Cepat (Walk-in)</p>
                        <p class="text-sm text-red-500 mt-2 group-hover:text-red-700">Buat pesanan langsung untuk pelanggan walk-in (bayar di tempat).</p>
                    </a>
                    <!-- Kartu 1: Manajemen Layanan -->
                    <a href="{{ route('admin.services.index') }}" class="group block p-7 bg-green-100/80 border-0 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-105 hover:bg-green-200/80">
                        <p class="text-xl font-semibold text-green-700 group-hover:text-green-900">Manajemen Layanan</p>
                        <p class="text-sm text-green-500 mt-2 group-hover:text-green-700">Tambah, edit, atau hapus layanan laundry (misal: cuci kiloan, cuci setrika).</p>
                    </a>
                    <!-- Kartu 2: Daftar Pesanan -->
                    <a href="{{ route('admin.orders.index') }}" class="group block p-7 bg-yellow-100/80 border-0 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-105 hover:bg-yellow-200/80">
                        <p class="text-xl font-semibold text-yellow-700 group-hover:text-yellow-900">Daftar Semua Pesanan</p>
                        <p class="text-sm text-yellow-500 mt-2 group-hover:text-yellow-700">Monitoring status dan detail seluruh pesanan dari Customer.</p>
                    </a>
                    <!-- Kartu 3: Kelola Akun -->
                    <a href="{{ route('admin.users.index') }}" class="group block p-7 bg-purple-100/80 border-0 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-105 hover:bg-purple-200/80">
                        <p class="text-xl font-semibold text-purple-700 group-hover:text-purple-900">Kelola Pengguna</p>
                        <p class="text-sm text-purple-500 mt-2 group-hover:text-purple-700">Atur akun Admin, Kurir, dan Pelanggan yang terdaftar.</p>
                    </a>
                </div>

                <!-- Recent orders table -->
                <div class="mt-12">
                    <h3 class="text-2xl font-semibold mb-4">Pesanan Terbaru (5 Terakhir)</h3>
                    <div class="bg-white/80 rounded-2xl shadow-md overflow-x-auto">
                        <table class="w-full table-auto text-left">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3">No Order</th>
                                    <th class="px-4 py-3">Layanan</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $o)
                                <tr class="odd:bg-white even:bg-blue-50/40 hover:bg-blue-100/60 transition">
                                    <td class="px-4 py-3">{{ $o->order_number }}</td>
                                    <td class="px-4 py-3">{{ $o->service->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $o->status }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($o->total_price ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $o->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-4 py-3">Belum ada pesanan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Statistik Utama (Contoh Data) -->
                <div class="mt-12 border-t-2 pt-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Statistik Utama</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <x-stat-card label="Total Pesanan Bulan Ini" :value="$total_orders_month ?? 0" bgClass="bg-blue-50/80" textClass="text-blue-700"/>
                        <x-stat-card label="Menunggu Penjemputan" :value="$waiting_pickup ?? 0" bgClass="bg-red-50/80" textClass="text-red-700"/>
                        <x-stat-card label="Total Pendapatan Bulan Ini" :value="'Rp ' . number_format($total_revenue_month ?? 0,0,',','.')" bgClass="bg-yellow-50/80" textClass="text-yellow-700"/>
                        <x-stat-card label="Kurir Aktif" :value="$active_couriers ?? 0" bgClass="bg-green-50/80" textClass="text-green-700"/>
                    </div>
                </div>
                <!-- Chart: Revenue last 6 months -->
                <div class="mt-8">
                    <h3 class="text-xl font-semibold mb-3">Pendapatan 6 Bulan Terakhir</h3>
                    @if(!empty($hasMonthlyRevenue) && $hasMonthlyRevenue)
                        <div class="bg-white p-6 rounded-2xl shadow-md">
                            <canvas id="revenueChart" class="w-full h-48"></canvas>
                        </div>
                    @else
                        <div class="bg-white p-6 rounded-2xl shadow-md text-gray-500">
                            Belum ada data pendapatan untuk 6 bulan terakhir.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function(){
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;
            const labels = JSON.parse('@json($months ?? [])'.replace(/&quot;/g, '"'));
            const data = JSON.parse('@json($monthlyRevenue ?? [])'.replace(/&quot;/g, '"'));
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        backgroundColor: '#f59e0b',
                        borderColor: '#f59e0b',
                        data: data
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) { return 'Rp ' + (value).toLocaleString(); }
                            }
                        }
                    }
                }
            });
        })();
    </script>

</body>
</html>