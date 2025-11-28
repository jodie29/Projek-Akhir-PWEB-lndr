<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - PowerWash Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        /* FIX KRUSIAL: Memastikan HTML dan BODY memiliki tinggi 100% dari viewport */
        html, body {
            height: 100%; 
            margin: 0; /* Pastikan tidak ada margin default yang mengganggu */
            padding: 0; /* Pastikan tidak ada padding default */
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fc;
        }
        /* Style untuk container utama agar tingginya dihitung dari 100% body */
        .app-container {
            min-height: 100vh; /* Menggunakan vh sebagai fallback dan konfirmasi */
            display: flex;
            flex-direction: column;
        }

        .nav-link {
            transition: all 0.2s ease-in-out;
        }
        .nav-link:hover {
            color: #4f46e5; /* Indigo-600 */
        }
    </style>
</head>
<body>
    <!-- Mengganti min-h-screen flex flex-col dengan class khusus untuk memastikan height propagation -->
    <div class="app-container">
        <!-- Header/Navbar Atas -->
        <header class="bg-white shadow-md sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <a href="{{ route('customer.dashboard') }}" class="text-2xl font-extrabold text-blue-600">
                    PowerWash
                </a>

                <!-- Navigasi Utama -->
                <nav class="hidden md:flex space-x-6">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link text-gray-600 hover:text-indigo-600 font-medium flex items-center">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                    <a href="{{ route('customer.order.create') }}" class="nav-link text-gray-600 hover:text-indigo-600 font-medium flex items-center">
                        <i class="fas fa-plus-square mr-1"></i> Pesan Baru
                    </a>
                    <a href="{{ route('customer.order.history') }}" class="nav-link text-gray-600 hover:text-indigo-600 font-medium flex items-center">
                        <i class="fas fa-history mr-1"></i> Riwayat
                    </a>
                    <a href="{{ route('customer.profile.edit') }}" class="nav-link text-gray-600 hover:text-indigo-600 font-medium flex items-center">
                        <i class="fas fa-user-circle mr-1"></i> Profil
                    </a>
                </nav>

                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500 hidden sm:block">
                        Halo, {{ $user_name ?? 'Pelanggan' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" onsubmit="localStorage.removeItem('pw_active_session_v1'); sessionStorage.removeItem('pw_tab_token_v1');">
                        @csrf
                        <button type="submit" class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition duration-150 shadow">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Konten Utama: Menggunakan flex-grow agar mengisi sisa ruang vertikal -->
        <main class="flex-grow max-w-7xl mx-auto w-full p-6 lg:p-8">
            @yield('content')
        </main>

        <!-- Footer Sederhana -->
        <footer class="bg-white border-t border-gray-200 mt-10 p-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} PowerWash Laundry. Hak Cipta Dilindungi.
        </footer>
    </div>
</body>
</html>