<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PowerWash</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <a href="/" class="text-xl font-bold">PowerWash</a>
                    <nav>
                        <!-- simple nav -->
                    </nav>
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 py-6">
            @yield('content')
        </main>

        <footer class="text-center text-sm text-gray-500 py-6">
            &copy; {{ date('Y') }} PowerWash
        </footer>
    </div>
</body>
</html>
