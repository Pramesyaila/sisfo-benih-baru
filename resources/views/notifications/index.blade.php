@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a class="breadcrumb__link" href="{{ route('catalog.index') }}">Katalog</a>
    <x-customer.icon name="chevron-right" :size="13"></x-customer.icon>
    <span class="breadcrumb__current">Notifikasi</span>
</nav>

<div class="page-header">
    <span class="page-header__eyebrow">Pemberitahuan</span>
    <h1 class="page-header__title">Notifikasi</h1>
    <p class="page-header__description">
        Satu pesanan ditampilkan satu notifikasi, berapa pun jumlah produk di dalamnya.
    </p>
</div>

<section class="surface overflow-hidden">
    @forelse ($groups as $notifications)
        @php
            $latest = $notifications->first();
            $unread = $notifications->contains(fn ($notification) => $notification->read_at === null);
        @endphp

        <div class="notification-list__item {{ $unread ? 'notification-list__item--unread' : '' }}">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="notification-list__title">{{ $latest->data['title'] ?? 'Notifikasi' }}</p>
                    <p class="text-xs text-[var(--ink-soft)] mt-1">{{ $latest->data['message'] ?? '' }}</p>
                </div>

                <form action="{{ route('notifications.read', $latest->id) }}" method="POST" class="shrink-0">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn--secondary btn--small" type="submit">
                        {{ $unread ? 'Tandai dibaca' : 'Buka' }}
                    </button>
                </form>
            </div>

            <p class="notification-list__meta">
                {{ $latest->created_at->diffForHumans() }}
                @if ($notifications->count() > 1)
                    &middot; {{ $notifications->count() }} pembaruan untuk pesanan ini
                @endif
            </p>
        </div>
    @empty
        <div class="p-10 text-center">
            <x-customer.empty-state
                eyebrow="Belum ada"
                title="Tidak ada notifikasi"
                description="Pemberitahuan tentang billing, faktur, dan status pesanan akan muncul di sini."
                action-label="Kembali ke katalog"
                :action-url="route('catalog.index')"
                icon="bell"
            ></x-customer.empty-state>
        </div>
    @endforelse
</section>
@endsection