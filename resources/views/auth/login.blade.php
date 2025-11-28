<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PowerWash Laundry</title>
    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Mengatur font Inter -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Card Login -->
        <div class="bg-white p-8 rounded-xl shadow-2xl border-t-4 border-blue-600">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-2 flex items-center justify-center">
                <!-- Icon Laundry Sederhana (Contoh: Baju) -->
                <svg class="w-8 h-8 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                PowerWash Login
            </h1>
            <p class="text-center text-gray-500 mb-8 text-sm">Masuk untuk mengakses Dashboard Anda</p>

            <!-- Menampilkan Error/Pesan dari Controller -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-md transition duration-300">
                    <p class="font-bold text-sm">Login Gagal!</p>
                    <ul class="mt-1 list-disc list-inside text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        {{-- Menggunakan direktif @class untuk menghindari peringatan konflik CSS IDE --}}
                        @class([
                            'block w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150',
                            'border-red-500' => $errors->has('email'),
                            'border-gray-300' => !$errors->has('email')
                        ])
                        placeholder="contoh@domain.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        {{-- Menggunakan direktif @class untuk menghindari peringatan konflik CSS IDE --}}
                        @class([
                            'block w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150',
                            'border-red-500' => $errors->has('password'),
                            'border-gray-300' => !$errors->has('password')
                        ])
                        placeholder="Masukkan password Anda">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tombol Login -->
                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-lg text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 transform hover:scale-[1.01]">
                        Masuk
                    </button>
                </div>
            </form>
            
            <div class="mt-6">
                <p class="text-sm text-center text-gray-500">
                    Belum punya akun? 
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition duration-150">Daftar Sekarang.</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>