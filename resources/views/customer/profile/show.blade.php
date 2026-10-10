@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="page-header">
    <span class="page-header__eyebrow">Akun pelanggan</span>
    <h1 class="page-header__title">Profil saya</h1>
    <p class="page-header__description">
        Data di bawah ini sama dengan data yang Anda isi saat pendaftaran.
        Data ini dipakai untuk menghubungi Anda, mencetak surat permohonan, dan menerbitkan faktur.
    </p>
</div>

<section class="profile-hero">
    <div class="profile-hero__identity">
        @if ($user->profile_photo)
            <img class="profile-avatar !rounded-[18px] !border-0 object-cover" src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto profil {{ $user->name }}">
        @else
            <span class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        @endif
        <div class="profile-hero__copy">
            <h2 class="profile-hero__name">{{ $user->name }}</h2>
            <p class="profile-hero__meta">{{ $user->email }} &middot; {{ $user->roleDisplayLabel() }}</p>
        </div>
    </div>
    <a class="btn btn--secondary" href="{{ route('profile.edit') }}">
        <x-customer.icon name="user" :size="15"></x-customer.icon>
        Edit profil
    </a>
</section>

<div class="profile-grid">
    <section class="surface profile-section">
        <div class="flex items-center justify-between gap-3">
            <div>
                <span class="section-kicker">Data pendaftaran</span>
                <h2 class="surface__title">Identitas pemohon</h2>
            </div>
            <x-customer.icon name="user" :size="19" class="text-[var(--forest-700)]"></x-customer.icon>
        </div>

        <div class="profile-list mt-4">
            @foreach ([
                'Nama lengkap' => $user->name,
                'Email' => $user->email,
                'Nomor KTP' => $user->nik,
                'Instansi / Kelompok tani' => $user->instansi,
                'Nomor HP' => $user->phone,
                'Nomor WhatsApp' => $user->whatsapp,
            ] as $label => $value)
                <div class="profile-row">
                    <span class="profile-row__label">{{ $label }}</span>
                    <span class="profile-row__value">{{ $value ?: 'Belum diisi' }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="surface profile-section">
        <div class="flex items-center justify-between gap-3">
            <div>
                <span class="section-kicker">Data pendaftaran</span>
                <h2 class="surface__title">Alamat</h2>
            </div>
            <x-customer.icon name="map-pin" :size="19" class="text-[var(--forest-700)]"></x-customer.icon>
        </div>

        <div class="profile-list mt-4">
            @foreach ([
                'Alamat' => $user->alamat,
                'Kelurahan/Desa' => $user->kelurahan,
                'Kecamatan' => $user->kecamatan,
                'Kabupaten/Kota' => $user->kabupaten_kota,
                'Provinsi' => $user->provinsi,
            ] as $label => $value)
                <div class="profile-row">
                    <span class="profile-row__label">{{ $label }}</span>
                    <span class="profile-row__value">{{ $value ?: 'Belum diisi' }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <aside class="profile-actions">
        <section class="surface profile-section">
            <span class="section-kicker">Akses cepat</span>
            <h2 class="surface__title">Lanjutkan aktivitas</h2>
            <p class="surface__subtitle mt-1">Kelola pesanan atau cari produk baru.</p>
            <div class="mt-4 grid gap-2">
                <a class="btn btn--primary w-full justify-start" href="{{ route('orders.index') }}">
                    <x-customer.icon name="clipboard" :size="16"></x-customer.icon>
                    Lihat pesanan saya
                </a>
                <a class="btn btn--secondary w-full justify-start" href="{{ route('catalog.index') }}">
                    <x-customer.icon name="leaf" :size="16"></x-customer.icon>
                    Jelajahi katalog
                </a>
            </div>
        </section>
        <div class="profile-note">
            <strong class="block text-[var(--forest-900)] mb-1">Status akun</strong>
            Akun Anda berstatus <strong>{{ $user->is_active ? 'aktif' : 'tidak aktif' }}</strong>. Gunakan data yang valid agar proses pesanan dapat berjalan dengan jelas.
        </div>
    </aside>
</div>
@endsection