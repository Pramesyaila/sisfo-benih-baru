<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#166534">
    <title>@yield('title', 'Benih & Bibit')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#16A34A', dark: '#166534' },
                        accent: { DEFAULT: '#EAB308', light: '#FEF9C3' },
                        base: '#F8FAFC'
                    },
                    fontFamily: {
                        sans: ['Segoe UI', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    }
                }
            }
        };
    </script>
    @include('components.customer.styles')
</head>
<body class="customer-body">
@php
    // Angka pada notifikasi keranjang menghitung jumlah produk (id pesanan item),
    // bukan jumlah unit. Satu produk dengan qty 5 tetap dihitung satu.
    $cartCount = auth()->check() && auth()->user()->isKonsumen()
        ? collect(session('cart', []))->count()
        : 0;

    // Notifikasi digabung per order: satu pesanan = satu entri, berapa pun
    // jumlah produk atau pembaruan notifikasi yang terkait.
    $notificationGroups = collect();
    $unreadCount = 0;

    if (auth()->check() && auth()->user()->isKonsumen()) {
        $notificationGroups = auth()->user()->unreadNotifications()
            ->latest()
            ->limit(50)
            ->get()
            ->groupBy(fn ($notification) => $notification->data['order_id'] ?? $notification->id)
            ->map(function ($notifications) {
                $latest = $notifications->first();

                return [
                    'latest' => $latest,
                    'count' => $notifications->count(),
                ];
            })
            ->sortByDesc(fn ($group) => $group['latest']->created_at)
            ->take(5)
            ->values();

        $unreadCount = $notificationGroups->sum('count');
    }
@endphp

