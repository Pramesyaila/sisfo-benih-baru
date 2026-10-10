@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
@php
    $statusTone = match ($order->status) {
        'dibatalkan', 'pembayaran_ditolak' => 'red',
        'menunggu_pembayaran', 'menunggu_verifikasi' => 'gold',
        'siap_diambil', 'selesai' => 'green',
        default => 'blue',
    };
    $steps = ['dipesan', 'diproses', 'menunggu_pembayaran', 'menunggu_verifikasi', 'siap_diambil', 'selesai'];
    $currentStep = array_search($order->status, $steps, true);
@endphp

<nav class="breadcrumb" aria-label="Breadcrumb">
    <a class="breadcrumb__link" href="{{ route('orders.index') }}">Pesanan saya</a>
    <x-customer.icon name="chevron-right" :size="13"></x-customer.icon>
    <span class="breadcrumb__current">{{ $order->order_number }}</span>
</nav>

<div class="page-header flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <span class="page-header__eyebrow">Rincian pesanan</span>
        <h1 class="page-header__title">{{ $order->order_number }}</h1>
        <p class="page-header__description">Dibuat {{ $order->created_at->translatedFormat('d M Y, H:i') }} &middot; {{ $order->items->count() }} jenis produk</p>
    </div>
    <div class="flex items-center gap-3">
        <x-customer.status-pill :label="\App\Models\Order::statusLabel($order->status)" :tone="$statusTone"></x-customer.status-pill>
        <span class="text-lg font-extrabold tracking-tight text-[var(--forest-900)]">{{ $order->formattedTotal() }}</span>
    </div>
</div>

@if (in_array($order->status, ['menunggu_pembayaran', 'pembayaran_ditolak'], true) && $order->billing)
    <div class="flash flash--validation" role="status">
        <span class="flash__icon"><x-customer.icon name="wallet" :size="17"></x-customer.icon></span>
        <div>
            <strong>Ada langkah yang perlu Anda lakukan.</strong>
            <div>Unduh billing di bawah, lakukan pembayaran, lalu unggah bukti pembayaran agar petugas dapat memverifikasi pesanan Anda.</div>
        </div>
    </div>
@elseif ($order->status === 'siap_diambil')
    <div class="flash flash--success" role="status">
        <span class="flash__icon"><x-customer.icon name="check" :size="17"></x-customer.icon></span>
        <div>Pembayaran telah diverifikasi. Pesanan Anda siap diambil di lokasi pengambilan yang telah dipilih.</div>
    </div>
@elseif ($order->status === 'selesai')
    <div class="flash flash--success" role="status">
        <span class="flash__icon"><x-customer.icon name="check" :size="17"></x-customer.icon></span>
        <div>Pengambilan telah selesai. Bukti penyelesaian dapat Anda lihat di samping halaman ini.</div>
    </div>
@elseif ($order->status === 'dibatalkan')
    <div class="flash flash--error" role="status">
        <span class="flash__icon"><x-customer.icon name="info" :size="17"></x-customer.icon></span>
        <div>Pesanan ini berstatus dibatalkan. Hubungi petugas jika Anda membutuhkan informasi lebih lanjut.</div>
    </div>
@endif

