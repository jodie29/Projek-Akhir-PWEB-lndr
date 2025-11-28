@extends('customer.layout.customer_app')

@section('title', 'Buat Pesanan Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Pesan Layanan Laundry Anda</h1>
    
    <div class="bg-white p-8 rounded-xl shadow-2xl border border-indigo-100">
        <form action="{{ route('customer.order.store') }}" method="POST">
            @csrf
            
            <!-- Langkah 1: Pilih Layanan & Berat -->
            <section class="mb-8 p-4 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-indigo-700 mb-4 flex items-center">
                    <i class="fas fa-box-open mr-3"></i> 1. Detail Layanan
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pilihan Layanan -->
                    <div>
                        <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Jenis Layanan <span class="text-red-500">*</span></label>
                        <select id="service_id" name="service_id" required class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} (Rp {{ number_format($service->price_per_kg, 0, ',', '.') }}/kg)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilihan Metode Pembayaran -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran <span class="text-gray-500">(Opsional)</span></label>
                        <select id="payment_method" name="payment_method" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm">
                            <option value="">-- Pilih Metode Pembayaran --</option>
                            <option value="Bayar Nanti">Bayar Nanti (Bayar saat pengambilan)</option>
                            <option value="Tunai">Tunai (Tunai di tempat)</option>
                            <option value="QRIS">QRIS (Bayar via QR saat pickup)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih metode pembayaran hanya sebagai preferensi — harga akhir akan ditetapkan setelah penimbangan oleh kurir/admin.</p>
                    </div>

                    <!-- Estimasi Berat dihilangkan dari form pelanggan; admin yang akan menimbang -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Berat</label>
                        <div class="mt-1 text-sm text-gray-500">Berat tidak dimasukkan oleh pelanggan. Kurir atau admin akan menimbang saat penjemputan dan menentukan harga akhir.</div>
                    </div>
                </div>
            </section>
            
            <!-- Langkah 2: Jadwal Penjemputan & Pengantaran -->
            <section class="mb-8 p-4 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-indigo-700 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt mr-3"></i> 2. Jadwal Pickup & Delivery
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal Penjemputan (Pickup) -->
                    <div>
                        <label for="pickup_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Penjemputan <span class="text-red-500">*</span></label>
                        <input type="date" id="pickup_date" name="pickup_date" required class="mt-1 block w-full py-3 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <!-- Waktu Penjemputan -->
                    <div>
                        <label for="pickup_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu Penjemputan <span class="text-red-500">*</span></label>
                        <input type="time" id="pickup_time" name="pickup_time" required class="mt-1 block w-full py-3 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <!-- Tanggal Pengantaran (Delivery) - Optional -->
                    <div class="md:col-span-2">
                         <label for="delivery_date_pref" class="block text-sm font-medium text-gray-700 mb-2">Preferensi Tanggal Pengantaran (Jika Layanan Normal)</label>
                        <input type="date" id="delivery_date_pref" name="delivery_date_pref" class="mt-1 block w-full py-3 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Untuk layanan Kilat, tanggal otomatis disesuaikan 24 jam setelah pickup.</p>
                    </div>
                </div>
            </section>

            <!-- Langkah 3: Alamat & Catatan -->
            <section class="mb-8 p-4">
                <h2 class="text-2xl font-semibold text-indigo-700 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt mr-3"></i> 3. Lokasi & Catatan
                </h2>
                
                <!-- Alamat (Menggunakan Alamat Default Pelanggan sebagai Placeholder) -->
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Penjemputan/Pengantaran <span class="text-red-500">*</span></label>
                    <textarea id="address" name="address" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-3" placeholder="Alamat lengkap Anda (Contoh: Jl. Sudirman No. 12, dekat Taman Kota)">{{ $default_address ?? 'Alamat default pelanggan' }}</textarea>
                    <p class="mt-1 text-xs text-indigo-600">Alamat ini akan digunakan untuk pickup dan delivery.</p>
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-3" placeholder="Contoh: Tolong pisahkan baju putih, atau hubungi 15 menit sebelum tiba."></textarea>
                </div>
            </section>

            <!-- Ringkasan dan Tombol Kirim -->
            <div class="pt-6 border-t border-gray-200">
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
                    <p class="text-sm font-medium text-yellow-800 flex items-start">
                        <i class="fas fa-info-circle mr-2 mt-1"></i>
                        Estimasi biaya akan dihitung dan dikonfirmasi oleh petugas kami setelah penimbangan akurat dilakukan saat penjemputan.
                    </p>
                </div>
                
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                    <i class="fas fa-paper-plane mr-2"></i> Konfirmasi & Buat Pesanan
                </button>
            </div>
            
        </form>
    </div>
</div>
@endsection