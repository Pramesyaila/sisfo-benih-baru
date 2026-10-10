@extends('layouts.app')

@section('title', $product->name)

@section('content')
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a class="breadcrumb__link" href="{{ route('catalog.index') }}">Katalog</a>
    <x-customer.icon name="chevron-right" :size="13"></x-customer.icon>
    <span class="breadcrumb__current">{{ $product->name }}</span>
</nav>

<section class="detail-layout">
    <div class="detail-visual">
        <x-customer.product-art :product="$product" size="large"></x-customer.product-art>
    </div>

    <div class="detail-copy">
        <span class="detail-category">{{ $product->category->name }}</span>
        <h1 class="detail-title">{{ $product->name }}</h1>
        <p class="detail-description">{{ $product->description ?: 'Pilihan benih atau bibit untuk membantu Anda memulai perhitungan yang baik.' }}</p>

        <div class="detail-price-row">
            <div>
                <div class="detail-price">{{ $product->formattedPrice() }}</div>
                <div class="detail-price__unit">per {{ $product->packaging_unit }}</div>
            </div>
            @if ($product->stock > 0)
                <span class="availability {{ $product->isLowStock() ? 'availability--low' : 'availability--good' }}">
                    {{ $product->isLowStock() ? 'Stok menipis' : 'Stok tersedia' }}
                </span>
            @else
                <span class="availability availability--empty">Stok habis</span>
            @endif
        </div>

        <div class="spec-grid">
            <div class="spec-item">
                <div class="spec-item__label">Kemasan</div>
                <div class="spec-item__value">{{ $product->packagingLabel() }}</div>
            </div>
            <div class="spec-item">
                <div class="spec-item__label">Ketersediaan</div>
                <div class="spec-item__value">{{ $product->stock }} {{ $product->packaging_unit }}</div>
            </div>
        </div>

        <div class="purchase-panel">
            @auth
                @if (auth()->user()->isKonsumen())
                    @if ($product->stock > 0)
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="purchase-form">
                            @csrf
                            <div class="quantity-control">
                                <label class="form-label" for="product-quantity">Jumlah</label>
                                <input id="product-quantity" class="form-input" type="number" name="qty" value="1" min="1" max="{{ $product->stock }}">
                            </div>
                            <button class="btn btn--primary" type="submit">
                                <x-customer.icon name="bag" :size="16"></x-customer.icon>
                                Tambah ke keranjang
                            </button>
                        </form>
                        <div class="detail-note">
                            <x-customer.icon name="info" :size="14"></x-customer.icon>
                            <span>Pesanan akan diproses oleh petugas layanan setelah Anda mengirim bukti pembayaran.</span>
                        </div>
                    @else
                        <div class="flash flash--error mb-0" role="status">
                            <span class="flash__icon"><x-customer.icon name="info" :size="17"></x-customer.icon></span>
                            <div>Stok sedang habis. Anda dapat menyimpan halaman ini dan kembali mengeceknya nanti.</div>
                        </div>
                    @endif
                @else
                    <div class="profile-note">Anda sedang melihat katalog sebagai petugas. Gunakan akun konsumen untuk menambahkan produk ke keranjang.</div>
                @endif
            @else
                <a class="btn btn--primary" href="{{ route('login') }}">
                    Masuk untuk memesan
                    <x-customer.icon name="arrow-right" :size="16"></x-customer.icon>
                </a>
                <div class="detail-note">
                    <x-customer.icon name="shield" :size="14"></x-customer.icon>
                    <span>Buat akun pelanggan untuk menyimpan pesanan dan melihat statusnya.</span>
                </div>
            @endauth
        </div>
    </div>
</section>

@if ($related->isNotEmpty())
    <section class="mt-12">
        <div class="catalog-toolbar">
            <div class="catalog-toolbar__heading">
                <span class="section-kicker">Temukan juga</span>
                <h2>Produk sejenis</h2>
            </div>
            <a class="text-link" href="{{ route('catalog.index', ['category' => $product->category->slug]) }}">
                Lihat kategori {{ $product->category->name }}
                <x-customer.icon name="arrow-right" :size="15"></x-customer.icon>
            </a>
        </div>
        <div class="product-grid product-grid--related">
            @foreach ($related as $relatedProduct)
                <x-customer.product-card :product="$relatedProduct"></x-customer.product-card>
            @endforeach
        </div>
    </section>
@endif
@endsection