<div class="content-layout">
    <div class="content-main">
        <section class="surface overflow-hidden">
            <div class="surface__header">
                <h2 class="surface__title">Produk dalam pesanan</h2>
                <p class="surface__subtitle">Rincian harga tetap menggunakan nilai saat pesanan dibuat.</p>
            </div>
            <div class="divide-y divide-[var(--line)]">
                @foreach ($order->items as $item)
                    <div class="cart-row">
                        <div class="cart-row__art">
                            @if ($item->product)
                                <x-customer.product-art :product="$item->product" size="compact"></x-customer.product-art>
                            @else
                                <div class="product-art product-art--compact"><div class="product-art__placeholder"><x-customer.icon name="leaf" :size="30"></x-customer.icon></div></div>
                            @endif
                        </div>
                        <div class="cart-row__content">
                            <p class="cart-row__name">{{ $item->product_name }}</p>
                            <p class="cart-row__meta">{{ $item->qty }} {{ $item->packaging }} &times; {{ $item->formattedPrice() }}</p>
                        </div>
                        <div class="cart-row__spacer"></div>
                        <div class="cart-row__subtotal">{{ $item->formattedSubtotal() }}</div>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between px-4 py-4 bg-[var(--surface-muted)]">
                <span class="text-xs font-semibold text-[var(--muted)]">Total pesanan</span>
                <strong class="text-lg tracking-tight text-[var(--forest-900)]">{{ $order->formattedTotal() }}</strong>
            </div>
        </section>

        @if ($order->billing)
            <section class="surface p-5 md:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <span class="section-kicker">Dokumen pembayaran</span>
                        <h2 class="surface__title">Billing</h2>
                        <p class="surface__subtitle mt-1">Nomor {{ $order->billing->bill_number }}</p>
                    </div>
                    <a
                        class="btn btn--primary btn--small"
                        href="{{ asset('storage/' . $order->billing->file_path) }}"
                        target="_blank"
                        rel="noopener"
                    >
                        <x-customer.icon name="download" :size="15"></x-customer.icon>
                        Unduh billing
                    </a>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 mt-5">
                    <div class="spec-item"><div class="spec-item__label">Jumlah tagihan</div><div class="spec-item__value">{{ $order->billing->formattedAmount() }}</div></div>
                    <div class="spec-item"><div class="spec-item__label">Tanggal dikirim</div><div class="spec-item__value">{{ optional($order->billing->sent_at)->format('d M Y') ?: '-' }}</div></div>
                    <div class="spec-item"><div class="spec-item__label">Status pembayaran</div><div class="spec-item__value">{{ \App\Models\Order::statusLabel($order->status) }}</div></div>
                </div>

                @if ($order->billing->notes)
                    <p class="form-hint mt-4">{{ $order->billing->notes }}</p>
                @endif

                @if (in_array($order->status, ['menunggu_pembayaran', 'pembayaran_ditolak'], true))
                    <form action="{{ route('orders.uploadProof', $order) }}" method="POST" enctype="multipart/form-data" class="mt-5">
                        @csrf
                        <label class="form-label" for="payment-proof">Unggah bukti pembayaran</label>
                        <div class="upload-zone">
                            <x-customer.icon name="upload" :size="18"></x-customer.icon>
                            <input id="payment-proof" class="file-input" type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <p class="form-hint">Format JPG, PNG, atau PDF. Maksimal 4 MB.</p>
                        @error('file')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <button class="btn btn--primary mt-3" type="submit">
                            <x-customer.icon name="upload" :size="16"></x-customer.icon>
                            Kirim bukti pembayaran
                        </button>
                    </form>
                @elseif ($order->paymentProofs->isNotEmpty())
                    <p class="form-hint mt-5">Bukti pembayaran Anda sudah diterima dan menunggu keputusan petugas.</p>
                @endif
            </section>
        @elseif (in_array($order->status, ['menunggu_pembayaran', 'pembayaran_ditolak'], true))
            <section class="surface p-5 md:p-6">
                <span class="section-kicker">Dokumen pembayaran</span>
                <h2 class="surface__title">Billing belum tersedia</h2>
                <p class="surface__subtitle mt-1">Petugas layanan sedang menyiapkan dokumen billing untuk pesanan Anda.</p>
            </section>
        @endif

        @if ($order->invoice)
            <section class="surface p-5 md:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <span class="section-kicker">Dokumen penjualan</span>
                        <h2 class="surface__title">Faktur</h2>
                        <p class="surface__subtitle mt-1">{{ $order->invoice->invoice_number }}</p>
                    </div>
                    <a
                        class="btn btn--primary btn--small"
                        href="{{ route('orders.invoice', $order) }}"
                        target="_blank"
                        rel="noopener"
                    >
                        <x-customer.icon name="download" :size="15"></x-customer.icon>
                        Unduh / cetak faktur
                    </a>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 mt-5">
                    <div class="spec-item">
                        <div class="spec-item__label">Total faktur</div>
                        <div class="spec-item__value">{{ $order->invoice->formattedTotal() }}</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-item__label">Tanggal pengambilan</div>
                        <div class="spec-item__value">{{ optional($order->invoice->pickup_date)->translatedFormat('d M Y') ?: '-' }}</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-item__label">Lokasi pengambilan</div>
                        <div class="spec-item__value">{{ $order->invoice->pickupLocationLabel() }}</div>
                    </div>
                </div>
            </section>
        @endif

        @if ($order->paymentProofs->isNotEmpty())
            <section class="surface overflow-hidden">
                <div class="surface__header">
                    <h2 class="surface__title">Riwayat bukti pembayaran</h2>
                    <p class="surface__subtitle">Semua berkas yang pernah Anda unggah.</p>
                </div>
                <div class="divide-y divide-[var(--line)]">
                    @foreach ($order->paymentProofs as $proof)
                        @php
                            $proofTone = match ($proof->status) {
                                'valid' => 'green',
                                'ditolak' => 'red',
                                default => 'gold',
                            };
                        @endphp
                        <div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="trust-item__icon">
                                    <x-customer.icon name="file" :size="18"></x-customer.icon>
                                </span>
                                <div>
                                    <a
                                        class="text-xs font-extrabold text-[var(--forest-800)] no-underline hover:underline"
                                        href="{{ asset('storage/' . $proof->file_path) }}"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        Lihat berkas bukti bayar
                                    </a>
                                    <p class="mt-1 text-[10px] text-[var(--muted)]">
                                        Diunggah {{ $proof->created_at->translatedFormat('d M Y, H:i') }}
                                        @if ($proof->note)
                                            &middot; Catatan: {{ $proof->note }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <x-customer.status-pill :label="ucfirst($proof->status)" :tone="$proofTone"></x-customer.status-pill>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <aside class="content-aside">
        <section class="surface p-5 md:p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <span class="section-kicker">Perjalanan pesanan</span>
                    <h2 class="surface__title">Status pesanan</h2>
                </div>
                <x-customer.icon name="clock" :size="19" class="text-[var(--forest-700)]"></x-customer.icon>
            </div>
            <ol class="mt-5 space-y-4">
                @foreach ($steps as $index => $step)
                    @php
                        $isDone = $currentStep !== false && $index < $currentStep;
                        $isActive = $order->status === $step;
                    @endphp
                    <li class="relative flex gap-3">
                        @if (! $loop->last)
                            <span class="absolute left-[11px] top-6 h-[calc(100%+4px)] w-px {{ $isDone ? 'bg-[var(--gold-300)]' : 'bg-[var(--line)]' }}"></span>
                        @endif
                        <span class="relative z-[1] flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border text-[10px] font-extrabold {{ $isDone ? 'border-[var(--gold-300)] bg-[var(--gold-300)] text-[var(--forest-950)]' : ($isActive ? 'border-[var(--forest-800)] bg-[var(--forest-800)] text-white' : 'border-[var(--line-strong)] bg-[var(--surface)] text-[var(--muted)]') }}">
                            @if ($isDone)
                                <x-customer.icon name="check" :size="12"></x-customer.icon>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </span>
                        <span class="pt-1 text-[11px] {{ $isActive ? 'font-extrabold text-[var(--forest-800)]' : ($isDone ? 'font-semibold text-[var(--ink-soft)]' : 'text-[var(--muted)]') }}">{{ \App\Models\Order::statusLabel($step) }}</span>
                    </li>
                @endforeach
            </ol>
            <div class="mt-5 space-y-2 border-t border-[var(--line)] pt-4 text-[11px] text-[var(--muted)]">
                <div class="flex justify-between gap-3"><span>Jadwal pengambilan</span><strong class="text-[var(--ink-soft)]">{{ optional($order->pickup_date)->translatedFormat('d M Y') ?: '-' }}</strong></div>
                <div class="flex justify-between gap-3"><span>Lokasi</span><strong class="text-[var(--ink-soft)]">{{ $order->pickupLocationLabel() }}</strong></div>
            </div>
        </section>

        @if ($order->completionReceipt)
            <section class="surface p-5 md:p-6">
                <div class="flex items-center gap-3">
                    <span class="trust-item__icon"><x-customer.icon name="check" :size="19"></x-customer.icon></span>
                    <div>
                        <h2 class="surface__title">Bukti penyelesaian</h2>
                        <p class="surface__subtitle mt-1">{{ $order->completionReceipt->receipt_number }}</p>
                    </div>
                </div>
                <p class="form-hint mt-4">Diunggah {{ optional($order->completionReceipt->completed_at)->translatedFormat('d M Y, H:i') }}</p>
                <a
                    class="btn btn--secondary w-full mt-4"
                    href="{{ asset('storage/' . $order->completionReceipt->file_path) }}"
                    target="_blank"
                    rel="noopener"
                >
                    <x-customer.icon name="download" :size="15"></x-customer.icon>
                    Lihat faktur selesai
                </a>
            </section>
        @endif

        <section class="profile-note">
            <strong class="block text-[var(--forest-900)] mb-1">Butuh bantuan?</strong>
            Hubungi petugas layanan melalui kanal resmi instansi untuk informasi billing dan status pembayaran.
        </section>
    </aside>
</div>
@endsection