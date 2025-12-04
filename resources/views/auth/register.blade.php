<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PowerWash Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.25); }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white p-8 rounded-xl shadow-2xl border-t-4 border-blue-600 transition-all duration-300">
            <h1 class="text-2xl font-extrabold text-center text-gray-800 mb-2">Buat Akun Pelanggan</h1>
            <p class="text-center text-gray-500 mb-6 text-sm">Daftar untuk membuat akun pelanggan dan mulai membuat pesanan.</p>

            <div id="error-message-container">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-md">
                        <p class="font-bold text-sm">Terjadi kesalahan:</p>
                        <ul class="mt-1 list-disc list-inside text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-md">
                        <p class="font-bold text-sm">{{ session('error') }}</p>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('register.post') }}" id="register-form" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}" class="block w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Nama Anda">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" class="block w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="email@domain.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Ketik ulang password Anda">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input id="phone" name="phone" type="text" required value="{{ old('phone') }}" class="block w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="0812xxxx">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat (opsional)</label>
                    <textarea id="address" name="address" class="block w-full px-4 py-2 border border-gray-300 rounded-lg" rows="3" placeholder="Alamat Anda">{{ old('address') }}</textarea>
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Daftar</button>
                </div>
            </form>

            <p class="mt-4 text-sm text-center text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600">Masuk</a></p>
        </div>
    </div>

    <script>
        // Basic client-side checks to improve UX (doesn't replace server validation)
        document.getElementById('register-form').addEventListener('submit', function(e) {
            const pw = document.getElementById('password').value;
            const pw2 = document.getElementById('password_confirmation').value;
            if (pw.length < 8) {
                alert('Password harus minimal 8 karakter.');
                e.preventDefault();
                return;
            }
            if (pw !== pw2) {
                alert('Password dan konfirmasi tidak cocok.');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>