<div class="customer-shell">
    <header class="site-header">
        <div class="customer-container">
            <div class="header-main">
                @php $landingLogo = \App\Models\LandingContent::current()->logoUrl(); @endphp
                <a href="{{ route('catalog.index') }}" class="brand" aria-label="Beranda katalog benih dan bibit">
                    <span class="brand-mark">
                        @if ($landingLogo)
                            <img src="{{ $landingLogo }}" alt="Logo instansi" class="brand-logo">
                        @else
                            <x-customer.icon name="sprout" :size="23"></x-customer.icon>
                        @endif
                    </span>
                    <span class="brand-copy">
                        <span class="brand-kicker">KATALOG RESMI</span>
                        <span class="brand-name">Benih &amp; Bibit</span>
                    </span>
                </a>

                <form action="{{ route('catalog.index') }}" method="GET" class="header-search" role="search">
                    <span class="header-search__icon"><x-customer.icon name="search" :size="17"></x-customer.icon></span>
                    <input class="header-search__input" type="search" name="q" value="{{ request('q') }}" placeholder="Cari benih atau bibit..." aria-label="Cari benih atau bibit">
                    <button class="header-search__button" type="submit">Cari</button>
                </form>

                @auth
                    @if (auth()->user()->isKonsumen())
                        {{-- Urutan: Katalog, Pesanan, Keranjang, lalu Profil. --}}
                        <nav class="desktop-nav" aria-label="Navigasi pelanggan">
                            <a class="nav-link {{ request()->routeIs('catalog.*') ? 'nav-link--active' : '' }}" href="{{ route('catalog.index') }}">
                                <span class="nav-icon"><x-customer.icon name="leaf" :size="18"></x-customer.icon></span>
                                Katalog
                            </a>
                            <a class="nav-link {{ request()->routeIs('orders.*') ? 'nav-link--active' : '' }}" href="{{ route('orders.index') }}">
                                <span class="nav-icon"><x-customer.icon name="clipboard" :size="18"></x-customer.icon></span>
                                Pesanan
                            </a>
                            <a class="nav-link cart-link {{ request()->routeIs('cart.*', 'checkout.*') ? 'nav-link--active' : '' }}" href="{{ route('cart.index') }}">
                                <span class="nav-icon"><x-customer.icon name="bag" :size="18"></x-customer.icon></span>
                                Keranjang
                                @if ($cartCount > 0)
                                    <span class="cart-count">{{ $cartCount }}</span>
                                @endif
                            </a>
                            <a class="nav-link {{ request()->routeIs('profile.*') ? 'nav-link--active' : '' }}" href="{{ route('profile.show') }}">
                                <span class="nav-icon"><x-customer.icon name="user" :size="18"></x-customer.icon></span>
                                Profil
                            </a>
                        </nav>
                        <div class="header-actions">
                            <details class="notification-menu">
                                <summary class="notification-bell" aria-label="Notifikasi">
                                    <x-customer.icon name="bell" :size="18"></x-customer.icon>
                                    @if ($notificationGroups->isNotEmpty())
                                        <span class="notification-dot">{{ $notificationGroups->count() }}</span>
                                    @endif
                                </summary>
                                <div class="notification-panel">
                                    <p class="notification-panel__title">Notifikasi</p>
                                    @forelse ($notificationGroups as $group)
                                        <form action="{{ route('notifications.read', $group['latest']->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="notification-item">
                                                <strong>{{ $group['latest']->data['title'] ?? 'Notifikasi' }}</strong>
                                                <span>{{ $group['latest']->data['message'] ?? '' }}</span>
                                            </button>
                                        </form>
                                    @empty
                                        <p class="notification-panel__empty">Belum ada notifikasi baru.</p>
                                    @endforelse
                                    @if ($notificationGroups->isNotEmpty())
                                        <a class="notification-panel__more" href="{{ route('notifications.index') }}">Lihat semua notifikasi</a>
                                    @endif
                                </div>
                            </details>
                            <span class="header-user">Hai, {{ auth()->user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="header-logout" type="submit">Keluar</button>
                            </form>
                        </div>
                    @else
                        <nav class="desktop-nav" aria-label="Navigasi staf">
                            <a class="nav-link {{ request()->routeIs('catalog.*') ? 'nav-link--active' : '' }}" href="{{ route('catalog.index') }}">
                                <span class="nav-icon"><x-customer.icon name="leaf" :size="16"></x-customer.icon></span>
                                Lihat katalog
                            </a>
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <span class="nav-icon"><x-customer.icon name="clipboard" :size="16"></x-customer.icon></span>
                                Dashboard admin
                            </a>
                        </nav>
                        <div class="header-actions">
                            <span class="header-user">{{ auth()->user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="header-logout" type="submit">Keluar</button>
                            </form>
                        </div>
                    @endif
                @else
                    <nav class="desktop-nav" aria-label="Navigasi umum">
                        <a class="nav-link {{ request()->routeIs('catalog.*') ? 'nav-link--active' : '' }}" href="{{ route('catalog.index') }}">
                            <span class="nav-icon"><x-customer.icon name="leaf" :size="16"></x-customer.icon></span>
                            Katalog
                        </a>
                    </nav>
                    <div class="header-actions">
                        <a class="btn btn--quiet btn--small" href="{{ route('login') }}">Masuk</a>
                        <a class="btn btn--gold btn--small" href="{{ route('register') }}">Daftar</a>
                    </div>
                @endauth

                <button id="customer-menu-toggle" class="mobile-menu-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" aria-controls="customer-mobile-menu">
                    <x-customer.icon name="menu" :size="19"></x-customer.icon>
                </button>
            </div>

            <div id="customer-mobile-menu" class="mobile-menu" aria-hidden="true">
                <form action="{{ route('catalog.index') }}" method="GET" class="header-search mobile-menu__search" role="search">
                    <span class="header-search__icon"><x-customer.icon name="search" :size="17"></x-customer.icon></span>
                    <input class="header-search__input" type="search" name="q" value="{{ request('q') }}" placeholder="Cari benih atau bibit..." aria-label="Cari benih atau bibit">
                    <button class="header-search__button" type="submit">Cari</button>
                </form>
                <nav class="mobile-menu__links" aria-label="Navigasi seluler">
                    <a class="mobile-menu__link {{ request()->routeIs('catalog.*') ? 'mobile-menu__link--active' : '' }}" href="{{ route('catalog.index') }}">
                        <x-customer.icon name="leaf" :size="17"></x-customer.icon>
                        Katalog
                    </a>
                    @auth
                        @if (auth()->user()->isKonsumen())
                            <a class="mobile-menu__link {{ request()->routeIs('orders.*') ? 'mobile-menu__link--active' : '' }}" href="{{ route('orders.index') }}">
                                <x-customer.icon name="clipboard" :size="17"></x-customer.icon>
                                Pesanan saya
                            </a>
                            <a class="mobile-menu__link {{ request()->routeIs('profile.*') ? 'mobile-menu__link--active' : '' }}" href="{{ route('profile.show') }}">
                                <x-customer.icon name="user" :size="17"></x-customer.icon>
                                Profil saya
                            </a>
                            <a class="mobile-menu__link {{ request()->routeIs('cart.*', 'checkout.*') ? 'mobile-menu__link--active' : '' }}" href="{{ route('cart.index') }}">
                                <x-customer.icon name="bag" :size="17"></x-customer.icon>
                                Keranjang
                                @if ($cartCount > 0)
                                    <span class="cart-count">{{ $cartCount }}</span>
                                @endif
                            </a>
                        @else
                            <a class="mobile-menu__link" href="{{ route('admin.dashboard') }}">
                                <x-customer.icon name="clipboard" :size="17"></x-customer.icon>
                                Dashboard admin
                            </a>
                        @endif
                    @else
                        <a class="mobile-menu__link" href="{{ route('login') }}">
                            <x-customer.icon name="user" :size="17"></x-customer.icon>
                            Masuk
                        </a>
                        <a class="mobile-menu__link" href="{{ route('register') }}">
                            <x-customer.icon name="arrow-right" :size="17"></x-customer.icon>
                            Daftar akun
                        </a>
                    @endauth
                </nav>
                @auth
                    <div class="mobile-menu__meta">
                        @if (auth()->user()->isKonsumen() && $notificationGroups->isNotEmpty())
                            <div class="mb-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-[var(--muted)]">Notifikasi</p>
                                @foreach ($notificationGroups as $group)
                                    <form action="{{ route('notifications.read', $group['latest']->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="mobile-menu__link w-full text-left">
                                            <x-customer.icon name="bell" :size="17"></x-customer.icon>
                                            {{ $group['latest']->data['title'] ?? 'Notifikasi' }}
                                        </button>
                                    </form>
                                @endforeach
                                <a class="mobile-menu__link" href="{{ route('notifications.index') }}">
                                    <x-customer.icon name="bell" :size="17"></x-customer.icon>
                                    Lihat semua notifikasi
                                </a>
                            </div>
                        @endif
                        <span>{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="mobile-menu__logout" type="submit">Keluar</button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main class="page-main">
        <div class="customer-container">
            {{-- Alert konsisten untuk sisi konsumen. --}}
            <div class="space-y-3 mb-4 empty:mb-0">
                @if (session('success'))
                    <x-customer.alert type="success" :message="session('success')"></x-customer.alert>
                @endif

                @if (session('error'))
                    <x-customer.alert type="error" :message="session('error')"></x-customer.alert>
                @endif

                @if (session('warning'))
                    <x-customer.alert type="warning" :message="session('warning')"></x-customer.alert>
                @endif

                @if ($errors->any())
                    <x-customer.alert
                        type="error"
                        title="Periksa kembali data berikut"
                        :errors="$errors->all()"
                    ></x-customer.alert>
                @endif
            </div>

            @yield('content')
        </div>
    </main>

    <footer class="site-footer">
        <div class="customer-container">
            <div class="footer-main">
                <div class="footer-brand">
                    <div class="brand">
                        <span class="brand-mark"><x-customer.icon name="sprout" :size="22"></x-customer.icon></span>
                        <span class="brand-copy">
                            <span class="brand-kicker">KATALOG RESMI</span>
                            <span class="brand-name">Benih &amp; Bibit</span>
                        </span>
                    </div>
                    <p>Temukan benih dan bibit pilihan, cek ketersediaan, lalu ajukan pesanan dengan mudah.</p>
                </div>
                <div>
                    <h2 class="footer-title">Jelajahi</h2>
                    <div class="footer-links">
                        <a href="{{ route('catalog.index') }}">Katalog produk</a>
                        @auth
                            @if (auth()->user()->isKonsumen())
                                <a href="{{ route('orders.index') }}">Pesanan saya</a>
                                <a href="{{ route('profile.show') }}">Profil saya</a>
                            @else
                                <a href="{{ route('admin.dashboard') }}">Dashboard admin</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}">Masuk</a>
                        @endauth
                    </div>
                </div>
                <div>
                    <h2 class="footer-title">Informasi</h2>
                    <div class="footer-links">
                        <span>Produk &amp; ketersediaan</span>
                        <span>Administrasi pesanan</span>
                        <span>Proses &amp; pengambilan</span>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} Sistem Informasi Pengelolaan &amp; Penjualan Benih/Bibit</span>
                <span>Dirancang untuk pengalaman pemesanan yang lebih jelas.</span>
            </div>
        </div>
    </footer>
</div>

<script>
    (function () {
        const toggle = document.getElementById('customer-menu-toggle');
        const menu = document.getElementById('customer-mobile-menu');
        if (!toggle || !menu) return;

        toggle.addEventListener('click', function () {
            const isOpen = menu.classList.toggle('is-open');
            menu.setAttribute('aria-hidden', String(!isOpen));
            toggle.setAttribute('aria-expanded', String(isOpen));
            toggle.setAttribute('aria-label', isOpen ? 'Tutup navigasi' : 'Buka navigasi');
            toggle.innerHTML = isOpen
                ? '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"></path></svg>'
                : '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>';
        });
    })();
</script>
</body>
</html>
