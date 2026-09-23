<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Katalog Benih & Bibit')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#16A34A', dark: '#166534' },
                        accent: { DEFAULT: '#EAB308', light: '#FEF9C3' },
                        base: '#F8FAFC',
                    },
                    fontFamily: {
                        sans: ['Segoe UI', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F8FAFC; }
        .badge { @apply inline-block px-2 py-1 rounded-full text-xs font-semibold; }
    </style>
</head>
<body class="min-h-screen flex flex-col text-black">

    <header class="bg-primary-dark text-white sticky top-0 z-40 shadow">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('catalog.index') }}" class="flex items-center gap-2 font-bold text-lg">
                    <span class="bg-accent text-primary-dark rounded-full w-9 h-9 flex items-center justify-center">🌾</span>
                    <span>SI Benih &amp; Bibit</span>
                </a>

                <form action="{{ route('catalog.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-6">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari benih / bibit..."
                        class="w-full rounded-l-md px-3 py-2 text-black focus:outline-none">
                    <button class="bg-accent text-primary-dark px-4 rounded-r-md font-semibold hover:brightness-95">Cari</button>
                </form>

                <nav class="flex items-center gap-4 text-sm">
                    @auth
                         @if(auth()->user()->isKonsumen())

                        <a
                            href="{{ route('cart.index') }}"
                            class="hover:text-accent-light flex items-center gap-1"
                >
                    Keranjang
                    </a>

                <a
                    href="{{ route('orders.index') }}"
                    class="hover:text-accent-light"
                >
                    Pesanan Saya
                </a>

                <a
                    href="{{ route('profile.show') }}"
                    class="hover:text-accent-light"
                >
                    Profile
                </a>

        @else

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="hover:text-accent-light"
                >
                    Dashboard Admin
                </a>

        @endif
                        <span class="hidden sm:inline text-white/80">Hai, {{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="bg-accent text-primary-dark px-3 py-1.5 rounded-md font-semibold hover:brightness-95">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-accent-light">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-accent text-primary-dark px-3 py-1.5 rounded-md font-semibold hover:brightness-95">Daftar</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 bg-primary/10 border border-primary text-primary-dark px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-md">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-md">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-primary-dark text-white/80 text-sm">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center">
            &copy; {{ date('Y') }} Sistem Informasi Pengelolaan &amp; Penjualan Benih/Bibit
        </div>
    </footer>
</body>
</html>
