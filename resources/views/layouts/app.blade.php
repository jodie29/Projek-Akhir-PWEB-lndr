<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PowerWash</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <a href="/" class="text-xl font-bold">PowerWash</a>
                    <nav class="flex items-center gap-4">
                        @auth
                            <span class="text-sm text-gray-700 mr-3 hidden md:block">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" onsubmit="localStorage.removeItem('pw_active_session_v1'); sessionStorage.removeItem('pw_tab_token_v1');">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-1 px-3 rounded-lg transition duration-150 shadow-md">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Login</a>
                        @endauth
                    </nav>
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 py-6">
            @auth
                @if((Auth::user()->role ?? '') === 'courier' && ! \Illuminate\Support\Facades\Schema::hasColumn('orders', 'courier_id'))
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                        <p class="text-sm text-red-700">Peringatan: Kolom <code>courier_id</code> belum tersedia di database. Beberapa fitur kurir dapat terbatas; Jalankan <code>php artisan migrate</code> di server untuk menyelesaikan.</p>
                    </div>
                @endif
            @endauth
            @yield('content')
        </main>

        <footer class="text-center text-sm text-gray-500 py-6">
            &copy; {{ date('Y') }} PowerWash
        </footer>
    </div>
    @auth
    <script>
        // Security behavior: if a protected page is opened in a new tab (no sessionStorage token)
        // then auto-logout and return to login page to avoid shared links giving access.
        (function() {
            try {
                const tokenKey = 'pw_tab_token_v1';
                const globalKey = 'pw_active_session_v1';
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const logoutUrl = "{{ route('logout') }}";
                const loginUrl = "{{ route('login') }}";

                // Ensure this tab is marked as active for the logged-in user. Previously,
                // we treated missing sessionStorage token as a sign to force-logout, which
                // caused users who open protected pages in a new tab to be logged out.
                //
                // New behavior: when a user is authenticated, set a session token for the
                // current tab and refresh a global localStorage token so other tabs remain
                // aware of the active session and will redirect when logout occurs.
                // Allow opting into the old "single-tab only" behavior by setting a
                // environment variable `SESSION_ENFORCE_SINGLE_TAB=true`. Default is off
                // (do not force automatic logout on new tabs).
                const enforceSingleTab = {{ config('app.session_enforce_single_tab', false) ? 'true' : 'false' }};

                if (enforceSingleTab) {
                    // If enforcing single-tab sessions, perform the old check where lack
                    // of a sessionStorage token triggers a server logout and redirect.
                    if (!sessionStorage.getItem(tokenKey)) {
                        fetch(logoutUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({})
                        }).catch(() => {})
                        .finally(() => {
                            localStorage.removeItem(globalKey);
                            sessionStorage.removeItem(tokenKey);
                            window.location.href = loginUrl;
                        });
                    } else {
                        // session tab token present; refresh global token to keep session marked active
                        sessionStorage.setItem(tokenKey, '1');
                        localStorage.setItem(globalKey, Date.now());
                    }
                } else {
                    // Default new behavior: ensure this tab is marked active and refresh
                    // the shared global token so other tabs stay in sync when the user
                    // explicitly logs out.
                    if (!sessionStorage.getItem(tokenKey)) {
                        sessionStorage.setItem(tokenKey, '1');
                    }
                    localStorage.setItem(globalKey, Date.now());
                }
            } catch (e) {
                // fail silently - do not break page
                console.warn('Tab token check failed', e);
            }
            // Sync logout across all tabs: when the global token is removed, ensure other tabs redirect
            window.addEventListener('storage', function(e) {
                if (e.key === globalKey && (e.newValue === null || e.newValue === undefined)) {
                    try { window.location.href = loginUrl; } catch (e) {}
                }
            });
        })();
    </script>
    @endauth
</body>
</html>
