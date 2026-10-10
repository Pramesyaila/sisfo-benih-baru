@extends('layouts.app')

@section('title', 'Katalog Benih & Bibit')

@section('content')
<section class="hero">
    <div class="hero__copy">
        <span class="hero__eyebrow">Benih &amp; bibit pilihan</span>
        @auth
            @if (auth()->user()->isKonsumen())
                <h1 class="hero__title">Selamat datang,<br>{{ auth()->user()->name }}.</h1>
            @else
                <h1 class="hero__title">{{ $landing->hero_title }}</h1>
            @endif
        @else
            <h1 class="hero__title">{{ $landing->hero_title }}</h1>
        @endauth
        <p class="hero__description">{{ $landing->hero_subtitle }}</p>
        @if ($landing->announcement)
            <div class="hero__note">
                <x-customer.icon name="info" :size="15"></x-customer.icon>
                <span>{{ $landing->announcement }}</span>
            </div>
        @endif
        <div class="hero__actions">
            <a class="btn btn--gold" href="#daftar-produk">
                Jelajahi produk
                <x-customer.icon name="arrow-right" :size="16"></x-customer.icon>
            </a>
            @auth
                @if (auth()->user()->isKonsumen())
                    <a class="btn btn--secondary" href="{{ route('orders.index') }}">
                        Lihat pesanan saya
                    </a>
                @endif
            @else
                <a class="btn btn--secondary" href="{{ route('register') }}">Buat akun pelanggan</a>
            @endauth
        </div>
        <div class="hero__note">
            <x-customer.icon name="shield" :size="15"></x-customer.icon>
            <span>Informasi produk dan status pesanan tersusun dalam satu alur.</span>
        </div>
    </div>
    <div class="hero__art" aria-hidden="true">
        <span class="hero__art-stamp">-grown with care</span>
        <span class="hero__art-orbit"></span>
        <svg class="hero__art-sprout" width="205" height="205" viewBox="0 0 220 220" fill="none">
            <path d="M111 190V78" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
            <path d="M111 125C67 127 43 105 42 62c43-2 67 20 69 63Z" fill="currentColor" opacity=".83"></path>
            <path d="M111 105c2-45 29-68 72-70 1 44-26 68-72 70Z" fill="currentColor" opacity=".6"></path>
            <path d="M111 151c34 0 56-17 57-48-34-2-56 16-57 48Z" fill="currentColor" opacity=".72"></path>
            <path d="M75 190h73" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
            <path d="M31 190c32-13 126-13 158 0" stroke="#FEF9C3" stroke-width="2" stroke-linecap="round" opacity=".8"></path>
        </svg>
        <div class="hero__art-label">
            <strong>Siap tumbuh</strong>
            <span>Pilih produk yang sesuai untuk kebutuhan Anda.</span>
        </div>
    </div>
</section>

<section class="trust-strip" aria-label="Keunggulan katalog">
    <div class="trust-item">
        <span class="trust-item__icon"><x-customer.icon name="leaf" :size="19"></x-customer.icon></span>
        <span class="trust-item__copy"><strong>Produk terkurasi</strong><span>Benih dan bibit dengan informasi yang jelas.</span></span>
    </div>
    <div class="trust-item">
        <span class="trust-item__icon"><x-customer.icon name="package" :size="19"></x-customer.icon></span>
        <span class="trust-item__copy"><strong>Kemasan transparan</strong><span>Ukuran, satuan, dan harga terlihat sebelum memesan.</span></span>
    </div>
    <div class="trust-item">
        <span class="trust-item__icon"><x-customer.icon name="clipboard" :size="19"></x-customer.icon></span>
        <span class="trust-item__copy"><strong>Status mudah dilacak</strong><span>Perjalanan pesanan tersedia di akun Anda.</span></span>
    </div>
</section>

