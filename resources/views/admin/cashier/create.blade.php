<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Cepat - PowerWash Laundry</title>
    
    <!-- 1. Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

    <!-- Header / Navbar -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <span class="text-xl font-bold text-white">PowerWash ADMIN</span>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-white text-sm mr-4 hover:text-blue-200 transition duration-150">Kembali ke Dashboard</a>
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

    <!-- Konten Utama Form Kasir -->
    <main class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-xl p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Kasir Cepat (Walk-in)
                </h1>

                <p class="text-gray-600 mb-8">
                    Gunakan formulir ini untuk mencatat pesanan dari pelanggan yang datang langsung (walk-in).
                </p>
                
                <!-- Notifikasi Error/Sukses -->
                @if (session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        <strong>Perhatian!</strong> Ada beberapa masalah dengan input Anda.
                        <ul class="mt-1.5 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Pembuatan Pesanan Walk-in -->
                <form action="{{ route('admin.cashier.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        
                        <!-- 1. Bagian Data Pelanggan -->
                        <div class="p-6 border border-gray-200 rounded-lg bg-indigo-50">
                            <h3 class="text-xl font-semibold text-indigo-700 mb-4 border-b pb-2">1. Data Pelanggan</h3>
                            <p class="text-sm text-indigo-500 mb-4">Pelanggan walk-in tidak memerlukan login, cukup catat nama dan kontak.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama Pelanggan -->
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                                    <input type="text" name="customer_name" id="customer_name" 
                                           @class([
                                               'w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500',
                                               'border-red-500' => $errors->has('customer_name'),
                                               'border-gray-300' => !$errors->has('customer_name'),
                                           ])
                                           value="{{ old('customer_name') }}" required>
                                    @error('customer_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Nomor Telepon -->
                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon <span class="text-red-500">*</span></label>
                                    <input type="text" name="customer_phone" id="customer_phone" 
                                           @class([
                                               'w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500',
                                               'border-red-500' => $errors->has('customer_phone'),
                                               'border-gray-300' => !$errors->has('customer_phone'),
                                           ])
                                           value="{{ old('customer_phone') }}" required>
                                    @error('customer_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="mt-4">
                                <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat (Opsional)</label>
                                <textarea name="customer_address" id="customer_address" rows="2" 
                                          @class([
                                              'w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500',
                                              'border-red-500' => $errors->has('customer_address'),
                                              'border-gray-300' => !$errors->has('customer_address'),
                                          ])>{{ old('customer_address') }}</textarea>
                                @error('customer_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 2. Bagian Detail Pesanan -->
                        <div class="p-6 border border-gray-200 rounded-lg bg-blue-50">
                            <h3 class="text-xl font-semibold text-blue-700 mb-4 border-b pb-2">2. Detail Pesanan</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <!-- Pilih Layanan -->
                                <div>
                                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Layanan <span class="text-red-500">*</span></label>
                                    <select name="service_id" id="service_id" 
                                            @class([
                                                'w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500',
                                                'border-red-500' => $errors->has('service_id'),
                                                'border-gray-300' => !$errors->has('service_id'),
                                            ]) required>
                                        <option value="">-- Pilih Layanan --</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" 
                                                    data-unit="{{ $service->unit }}" 
                                                    {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }} (Rp {{ number_format($service->price, 0, ',', '.') }}/{{ $service->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Berat/Kuantitas -->
                                <div>
                                    <label for="weight_or_qty" class="block text-sm font-medium text-gray-700 mb-1">
                                        <span id="qty_label">Berat (kg)</span> / Kuantitas <span class="text-red-500">*</span>
                                    </label>
                                     <input type="number" step="0.1" min="0.1" name="actual_weight" id="weight_or_qty" 
                                           @class([
                                               'w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500',
                                               'border-red-500' => $errors->has('actual_weight'),
                                                   'border-gray-300' => !$errors->has('actual_weight'),
                                           ])
                                           value="{{ old('actual_weight') }}" required>
                                    @error('actual_weight')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="mt-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                                <textarea name="notes" id="notes" rows="2" 
                                          @class([
                                              'w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500',
                                              'border-red-500' => $errors->has('notes'),
                                              'border-gray-300' => !$errors->has('notes'),
                                          ])>{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 3. Bagian Pembayaran -->
                        <div class="p-6 border border-gray-200 rounded-lg bg-green-50">
                            <h3 class="text-xl font-semibold text-green-700 mb-4 border-b pb-2">3. Pembayaran</h3>
                            <p class="text-sm text-green-500 mb-4">Untuk pesanan walk-in, status awal adalah 'Di Laundry' dan pembayaran dianggap 'Lunas' atau 'Belum Lunas'.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Status Pembayaran -->
                                <div>
                                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                                    <select name="payment_status" id="payment_status" 
                                            @class([
                                                'w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500',
                                                'border-red-500' => $errors->has('payment_status'),
                                                'border-gray-300' => !$errors->has('payment_status'),
                                            ]) required>
                                        <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                                        <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                                    </select>
                                    @error('payment_status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Method (matches controller expected field name) -->
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                                    <select name="payment_method" id="payment_method"
                                            @class([
                                                'w-full px-3 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500',
                                                'border-red-500' => $errors->has('payment_method'),
                                                'border-gray-300' => !$errors->has('payment_method'),
                                            ]) required>
                                        <option value="Tunai" {{ old('payment_method') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                        <option value="QRIS" {{ old('payment_method') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                        <option value="Bayar Nanti" {{ old('payment_method') == 'Bayar Nanti' ? 'selected' : '' }}>Bayar Nanti</option>
                                    </select>
                                    @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Due date removed as per requirement -->
                            </div>
                        </div>

                    </div>

                    <!-- Tombol Submit -->
                    <div class="mt-8 pt-6 border-t flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-150 transform hover:scale-[1.02]">
                            Buat Pesanan & Lanjutkan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
    
    <!-- Script Auto Label Berat/Kuantitas -->
    <script>
        document.getElementById('service_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var unit = selectedOption.getAttribute('data-unit');
            var labelSpan = document.getElementById('qty_label');
            var inputQty = document.getElementById('weight_or_qty');

            if (unit && unit.toLowerCase() === 'kg') {
                labelSpan.textContent = 'Berat (kg)';
                inputQty.setAttribute('step', '0.1');
            } else if (unit && unit.toLowerCase() === 'pcs') {
                labelSpan.textContent = 'Kuantitas (Pcs)';
                inputQty.setAttribute('step', '1');
            } else {
                labelSpan.textContent = 'Jumlah (kg/Pcs)';
                inputQty.setAttribute('step', '0.1'); 
            }
        });
        document.getElementById('service_id').dispatchEvent(new Event('change'));
    </script>

</body>
</html>