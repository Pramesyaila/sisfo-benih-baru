{{--
    Daftar menu navigasi petugas.

    Dipakai dua kali: pada sidebar lebar (desktop) dan pada drawer
    navigasi (mobile). Menghindaril duplikasi dan menjaga urutan menu
    selalu sama di kedua tampilan.
--}}
<a href="{{ route('admin.dashboard') }}"
   class="{{ $linkClass }} {{ request()->routeIs('admin.dashboard') ? $activeClass : '' }}">
    <span class="nav-item"><span class="nav-icon">📊</span> Dashboard</span>
</a>

@if (auth()->user()->isPetugasLayanan())
    <p class="nav-heading">Administrasi</p>

    <a href="{{ route('admin.orders.index') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.orders.*') ? $activeClass : '' }}">
        <span class="nav-item">
            <span class="nav-icon">🧾</span> Pesanan
            @if (($pendingOrdersCount ?? 0) > 0)
                <span class="nav-badge nav-badge--orders" title="{{ $pendingOrdersCount }} pesanan baru">{{ $pendingOrdersCount }}</span>
            @endif
        </span>
    </a>

    <a href="{{ route('admin.paymentProofs.index') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.paymentProofs.*') ? $activeClass : '' }}">
        <span class="nav-item">
            <span class="nav-icon">💳</span> Verifikasi Pembayaran
            @if (($pendingPaymentsCount ?? 0) > 0)
                <span class="nav-badge nav-badge--verify" title="{{ $pendingPaymentsCount }} bukti menunggu verifikasi">{{ $pendingPaymentsCount }}</span>
            @endif
        </span>
    </a>

    <a href="{{ route('admin.notifications.index') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.notifications.*') ? $activeClass : '' }}">
        <span class="nav-item"><span class="nav-icon">📨</span> Notifikasi Email</span>
    </a>

    <p class="nav-heading">Sistem</p>

    @if (auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.admins.index') }}"
           class="{{ $linkClass }} {{ request()->routeIs('admin.admins.*') ? $activeClass : '' }}">
            <span class="nav-item"><span class="nav-icon">👥</span> Kelola Admin</span>
        </a>
    @endif

    <a href="{{ route('admin.landing.edit') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.landing.*') ? $activeClass : '' }}">
        <span class="nav-item"><span class="nav-icon">🌐</span> Landing Page</span>
    </a>
@endif

@if (auth()->user()->isPetugasGudang())
    <p class="nav-heading">Katalog &amp; Stok</p>

    <a href="{{ route('admin.categories.index') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') ? $activeClass : '' }}">
        <span class="nav-item"><span class="nav-icon">🏷️</span> Kategori &amp; Varietas</span>
    </a>

    <a href="{{ route('admin.products.index') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.products.*') ? $activeClass : '' }}">
        <span class="nav-item"><span class="nav-icon">🌱</span> Produk</span>
    </a>

    <a href="{{ route('admin.stock.index') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.stock.index') ? $activeClass : '' }}">
        <span class="nav-item"><span class="nav-icon">📦</span> Stok Masuk/Keluar</span>
    </a>

    <a href="{{ route('admin.stock.history') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.stock.history') ? $activeClass : '' }}">
        <span class="nav-item"><span class="nav-icon">🕒</span> Riwayat Stok</span>
    </a>

    <a href="{{ route('admin.warehouse.index') }}"
       class="{{ $linkClass }} {{ request()->routeIs('admin.warehouse.*') ? $activeClass : '' }}">
        <span class="nav-item"><span class="nav-icon">🚚</span> Pengambilan Benih</span>
    </a>
@endif

<p class="nav-heading">Laporan</p>

<a href="{{ route('admin.reports.sales') }}"
   class="{{ $linkClass }} {{ request()->routeIs('admin.reports.sales') ? $activeClass : '' }}">
    <span class="nav-item"><span class="nav-icon">🧾</span> Laporan Penjualan</span>
</a>

<a href="{{ route('admin.reports.stock') }}"
   class="{{ $linkClass }} {{ request()->routeIs('admin.reports.stock') ? $activeClass : '' }}">
    <span class="nav-item"><span class="nav-icon">📈</span> Laporan Stok</span>
</a>

<p class="nav-heading">Lainnya</p>

<a href="{{ route('profile.show') }}"
   class="{{ $linkClass }} {{ request()->routeIs('profile.*') ? $activeClass : '' }}">
    <span class="nav-item"><span class="nav-icon">👤</span> Profile</span>
</a>

<a href="{{ route('catalog.index') }}" class="{{ $linkClass }}">
    <span class="nav-item"><span class="nav-icon">🛍️</span> Lihat Katalog</span>
</a>