@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="page-header">
    <span class="page-header__eyebrow">Ruang pelanggan</span>
    <h1 class="page-header__title">Pesanan saya</h1>
    <p class="page-header__description">Pantau status pesanan, lihat dokumen, dan kembali ke katalog ketika Anda ingin memesan lagi.</p>
    <div class="page-header__actions">
        <a class="btn btn--primary" href="{{ route('catalog.index') }}">
            <x-customer.icon name="leaf" :size="16"></x-customer.icon>
            Jelajahi katalog
        </a>
    </div>
</div>

@if ($orders->isEmpty())
    <x-customer.empty-state
        eyebrow="Belum ada pesanan"
        title="Riwayat pesanan masih kosong"
        description="Pesanan yang Anda buat akan tampil di sini lengkap dengan status dan dokumen pembayarannya."
        action-label="Mulai memilih produk"
        :action-url="route('catalog.index')"
        icon="clipboard"
    ></x-customer.empty-state>
@else
    <section class="surface overflow-hidden" aria-label="Daftar pesanan">
        <div class="surface__header flex items-center justify-between gap-3">
            <div>
                <h2 class="surface__title">Semua pesanan</h2>
                <p class="surface__subtitle">Klik pesanan untuk melihat rincian dan aksi yang tersedia.</p>
            </div>
            <span class="text-xs font-semibold text-[var(--muted)]">{{ $orders->total() }} pesanan</span>
        </div>
        <div class="order-list">
            @foreach ($orders as $order)
                @php
                    $tone = match ($order->status) {
                        'dibatalkan', 'pembayaran_ditolak' => 'red',
                        'menunggu_pembayaran', 'menunggu_verifikasi' => 'gold',
                        'selesai', 'siap_diambil' => 'green',
                        default => 'blue',
                    };
                @endphp
                <a class="order-list__item" href="{{ route('orders.show', $order) }}">
                    <div class="order-list__top">
                        <div>
                            <span class="order-list__label">Nomor pesanan</span>
                            <strong class="order-list__number">{{ $order->order_number }}</strong>
                            @if ($order->billing)
                                <span class="order-list__label mt-1 block">Billing {{ $order->billing->bill_number }} tersedia</span>
                            @endif
                        </div>
                        <x-customer.status-pill :label="\App\Models\Order::statusLabel($order->status)" :tone="$tone"></x-customer.status-pill>
                    </div>
                    <div class="order-list__bottom">
                        <span><x-customer.icon name="calendar" :size="14"></x-customer.icon>{{ $order->created_at->translatedFormat('d M Y') }}</span>
                        <strong>{{ $order->formattedTotal() }}</strong>
                        <span class="order-list__action">Lihat detail <x-customer.icon name="arrow-right" :size="14"></x-customer.icon></span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    <div class="mt-5">
        {{ $orders->links() }}
    </div>
@endif
@endsection
