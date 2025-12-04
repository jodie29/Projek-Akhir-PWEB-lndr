<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Pembayaran & Penjemputan - PowerWash</title>

    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Konfigurasi Tailwind & Font Inter -->
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background-color: #f3f4f6; /* bg-gray-100 */
        }
        /* Custom ring focus for better accessibility */
        input:focus, select:focus, button:focus {
            border-color: #3b82f6; 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5); 
            outline: none;
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <div class="max-w-2xl mx-auto py-4">
        <h1 id="order-title" class="text-3xl font-extrabold text-gray-800 mb-6">Catat Pembayaran</h1>

        <!-- Flash Message Area -->
        <div id="flash-message-container">
            <!-- Success message will appear here after submission -->
        </div>

        <!-- Order Summary Card -->
        <div class="bg-white shadow-xl rounded-xl p-6 mb-6 border-l-4 border-blue-500">
            <!-- Component: Header Illustration -->
            <div class="flex items-center justify-between mb-4 border-b pb-4 border-gray-100">
                <div class="text-left w-3/4">
                    <h2 class="text-xl font-semibold text-blue-700">Catat Pembayaran & Penjemputan</h2>
                    <p class="text-sm text-gray-500">Pastikan detail pesanan sudah sesuai.</p>
                </div>
                <div class="w-1/4 flex justify-end">
                    <img src="https://static.vecteezy.com/system/resources/previews/026/721/193/non_2x/washing-machine-and-laundry-laundry-sticker-png.png" 
                         alt="Ilustrasi Laundry" class="h-16 w-auto object-contain">
                </div>
            </div>
            <!-- End Component: Header Illustration -->
            
            <div id="order-details" class="space-y-2 text-gray-700 text-base">
                <p><strong>Layanan:</strong> {{ $order->service->name ?? '-' }}</p>
                <p><strong>Berat:</strong> {{ $order->actual_weight ?? '-' }} kg</p>
                <p class="text-lg font-bold text-red-600"><strong>Total yang harus dibayar:</strong> Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Payment Form -->
        <form method="POST" action="{{ route('courier.orders.pickup.store', $order->id) }}" id="payment-form" class="bg-white shadow-xl rounded-xl p-6">
            @csrf

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm font-medium">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm font-medium">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm font-medium">
                    <strong>Kesalahan Validasi:</strong>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Error message for form validation -->
             <div id="form-error-container" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm font-medium">
                <!-- Validation errors will be injected here -->
            </div>
            
            <div class="mb-5">
                <label for="collection_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select id="collection_method" name="collection_method" class="w-full border border-gray-300 px-4 py-2.5 rounded-lg shadow-sm transition duration-150" required>
                    <option value="">Pilih Metode Pembayaran</option>
                    <option value="Tunai" {{ old('collection_method') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ old('collection_method') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    <option value="Transfer" {{ old('collection_method') == 'Transfer' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="collected_amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah yang dikumpulkan (Rp)</label>
                <input type="number" step="100" id="collected_amount" name="collected_amount" 
                       class="w-full border border-gray-300 px-4 py-2.5 rounded-lg shadow-sm transition duration-150" 
                       required placeholder="Contoh: 38250" value="{{ old('collected_amount', $order->total_price ?? '') }}">
            </div>

            <div class="flex gap-4 justify-end pt-4 border-t border-gray-100">
                <a href="#" onclick="history.back(); return false;" class="px-5 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition duration-150">Batal</a>
                <button type="submit" id="submit-button" class="px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150 transform active:scale-95">
                    Simpan & Catat
                </button>
            </div>
        </form>
    </div>

    <script>
        // Minimal JS: focus the amount field for convenience
        document.addEventListener('DOMContentLoaded', function() {
            const amount = document.getElementById('collected_amount');
            if (amount) amount.focus();
        });
    </script>
</body>
</html>