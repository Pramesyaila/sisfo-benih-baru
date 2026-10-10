@extends('layouts.app')

@section('title', 'Registrasi')

@section('content')
<div class="auth-shell">
    <section class="auth-card auth-card--wide">
        <p class="page-header__eyebrow">Pendaftaran pelanggan</p>
        <h1 class="page-header__title">Buat akun konsumen</h1>
        <p class="page-header__description mb-6">
            Data ini dipakai untuk menghubungi Anda, mencetak surat permohonan, dan menerbitkan faktur.
        </p>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="name">Nama lengkap</label>
                    <input class="form-input" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="nik">Nomor KTP</label>
                    <input class="form-input" id="nik" type="text" name="nik" value="{{ old('nik') }}" inputmode="numeric">
                    @error('nik')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label" for="instansi">Instansi / Kelompok tani</label>
                <input class="form-input" id="instansi" type="text" name="instansi" value="{{ old('instansi') }}" placeholder="Kosongkan bila transaksi pribadi">
                @error('instansi')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label" for="alamat">Alamat (jalan)</label>
                <textarea class="form-input" id="alamat" name="alamat" rows="2">{{ old('alamat') }}</textarea>
                @error('alamat')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                @foreach (\App\Http\Controllers\ProfileController::addressFields() as $field)
                    <div>
                        <label class="form-label" for="{{ $field['name'] }}">{{ $field['label'] }}</label>
                        <input
                            class="form-input"
                            id="{{ $field['name'] }}"
                            type="text"
                            name="{{ $field['name'] }}"
                            value="{{ old($field['name']) }}"
                            placeholder="{{ $field['placeholder'] }}"
                        >
                        @error($field['name'])<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="email">Email</label>
                    <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="phone">Nomor HP</label>
                    <input class="form-input" id="phone" type="text" name="phone" value="{{ old('phone') }}" inputmode="numeric">
                    @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label" for="whatsapp">Nomor WhatsApp <span class="text-xs text-[var(--muted)]">(opsional)</span></label>
                <input class="form-input" id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp') }}" inputmode="numeric">
                @error('whatsapp')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label" for="password">Kata sandi</label>
                    <input class="form-input" id="password" type="password" name="password" required>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="password_confirmation">Ulangi kata sandi</label>
                    <input class="form-input" id="password_confirmation" type="password" name="password_confirmation" required>
                </div>
            </div>

            <button class="btn btn--primary w-full justify-center" type="submit">
                Daftar sekarang
                <x-customer.icon name="arrow-right" :size="16"></x-customer.icon>
            </button>
        </form>

        <p class="auth-footer mt-6">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </p>
    </section>
</div>
@endsection