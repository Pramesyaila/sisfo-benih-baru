<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#16A34A', dark: '#166534' },
                        accent: { DEFAULT: '#EAB308', light: '#FEF9C3' },
                        base: '#F8FAFC',
                    }
                }
            }
        }
    </script>
    <style> body { background-color: #F8FAFC; } </style>
</head>
<body class="min-h-screen flex text-black">

    <!-- Sidebar -->
    <aside class="w-64 bg-primary-dark text-white flex-shrink-0 hidden lg:flex flex-col">
        <div class="h-16 flex items-center gap-2 px-5 font-bold text-lg border-b border-white/10">
            <span class="bg-accent text-primary-dark rounded-full w-9 h-9 flex items-center justify-center">🌾</span>
            SI Benih &amp; Bibit
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 font-semibold' : '' }}">📊 Dashboard</a>

            @if(auth()->user()->role === 'manager_gudang')
                <p class="px-3 pt-4 pb-1 text-xs uppercase tracking-wide text-white/50">Katalog &amp; Stok</p>
                <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 font-semibold' : '' }}">🏷️ Kategori</a>
                <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.products.*') ? 'bg-white/10 font-semibold' : '' }}">🌱 Produk</a>
                <a href="{{ route('admin.stock.index') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.stock.index') ? 'bg-white/10 font-semibold' : '' }}">📦 Stok Masuk/Keluar</a>
                <a href="{{ route('admin.stock.history') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.stock.history') ? 'bg-white/10 font-semibold' : '' }}">🕒 Riwayat Stok</a>
                <a href="{{ route('admin.warehouse.index') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.warehouse.*') ? 'bg-white/10 font-semibold' : '' }}">🚚 Serah Terima Benih</a>
            @endif

            @if(auth()->user()->role === 'petugas_layanan')
                <p class="px-3 pt-4 pb-1 text-xs uppercase tracking-wide text-white/50">Pelayanan</p>
                <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.orders.*') ? 'bg-white/10 font-semibold' : '' }}">🧾 Pesanan</a>
                <a href="{{ route('admin.paymentProofs.index') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.paymentProofs.*') ? 'bg-white/10 font-semibold' : '' }}">💳 Verifikasi Pembayaran</a>
            @endif

            @if(in_array(auth()->user()->role, ['petugas_pnbp','petugas_layanan']))
                <a href="{{ route('admin.pnbp.index') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.pnbp.*') ? 'bg-white/10 font-semibold' : '' }}">💰 Tagihan PNBP</a>
            @endif

            <p class="px-3 pt-4 pb-1 text-xs uppercase tracking-wide text-white/50">Laporan</p>
            <a href="{{ route('admin.reports.stock') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.reports.stock') ? 'bg-white/10 font-semibold' : '' }}">📈 Laporan Stok</a>
            <a href="{{ route('admin.reports.sales') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.reports.sales') ? 'bg-white/10 font-semibold' : '' }}">🧮 Laporan Penjualan</a>
            <a href="{{ route('admin.reports.distribution') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.reports.distribution') ? 'bg-white/10 font-semibold' : '' }}">📤 Laporan Distribusi</a>
            <a href="{{ route('admin.reports.pnbp') }}" class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('admin.reports.pnbp') ? 'bg-white/10 font-semibold' : '' }}">🧾 Laporan PNBP</a>

            <p class="px-3 pt-4 pb-1 text-xs uppercase tracking-wide text-white/50">Lainnya</p>

            <a
                href="{{ route('profile.show') }}"
                class="block px-3 py-2 rounded-md hover:bg-white/10 {{ request()->routeIs('profile.*') ? 'bg-white/10 font-semibold' : '' }}"
            >
                👤 Profile
            </a>

            <a
                href="{{ route('catalog.index') }}"
                class="block px-3 py-2 rounded-md hover:bg-white/10"
            >
                🛍️ Lihat Katalog
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 lg:px-6">
            <h1 class="font-semibold text-lg text-primary-dark">@yield('title', 'Dashboard')</h1>
            <div class="flex items-center gap-3">
                <span class="badge bg-accent-light text-primary-dark">{{ auth()->user()->roleLabel() }}</span>
                <span class="text-sm text-gray-600 hidden sm:inline">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="bg-primary text-white text-sm px-3 py-1.5 rounded-md hover:bg-primary-dark">Keluar</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-6">
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
    </div>
</body>
</html>
