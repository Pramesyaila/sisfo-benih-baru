@extends('layouts.app')

@section('title', 'Checkout')

@section('content')

<div class="page-header">
    <span class="page-header__eyebrow">Langkah 3 dari 3</span>
    <h1 class="page-header__title">Lakukan pesanan</h1>
    <p class="page-header__description">
        Lengkapi data pemesanan, lalu buat pesanan untuk memulai proses administrasi.
    </p>
</div>

<div class="order-steps" aria-label="Tahapan pemesanan">
    <div class="order-step order-step--done">
        <span class="order-step__number">
            <x-customer.icon name="check" :size="13"></x-customer.icon>
        </span>
        <span class="order-step__label">Pilih produk</span>
    </div>

<div class="order-step order-step--done">
    <span class="order-step__number">
        <x-customer.icon name="check" :size="13"></x-customer.icon>
    </span>
    <span class="order-step__label">Keranjang</span>
</div>

<div class="order-step order-step--active">
    <span class="order-step__number">3</span>
    <span class="order-step__label">Pesan</span>
</div>

</div>

<div class="content-layout">
    <section class="space-y-6">
        {{-- Formulir wajib diisi sebelum pesanan dibuat. --}}
        <div class="surface">
            <div class="surface__header">
                <h2 class="surface__title">Rencana penggunaan dan pengambilan</h2>
                <p class="surface__subtitle">
                    Tiga data berikut wajib diisi sebelum pesanan dapat dibuat.
                </p>
            </div>

            <form action="{{ route('checkout.store') }}" method="POST" class="checkout-form p-5 md:p-6">
                @csrf

                <div class="checkout-required">
                    <div>
                        <label class="form-label" for="checkout-notes">
                            Tujuan penggunaan <span class="required-mark">wajib</span>
                        </label>
                        <textarea
                            id="checkout-notes"
                            class="form-textarea"
                            name="notes"
                            rows="3"
                            maxlength="1000"
                            placeholder="Contoh: untuk kebutuhan tanam musim ini seluas 1 hektar"
                            required
                        >{{ old('notes') }}</textarea>
                        <p class="form-hint">Jelaskan tujuan penggunaan benih/bibit yang dipesan.</p>
                        @error('notes')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="checkout-required__grid">
                        <div>
                            <label class="form-label" for="checkout-pickup-date">
                                Tanggal pengambilan <span class="required-mark">wajib</span>
                            </label>
                            <input
                                id="checkout-pickup-date"
                                type="date"
                                name="pickup_date"
                                min="{{ now()->format('Y-m-d') }}"
                                value="{{ old('pickup_date') }}"
                                class="form-input"
                                required
                            >
                            @error('pickup_date')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label" for="checkout-pickup-location">
                                Lokasi pengambilan <span class="required-mark">wajib</span>
                            </label>
                            <select
                                id="checkout-pickup-location"
                                name="pickup_location"
                                class="form-input"
                                required
                            >
                                <option value="">Pilih lokasi pengambilan</option>
                                @foreach ($pickupLocations as $value => $label)
                                    <option value="{{ $value }}" @selected(old('pickup_location') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('pickup_location')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <details class="checkout-optional">
                    <summary>Data identitas pemohon (untuk surat permohonan dan faktur)</summary>
                    <p class="form-hint mt-2">
                        Data ini disimpan ke profil Anda agar surat permohonan dan faktur terisi otomatis.
                    </p>

                    <div class="checkout-required mt-4">
                        <div class="checkout-required__grid">
                            <div>
                                <label class="form-label" for="checkout-nik">Nomor KTP</label>
                                <input id="checkout-nik" type="text" name="nik" value="{{ old('nik', auth()->user()->nik) }}" class="form-input">
                                @error('nik')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="form-label" for="checkout-instansi">Instansi / Kelompok tani</label>
                                <input id="checkout-instansi" type="text" name="instansi" value="{{ old('instansi', auth()->user()->instansi) }}" class="form-input">
                                @error('instansi')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="checkout-alamat">Alamat (jalan)</label>
                            <textarea id="checkout-alamat" name="alamat" rows="2" class="form-textarea">{{ old('alamat', auth()->user()->alamat) }}</textarea>
                            @error('alamat')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="checkout-required__grid">
                            @foreach (\App\Http\Controllers\ProfileController::addressFields() as $field)
                                <div>
                                    <label class="form-label" for="checkout-{{ $field['name'] }}">{{ $field['label'] }}</label>
                                    <input
                                        id="checkout-{{ $field['name'] }}"
                                        type="text"
                                        name="{{ $field['name'] }}"
                                        value="{{ old($field['name'], auth()->user()->{$field['name']}) }}"
                                        placeholder="{{ $field['placeholder'] }}"
                                        class="form-input"
                                    >
                                    @error($field['name'])<p class="form-error">{{ $message }}</p>@enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="checkout-required__grid">
                            <div>
                                <label class="form-label" for="checkout-phone">Nomor HP</label>
                                <input id="checkout-phone" type="text" name="phone" inputmode="numeric" value="{{ old('phone', auth()->user()->phone) }}" class="form-input">
                                @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="form-label" for="checkout-whatsapp">Nomor WhatsApp</label>
                                <input id="checkout-whatsapp" type="text" name="whatsapp" inputmode="numeric" value="{{ old('whatsapp', auth()->user()->whatsapp) }}" class="form-input">
                                @error('whatsapp')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </details>

                <div class="mt-6">
                    <button class="btn btn--primary w-full" type="submit">
                        Buat pesanan
                        <x-customer.icon name="arrow-right" :size="16"></x-customer.icon>
                    </button>
                </div>
            </form>
        </div>

        <div class="surface cart-list" aria-label="Ringkasan produk">
        <div class="surface__header">
            <h2 class="surface__title">Produk yang akan dipesan</h2>
            <p class="surface__subtitle">
                Harga dan jumlah mengikuti pilihan di keranjang Anda.
            </p>
        </div>

    @foreach ($cart as $row)
        <div class="cart-row">
            <div class="cart-row__art">
                <x-customer.product-art
                    :product="$row['product']"
                    size="compact"
                ></x-customer.product-art>
            </div>

            <div class="cart-row__content">
                <p class="cart-row__name">
                    {{ $row['product']->name }}
                </p>

                <p class="cart-row__meta">
                    {{ $row['qty'] }}
                    {{ $row['product']->packaging_unit }}
                    &times;
                    {{ $row['product']->formattedPrice() }}
                </p>
            </div>

            <div class="cart-row__spacer"></div>

            <div class="cart-row__subtotal">
                Rp{{ number_format($row['subtotal'], 0, ',', '.') }}
            </div>
        </div>
    @endforeach

    <div class="flex items-center justify-between px-4 py-4 bg-[var(--surface-muted)]">
        <span class="text-xs font-semibold text-[var(--muted)]">
            Total item
        </span>

        <span class="text-sm font-extrabold text-[var(--forest-900)]">
            {{ $cart->sum('qty') }} item
        </span>
    </div>
</section>

@php $user = auth()->user(); @endphp
    </section>

    <aside class="surface summary-card">
    <h2 class="summary-card__title">Detail pemesanan</h2>

    <div class="summary-row">
        <span>Subtotal</span>
        <strong>
            Rp{{ number_format($total, 0, ',', '.') }}
        </strong>
    </div>

    <div class="summary-row summary-total">
        <span>Total pesanan</span>
        <strong>
            Rp{{ number_format($total, 0, ',', '.') }}
        </strong>
    </div>

    <div class="summary-note">
        <div class="flex items-start gap-2">
            <x-customer.icon
                name="info"
                :size="15"
            ></x-customer.icon>

            <span>
                Setelah pesanan dibuat, petugas layanan akan memeriksa
                ketersediaan benih lalu menerbitkan billing. Bayar sesuai
                billing, unggah bukti pembayaran, dan ambil benih Anda di
                lokasi pengambilan yang dipilih.
            </span>
        </div>
    </div>
</aside>

</div>
@endsection
