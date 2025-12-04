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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        input:focus {
            outline: none;
            border-color: #3b82f6; /* Blue-500 */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Card Login -->
        <div class="bg-white p-8 rounded-xl shadow-2xl border-t-4 border-blue-600 transition-all duration-300 transform hover:shadow-3xl">
            <h1 class="text-3xl font-extrabold text-center text-gray-800 mb-2 flex items-center justify-center">
                <!-- Icon Laundry Sederhana (Mesin Cuci) -->
                <svg class="w-9 h-9 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354l.707.707A1 1 0 0013 5.414V6h5a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h5.414l.707-.707zm0 0L7.414 9.414A2 2 0 006 10.828V18h12V10.828a2 2 0 00-.586-1.414L12 4.354zM12 15a3 3 0 100-6 3 3 0 000 6z"></path>
                </svg>
                PowerWash Login
            </h1>
            <p class="text-center text-gray-500 mb-8 text-sm">Masuk untuk mengakses Dashboard Anda</p>

            <!-- Menampilkan Error/Pesan (Area Dinamis) -->
            <div id="error-message-container">
                <!-- Server-side error messages -->
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
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-md transition duration-300">
                        <p class="font-bold text-sm">Login Gagal!</p>
                        <p class="mt-1 text-xs">{{ session('error') }}</p>
                    </div>
                @endif
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 shadow-md transition duration-300">
                        <p class="font-bold text-sm">{{ session('success') }}</p>
                    </div>
                @endif
            </div>

            <form id="login-form" class="space-y-5" method="POST" action="{{ route('login.post') }}">
                @csrf
                <!-- Input Email -->
                <div id="email-group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                        placeholder="contoh@domain.com">
                    <p id="email-error" class="mt-1 text-sm text-red-600 hidden">
                        <!-- Error icon -->
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Email tidak boleh kosong atau format tidak valid.
                    </p>
                </div>

                <!-- Input Password -->
                <div id="password-group">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                        placeholder="Masukkan password Anda">
                    <p id="password-error" class="mt-1 text-sm text-red-600 hidden">
                         <!-- Error icon -->
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Password tidak boleh kosong.
                    </p>
                </div>

                <!-- Tombol Login -->
                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-lg text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 transform hover:scale-[1.01] active:scale-100">
                        Masuk
                    </button>
                </div>
            </form>
            
            <div class="mt-6">
                <p class="text-sm text-center text-gray-500">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 transition duration-150">Daftar Sekarang.</a>
                </p>
            </div>
        </div>
        
        <!-- Footer info removed as requested -->
    </div>

    <script>
        const form = document.getElementById('login-form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');
        const errorMessageContainer = document.getElementById('error-message-container');

        // Fungsi untuk menampilkan pesan error di atas form (mensimulasikan error dari backend/controller)
        const displayBackendError = (message) => {
            const errorHtml = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-md transition duration-300">
                    <p class="font-bold text-sm">Login Gagal!</p>
                    <ul class="mt-1 list-disc list-inside text-xs">
                        <li>${message}</li>
                    </ul>
                </div>
            `;
            errorMessageContainer.innerHTML = errorHtml;
        };

        // Fungsi untuk mengatur tampilan error pada input field
        const setInputError = (inputElement, errorElement, isError) => {
            const errorClass = 'border-red-500';
            
            if (isError) {
                inputElement.classList.add(errorClass);
                inputElement.classList.remove('border-gray-300');
                errorElement.classList.remove('hidden');
            } else {
                inputElement.classList.remove(errorClass);
                inputElement.classList.add('border-gray-300');
                errorElement.classList.add('hidden');
            }
        };

        form.addEventListener('submit', function(event) {
            // Clear previous errors
            errorMessageContainer.innerHTML = '';
            setInputError(emailInput, emailError, false);
            setInputError(passwordInput, passwordError, false);

            const email = emailInput.value.trim();
            const password = passwordInput.value;
            let isValid = true;
            
            // Client-side validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                setInputError(emailInput, emailError, true);
                emailError.textContent = 'Alamat Email harus valid.';
                isValid = false;
            }

            if (!password) {
                setInputError(passwordInput, passwordError, true);
                passwordError.textContent = 'Password tidak boleh kosong.';
                isValid = false;
            }

            if (!isValid) {
                // Prevent form submission if client-side validation fails
                event.preventDefault();
                return;
            }
            // Otherwise allow form submission to the backend (do not prevent default)
        });

        // Optionally keep previously entered email (from server-side old() value)
        try { emailInput.value = emailInput.value || '{{ old('email') ?? '' }}'; } catch(e) {}

    </script>
</body>
</html>