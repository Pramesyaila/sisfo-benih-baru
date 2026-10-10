@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="page-header">
    <span class="page-header__eyebrow">Langkah 2 dari 3</span>
    <h1 class="page-header__title">Keranjang Anda</h1>
    <p class="page-header__description">Periksa kembali pilihan Anda sebelum melanjutkan ke proses pemesanan.</p>
</div>

<div class="order-steps" aria-label="Tahapan pemesanan">
    <div class="order-step order-step--done">
        <span class="order-step__number"><x-customer.icon name="check" :size="13"></x-customer.icon></span>
        <span class="order-step__label">Pilih produk</span>
    </div>
    <div class="order-step order-step--active">
        <span class="order-step__number">2</span>
        <span class="order-step__label">Keranjang</span>
    </div>
    <div class="order-step">
        <span class="order-step__number">3</span>
        <span class="order-step__label">Pesan</span>
    </div>
</div>

@if ($cart->isEmpty())
    <x-customer.empty-state
        eyebrow="Belum ada produk"
        title="Keranjang masih kosong"
        description="Mulai dari katalog dan pilih benih atau bibit yang ingin Anda pesan."
        action-label="Jelajahi katalog"
        :action-url="route('catalog.index')"
        icon="bag"
    ></x-customer.empty-state>
@else
    <div class="cart-layout">
        <section class="surface cart-list" aria-label="Produk dalam keranjang">
            @foreach ($cart as $row)
                <div class="cart-row">
                    <div class="cart-row__art">
                        <x-customer.product-art :product="$row['product']" size="compact"></x-customer.product-art>
                    </div>
                    <div class="cart-row__content">
                        <p class="cart-row__name">{{ $row['product']->name }}</p>
                        <p class="cart-row__meta">{{ $row['product']->packagingLabel() }} &middot; {{ $row['product']->formattedPrice() }}</p>
                    </div>
                    <form action="{{ route('cart.update', $row['product']) }}" method="POST" class="cart-row__quantity">
                        @csrf
                        @method('PATCH')
                        <label class="sr-only" for="cart-quantity-{{ $row['product']->id }}">Jumlah {{ $row['product']->name }}</label>
                        <input id="cart-quantity-{{ $row['product']->id }}" class="form-input" type="number" name="qty" value="{{ $row['qty'] }}" min="1" max="{{ $row['product']->stock }}">
                        <button class="btn btn--secondary btn--small" type="submit">Ubah</button>
                    </form>
                    <div class="cart-row__subtotal">Rp{{ number_format($row['subtotal'], 0, ',', '.') }}</div>
                    <form action="{{ route('cart.remove', $row['product']) }}" method="POST" class="cart-row__remove-form">
                        @csrf
                        @method('DELETE')
                        <button class="cart-row__remove" type="submit">Hapus</button>
                    </form>
                </div>
            @endforeach
        </section>

        <aside class="surface summary-card">
            <h2 class="summary-card__title">Ringkasan belanja</h2>
            <div class="summary-row">
                <span>Produk dipilih</span>
                <strong>{{ $cart->count() }} jenis</strong>
            </div>
            <div class="summary-row">
                <span>Total item</span>
                <strong>{{ $cart->sum('qty') }} item</strong>
            </div>
            <div class="summary-row summary-total">
                <span>Total</span>
                <strong>Rp{{ number_format($cart->sum('subtotal'), 0, ',', '.') }}</strong>
            </div>
            <a class="btn btn--primary w-full mt-5" href="{{ route('checkout.index') }}">
                Lanjut ke pemesanan
                <x-customer.icon name="arrow-right" :size="16"></x-customer.icon>
            </a>
            <p class="summary-note">Pesanan akan dibuat setelah Anda mengisi catatan pada langkah pemesanan. Stok dan proses pembayaran akan dikonfirmasi oleh petugas.</p>
        </aside>
    </div>
@endif
@endsection
