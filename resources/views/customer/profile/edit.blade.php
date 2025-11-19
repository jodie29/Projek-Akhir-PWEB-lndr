@extends('customer.layout.customer_app')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Pengaturan Akun & Profil</h1>
    
    <div class="bg-white p-8 rounded-xl shadow-2xl border border-indigo-100">
        <!-- Notifikasi (Contoh: Jika Berhasil Disimpan) -->
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline"> {{ session('status') }}</span>
            </div>
        @endif

        <form action="{{ route('customer.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Informasi Personal -->
            <section class="mb-8 p-4 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-indigo-700 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-3"></i> Detail Personal
                </h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ $user->name ?? 'Nama Pelanggan' }}" required class="mt-1 block w-full py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Email (Biasanya tidak diubah di sini) -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-500 mb-2">Email (Tidak Dapat Diubah)</label>
                        <input type="email" id="email" value="{{ $user->email ?? 'customer@example.com' }}" disabled class="mt-1 block w-full py-3 bg-gray-50 border border-gray-300 rounded-lg shadow-sm sm:text-sm cursor-not-allowed">
                    </div>

                    <!-- Nomor Telepon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="tel" id="phone" name="phone" value="{{ $user->phone ?? '081234567890' }}" required class="mt-1 block w-full py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Contoh: 08xxxxxxxxxx">
                    </div>
                </div>
            </section>
            
            <!-- Alamat Default -->
            <section class="mb-8 p-4">
                <h2 class="text-2xl font-semibold text-indigo-700 mb-4 flex items-center">
                    <i class="fas fa-map-marked-alt mr-3"></i> Alamat Default
                </h2>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Utama (Untuk Penjemputan/Pengantaran)</label>
                    <textarea id="address" name="address" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-3" placeholder="Alamat lengkap Anda">{{ $user->address ?? 'Jl. Default No. 10, Komplek Perumahan Indah' }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Alamat ini akan otomatis terisi saat membuat pesanan baru.</p>
                </div>
            </section>

            <!-- Bagian Ganti Password (Opsional) -->
            <section class="mb-8 p-4 border-t border-gray-200 pt-6">
                <h2 class="text-2xl font-semibold text-red-600 mb-4 flex items-center">
                    <i class="fas fa-lock mr-3"></i> Ganti Kata Sandi
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" class="mt-1 block w-full py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="Biarkan kosong jika tidak ingin mengubah">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="mt-1 block w-full py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>
            </section>

            <!-- Tombol Simpan -->
            <div class="pt-6 border-t border-gray-200">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan Profil
                </button>
            </div>
            
        </form>
    </div>
</div>
@endsection