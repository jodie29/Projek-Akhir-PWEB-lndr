<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Kurir (Simulasi)</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for 'Inter' font and base appearance */
        :root {
            font-family: 'Inter', sans-serif;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            transition: all 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #3b82f6; /* Blue-500 */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-start justify-center p-4 sm:p-6">

    <div class="max-w-2xl w-full bg-white rounded-xl shadow-xl p-6 sm:p-8 mt-10">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-3">Ubah Kurir</h1>

        <!-- Error/Message Display Area -->
        <div id="message-container" class="mb-6 hidden">
            <!-- Messages will be injected here -->
        </div>

        <!-- Catatan: Method POST digunakan di front-end, meskipun di Laravel akan di-override menjadi PUT/PATCH oleh @method('PUT') -->
        <form id="courier-form">
            <!-- Name Input -->
            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                <!-- Simulasi data yang sudah ada: $courier->name -->
                <input type="text" id="name" name="name" value="John Doe" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 transition duration-150 ease-in-out focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Email Input -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <!-- Simulasi data yang sudah ada: $courier->email -->
                <input type="email" id="email" name="email" value="john.doe@example.com" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 transition duration-150 ease-in-out focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Password Input (Kosong = tidak diubah) -->
            <div class="mb-5">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password (kosong = tidak diubah)</label>
                <!-- Atribut 'required' dihapus -->
                <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 transition duration-150 ease-in-out focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Confirm Password Input (Kosong = tidak diubah) -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                <!-- Atribut 'required' dihapus -->
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 transition duration-150 ease-in-out focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-150 ease-in-out transform hover:scale-[1.01]">
                    Simpan Perubahan
                </button>
                <a href="#" onclick="alert('Navigasi ke Daftar Kurir'); return false;" class="text-gray-600 hover:text-gray-800 transition duration-150 ease-in-out">
                    Batal
                </a>
                <!-- Link kembali ke Dashboard, ditambahkan kembali untuk kemudahan navigasi simulasi -->
                <a href="#" onclick="alert('Navigasi ke Dashboard'); return false;" class="ml-4 px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-lg shadow-sm hover:bg-gray-300 transition duration-150 ease-in-out">
                    Kembali ke Dashboard
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('courier-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Stop the actual form submission

            const form = event.target;
            const name = form.name.value.trim();
            const email = form.email.value.trim();
            const password = form.password.value;
            const confirmPassword = form.password_confirmation.value;
            const messageContainer = document.getElementById('message-container');

            let errors = [];

            // Basic Validation Checks (Name and Email must still be valid)
            if (name.length < 3) {
                errors.push('Nama harus memiliki minimal 3 karakter.');
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.push('Format Email tidak valid.');
            }

            const isPasswordChanging = password.length > 0 || confirmPassword.length > 0;

            // Password Validation (Only run if the user attempts to change the password)
            if (isPasswordChanging) {
                if (password.length > 0 && password.length < 8) {
                    errors.push('Password baru harus memiliki minimal 8 karakter.');
                }
                if (password !== confirmPassword) {
                    errors.push('Konfirmasi Password tidak cocok dengan Password baru.');
                }
                // Check if only confirmation is filled, but password isn't
                if (password.length === 0 && confirmPassword.length > 0) {
                    errors.push('Harap isi bidang Password baru jika Anda ingin mengonfirmasi.');
                }
                // Check if password is filled but confirmation isn't (already caught by mismatch, but good for clarity)
                if (password.length > 0 && confirmPassword.length === 0) {
                     errors.push('Konfirmasi Password diperlukan.');
                }
            }


            // Display Results
            messageContainer.innerHTML = '';
            messageContainer.classList.add('hidden');

            if (errors.length > 0) {
                // Display Errors (Red Box)
                const errorHtml = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                        <strong class="font-bold">Terjadi Kesalahan!</strong>
                        <span class="block sm:inline"> Mohon perbaiki kesalahan berikut:</span>
                        <ul class="list-disc ml-5 mt-2">
                            ${errors.map(err => `<li>${err}</li>`).join('')}
                        </ul>
                    </div>
                `;
                messageContainer.innerHTML = errorHtml;
                messageContainer.classList.remove('hidden');
            } else {
                // Successful Submission Simulation (Green Box)
                let message = `Kurir: "${name}" (${email}) berhasil diperbarui (simulasi).`;
                if (isPasswordChanging) {
                    message += ` Password juga telah diubah.`;
                }

                const successHtml = `
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                        <strong class="font-bold">Berhasil!</strong>
                        <span class="block sm:inline"> ${message}</span>
                    </div>
                `;
                messageContainer.innerHTML = successHtml;
                messageContainer.classList.remove('hidden');
                
                // Clear only password fields upon successful update
                form.password.value = '';
                form.password_confirmation.value = '';
            }

            // Scroll to the message area
            messageContainer.scrollIntoView({ behavior: 'smooth' });
        });
    </script>
</body>
</html>