{{--
  Filter kategori/varietas memakai tautan biasa, namun POSIX scroll restoration
  menjaga posisi gulir sehingga halaman tidak kembali ke paling atas.
--}}
<section id="daftar-produk" class="catalog-anchor">
    <div class="catalog-toolbar">
        <div class="catalog-toolbar__heading">
            <span class="section-kicker">Pilihan untuk Anda</span>
            <h2>Daftar produk</h2>
            <p>Temukan produk yang sesuai, lalu klik untuk melihat detail lengkapnya.</p>
        </div>
        <div class="catalog-toolbar__meta">{{ $products->total() }} produk ditemukan</div>
    </div>

    <nav class="filter-list" aria-label="Filter kategori">
        <a class="filter-link {{ ! $selectedCategory ? 'filter-link--active' : '' }}"
           href="{{ route('catalog.index', array_merge(request()->except(['category', 'variety', 'page']), [])) }}">
            Semua produk
        </a>
        @foreach ($categories as $category)
            <a class="filter-link {{ $selectedCategory === $category->slug ? 'filter-link--active' : '' }}"
               href="{{ route('catalog.index', array_merge(request()->except(['variety', 'page']), ['category' => $category->slug])) }}">
                {{ $category->name }}
            </a>
        @endforeach
    </nav>

    @php
        $activeCategory = $selectedCategory
            ? $categories->firstWhere('slug', $selectedCategory)
            : null;
    @endphp

    @if ($activeCategory && $activeCategory->children->isNotEmpty())
        <nav class="filter-list filter-list--sub" aria-label="Filter varietas">
            <a class="filter-link {{ ! $selectedVariety ? 'filter-link--active' : '' }}"
               href="{{ route('catalog.index', array_merge(request()->except(['variety', 'page']), ['category' => $selectedCategory])) }}">
                Semua {{ $activeCategory->name }}
            </a>
            @foreach ($activeCategory->children as $child)
                <a class="filter-link {{ $selectedVariety === $child->slug ? 'filter-link--active' : '' }}"
                   href="{{ route('catalog.index', array_merge(request()->except('page'), ['category' => $selectedCategory, 'variety' => $child->slug])) }}">
                    {{ $child->name }}
                </a>
            @endforeach
        </nav>
    @endif

    @if ($keyword)
        <div class="flash flash--success mb-5" role="status">
            <span class="flash__icon"><x-customer.icon name="search" :size="17"></x-customer.icon></span>
            <div>Menampilkan hasil untuk <strong>“{{ $keyword }}”</strong>.</div>
        </div>
    @endif

    <div id="catalog-results">
        @if ($products->isEmpty())
            <x-customer.empty-state
                eyebrow="Belum ada yang cocok"
                title="Produk belum ditemukan"
                description="Coba kata kunci lain atau jelajahi semua kategori untuk menemukan produk yang Anda cari."
                action-label="Lihat semua produk"
                :action-url="route('catalog.index')"
                icon="search"
            ></x-customer.empty-state>
        @else
            <div class="product-grid">
                @foreach ($products as $product)
                    <x-customer.product-card :product="$product"></x-customer.product-card>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</section>

<div class="section-divider">
    <div class="order-steps">
        <div class="order-step order-step--active">
            <span class="order-step__number">1</span>
            <span class="order-step__label">Pilih produk</span>
        </div>
        <div class="order-step">
            <span class="order-step__number">2</span>
            <span class="order-step__label">Isi rencana pengambilan</span>
        </div>
        <div class="order-step">
            <span class="order-step__number">3</span>
            <span class="order-step__label">Ikuti prosesnya</span>
        </div>
    </div>
</div>

<script>
    // Memilih kategori/varietas adalah navigasi halaman penuh. Tanpa penanganan
    // tambahan, browser mengembalikan posisi gulir ke paling atas sehingga
    // pengguna kehilangan tempat ia berada di daftar produk.
    //
    // Posisi gulir disimpan sebelum keluar dan dipulihkan setelah halaman siap.
    (function () {
        var STORAGE_KEY = 'catalogScrollY';
        var anchor = document.getElementById('daftar-produk');

        function readPosition() {
            try {
                var raw = window.sessionStorage.getItem(STORAGE_KEY);
                return raw ? parseInt(raw, 10) : null;
            } catch (e) {
                return null;
            }
        }

        function savePosition() {
            try {
                window.sessionStorage.setItem(STORAGE_KEY, String(window.scrollY));
            } catch (e) {
                // abaikan bila penyimpanan tidak tersedia
            }
        }

        function clearPosition() {
            try {
                window.sessionStorage.removeItem(STORAGE_KEY);
            } catch (e) {
                // abaikan bila penyimpanan tidak tersedia
            }
        }

        // Simpan posisi sebelum meninggalkan halaman.
        window.addEventListener('pagehide', savePosition);
        window.addEventListener('beforeunload', savePosition);

        var savedY = readPosition();

        if (savedY !== null) {
            // Pemulihan gulir dimatikan otomatis karena kita yang menanganinya.
            if ('scrollRestoration' in window.history) {
                window.history.scrollRestoration = 'manual';
            }

            var restore = function () {
                window.scrollTo(0, savedY);

                if (anchor) {
                    var offset = anchor.getBoundingClientRect().top + window.scrollY - 90;
                    // Bila daftar produk became lebih pendek, jaga agar tidak keluar jalur.
                    window.scrollTo(0, Math.min(savedY, Math.max(0, offset + savedY)));
                }

                clearPosition();
            };

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(restore);
            });

            window.addEventListener('load', restore);
        }

        // Navigasi ke luar katalog tidak perlu memulihkan posisi.
        document.querySelectorAll('a[href]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                if (link.closest('.filter-list') || link.closest('.pagination')) {
                    return;
                }

                var url = link.getAttribute('href') || '';

                if (link.hostname && link.hostname !== window.location.hostname) {
                    clearPosition();
                    return;
                }

                if (url.indexOf('/katalog') !== 0) {
                    clearPosition();
                }

                if (link.classList.contains('filter-link') || event.metaKey || event.ctrlKey) {
                    return;
                }
            });
        });
    })();
</script>
@endsection