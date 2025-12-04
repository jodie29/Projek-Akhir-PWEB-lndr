<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Cepat - PowerWash Laundry</title>
    
    <!-- 1. Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- 2. Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 3. Config Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary': '#2563EB', // Blue-600
                        'primary-dark': '#1D4ED8', // Blue-700
                    }
                }
            }
        }
    </script>
    
    <!-- 4. Lucide Icons (untuk ikon Logout) -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

    <!-- Header / Navbar -->
    <nav class="bg-primary shadow-2xl sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <span class="text-xl font-extrabold text-white tracking-wider">POWERWASH ADMIN</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-white text-sm font-medium hover:text-blue-200 transition duration-150 p-2 rounded-lg hover:bg-blue-700/50 hidden sm:block">
                        Kembali ke Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-1.5 px-3 rounded-xl transition duration-150 shadow-lg flex items-center group">
                            <i data-lucide="log-out" class="h-4 w-4 mr-1 transition-transform group-hover:translate-x-0.5"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Utama Form Kasir -->
    <main class="py-8 md:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-2xl p-6 md:p-10 border border-gray-100">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-2 flex items-center">
                    <i data-lucide="scan-barcode" class="h-8 w-8 text-primary mr-3"></i>
                    Kasir Cepat (Walk-in)
                </h1>
                <p class="text-gray-500 mb-8 border-b pb-4 text-sm md:text-base">
                    Input cepat untuk mencatat pesanan pelanggan yang datang langsung tanpa penjemputan.
                </p>
                
                <!-- Notifikasi Error/Sukses -->
                @if (session('success'))
                    <div class="mb-6 p-4 text-sm font-medium text-green-800 bg-green-100 border-l-4 border-green-500 rounded-lg shadow-md" role="alert">
                        <p class="font-semibold">Pesanan Berhasil Dicatat!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 border-l-4 border-red-500 rounded-lg shadow-md">
                        <p class="font-semibold">Perhatian!</p>
                        <ul class="mt-1.5 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Pembuatan Pesanan Walk-in -->
                <form action="{{ route('admin.cashier.store') }}" method="POST" class="space-y-10">
                    @csrf
                    
                    <!-- 1. Bagian Data Pelanggan -->
                    <div class="p-6 border border-indigo-200 rounded-xl bg-indigo-50 shadow-inner">
                        <h3 class="text-xl font-bold text-indigo-700 mb-4 border-b border-indigo-300 pb-2 flex items-center">
                             <i data-lucide="user" class="h-5 w-5 mr-2"></i> 1. Data Pelanggan
                        </h3>
                        <p class="text-sm text-indigo-500 mb-6">Cukup catat nama dan kontak yang bisa dihubungi.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Pelanggan -->
                            <div>
                                <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" 
                                    placeholder="Masukkan nama lengkap"
                                    @class([
                                        'w-full px-4 py-2 border rounded-xl shadow-sm transition duration-150',
                                        'focus:ring-primary focus:border-primary border-gray-300' => !$errors->has('customer_name'),
                                        'border-red-500 ring-red-500' => $errors->has('customer_name'),
                                    ])
                                    value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Nomor Telepon -->
                            <div>
                                <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_phone" id="customer_phone" 
                                    placeholder="Contoh: 0812xxxxxx"
                                    @class([
                                        'w-full px-4 py-2 border rounded-xl shadow-sm transition duration-150',
                                        'focus:ring-primary focus:border-primary border-gray-300' => !$errors->has('customer_phone'),
                                        'border-red-500 ring-red-500' => $errors->has('customer_phone'),
                                    ])
                                    value="{{ old('customer_phone') }}" required>
                                @error('customer_phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="mt-6">
                            <label for="customer_address" class="block text-sm font-semibold text-gray-700 mb-1">Alamat (Opsional)</label>
                            <textarea name="customer_address" id="customer_address" rows="2" 
                                placeholder="Alamat lengkap pelanggan"
                                @class([
                                    'w-full px-4 py-2 border rounded-xl shadow-sm transition duration-150',
                                    'focus:ring-primary focus:border-primary border-gray-300' => !$errors->has('customer_address'),
                                    'border-red-500 ring-red-500' => $errors->has('customer_address'),
                                ])>{{ old('customer_address') }}</textarea>
                            @error('customer_address')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 2. Bagian Detail Pesanan -->
                    <div class="p-6 border border-blue-200 rounded-xl bg-blue-50 shadow-inner">
                        <h3 class="text-xl font-bold text-blue-700 mb-4 border-b border-blue-300 pb-2 flex items-center">
                            <i data-lucide="package-search" class="h-5 w-5 mr-2"></i> 2. Detail Layanan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <!-- Pilih Layanan -->
                            <div>
                                <label for="service_id" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Layanan <span class="text-red-500">*</span></label>
                                <select name="service_id" id="service_id" 
                                        @class([
                                            'w-full px-4 py-2 border rounded-xl shadow-sm bg-white transition duration-150',
                                            'focus:ring-primary focus:border-primary border-gray-300' => !$errors->has('service_id'),
                                            'border-red-500 ring-red-500' => $errors->has('service_id'),
                                        ]) required>
                                    <option value="">-- Pilih Layanan --</option>
                                    {{-- Menggunakan data dummy karena $services belum tersedia --}}
                                    @php
                                        // Data dummy untuk demonstrasi tampilan
                                        $services = [
                                            (object)['id' => 1, 'name' => 'Cuci Kering Lipat', 'price' => 7000, 'unit' => 'kg'],
                                            (object)['id' => 2, 'name' => 'Cuci Sepatu Premium', 'price' => 35000, 'unit' => 'pcs'],
                                            (object)['id' => 3, 'name' => 'Setrika Saja', 'price' => 5000, 'unit' => 'kg'],
                                        ];
                                    @endphp
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" 
                                                data-unit="{{ $service->unit }}" 
                                                data-price="{{ $service->price }}"
                                                {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} (Rp {{ number_format($service->price, 0, ',', '.') }}/{{ $service->unit }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Berat/Kuantitas -->
                            <div>
                                <label for="weight_or_qty" class="block text-sm font-semibold text-gray-700 mb-1">
                                    <span id="qty_label">Berat (kg)</span> / Kuantitas <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.1" min="0.1" name="actual_weight" id="weight_or_qty" 
                                    placeholder="Contoh: 5.5"
                                    @class([
                                        'w-full px-4 py-2 border rounded-xl shadow-sm transition duration-150',
                                        'focus:ring-primary focus:border-primary border-gray-300' => !$errors->has('actual_weight'),
                                        'border-red-500 ring-red-500' => $errors->has('actual_weight'),
                                    ])
                                    value="{{ old('actual_weight') }}" required>
                                @error('actual_weight')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Estimasi Total Biaya (Hanya Tampilan) -->
                        <div class="p-4 bg-blue-100 rounded-lg mt-6 shadow-md border border-blue-300">
                            <p class="text-sm font-medium text-blue-800">Estimasi Total Biaya:</p>
                            <p id="total_cost" class="text-2xl font-extrabold text-blue-900 mt-1">Rp 0</p>
                            <input type="hidden" name="estimated_price" id="estimated_price_input" value="0">
                        </div>

                        <!-- Catatan -->
                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" id="notes" rows="2" 
                                placeholder="Catatan khusus, misalnya: 'Ada noda kopi di kemeja putih'"
                                @class([
                                    'w-full px-4 py-2 border rounded-xl shadow-sm transition duration-150',
                                    'focus:ring-primary focus:border-primary border-gray-300' => !$errors->has('notes'),
                                    'border-red-500 ring-red-500' => $errors->has('notes'),
                                ])>{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 3. Bagian Pembayaran -->
                    <div class="p-6 border border-green-200 rounded-xl bg-green-50 shadow-inner">
                        <h3 class="text-xl font-bold text-green-700 mb-4 border-b border-green-300 pb-2 flex items-center">
                            <i data-lucide="wallet" class="h-5 w-5 mr-2"></i> 3. Pembayaran
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Status Pembayaran -->
                            <div>
                                <label for="payment_status" class="block text-sm font-semibold text-gray-700 mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                                <select name="payment_status" id="payment_status" 
                                        @class([
                                            'w-full px-4 py-2 border rounded-xl shadow-sm bg-white transition duration-150',
                                            'focus:ring-primary focus:border-primary border-gray-300' => !$errors->has('payment_status'),
                                            'border-red-500 ring-red-500' => $errors->has('payment_status'),
                                        ]) required>
                                    <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Lunas (Bayar Saat Ambil)</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Lunas (Bayar Tunai/Transfer)</option>
                                </select>
                                @error('payment_status')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                                <select name="payment_method" id="payment_method"
                                        @class([
                                            'w-full px-4 py-2 border rounded-xl shadow-sm bg-white transition duration-150',
                                            'focus:ring-primary focus:border-primary border-gray-300' => !$errors->has('payment_method'),
                                            'border-red-500 ring-red-500' => $errors->has('payment_method'),
                                        ]) required>
                                    <option value="Tunai" {{ old('payment_method') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="QRIS" {{ old('payment_method') == 'QRIS' ? 'selected' : '' }}>QRIS/Transfer</option>
                                    <option value="Bayar Nanti" {{ old('payment_method') == 'Bayar Nanti' ? 'selected' : '' }}>Bayar Nanti (Sesuai Status Di Atas)</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-8 rounded-xl shadow-2xl transition duration-300 transform hover:scale-[1.02] border-b-4 border-blue-800 hover:border-blue-700 flex items-center">
                            <i data-lucide="send" class="h-5 w-5 mr-2"></i>
                            Buat Pesanan & Cetak Resi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
    
    <!-- Script Auto Label Berat/Kuantitas dan Kalkulasi Biaya -->
    <script>
        // Fungsi format Rupiah
        function formatRupiah(angka) {
            var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
            ribuan = ribuan.join('.').split('').reverse().join('');
            return 'Rp ' + (ribuan ? ribuan : 0);
        }

        function calculateTotal() {
            const serviceSelect = document.getElementById('service_id');
            const qtyInput = document.getElementById('weight_or_qty');
            const totalCostDisplay = document.getElementById('total_cost');
            const estimatedPriceInput = document.getElementById('estimated_price_input');

            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            
            if (!selectedOption || !qtyInput.value) {
                totalCostDisplay.textContent = formatRupiah(0);
                estimatedPriceInput.value = 0;
                return;
            }

            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const qty = parseFloat(qtyInput.value) || 0;
            
            const total = price * qty;
            
            totalCostDisplay.textContent = formatRupiah(Math.round(total));
            estimatedPriceInput.value = Math.round(total);
        }

        function updateQtyLabel() {
            const serviceSelect = document.getElementById('service_id');
            const qtyLabel = document.getElementById('qty_label');
            const qtyInput = document.getElementById('weight_or_qty');
            
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const unit = selectedOption.getAttribute('data-unit');

            if (unit && unit.toLowerCase() === 'kg') {
                qtyLabel.textContent = 'Berat (kg)';
                qtyInput.setAttribute('step', '0.1');
                qtyInput.setAttribute('placeholder', 'Contoh: 5.5');
            } else if (unit && unit.toLowerCase() === 'pcs') {
                qtyLabel.textContent = 'Kuantitas (Pcs)';
                qtyInput.setAttribute('step', '1');
                qtyInput.setAttribute('placeholder', 'Contoh: 3');
            } else {
                // Default jika tidak ada unit atau unit tidak terdefinisi
                qtyLabel.textContent = 'Jumlah';
                qtyInput.setAttribute('step', 'any');
                qtyInput.setAttribute('placeholder', 'Masukkan jumlah');
            }
        }

        // Event Listeners
        document.getElementById('service_id').addEventListener('change', function() {
            updateQtyLabel();
            calculateTotal();
        });
        document.getElementById('weight_or_qty').addEventListener('input', calculateTotal);

        // Inisialisasi pada load
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi ikon Lucide
            lucide.createIcons();
            
            // Panggil fungsi inisialisasi agar label dan biaya terhitung jika ada 'old' value
            updateQtyLabel();
            calculateTotal();
        });
    </script>
</body>
</html>