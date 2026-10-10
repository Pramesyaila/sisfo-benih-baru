@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="auth-layout">
    <aside class="auth-aside">
        <div>
            <span class="auth-aside__eyebrow">Selamat datang kembali</span>
            <h1 class="auth-aside__title">Mari lanjutkan<br>perjalanan tumbuh.</h1>
            <p class="auth-aside__copy">Masuk untuk memilih benih, membuat pesanan, dan mengikuti prosesnya dari satu akun.</p>
        </div>
        <div class="auth-aside__art">
            <x-customer.icon name="sprout" :size="96"></x-customer.icon>
            <small>benih · bibit · peluang tumbuh</small>
        </div>
    </aside>

    <section class="auth-card">
        <div class="auth-card__header">
            <span class="auth-card__eyebrow">Akun pelanggan</span>
            <h2 class="auth-card__title">Masuk ke akun Anda</h2>
            <p class="auth-card__description">Gunakan email dan kata sandi yang terdaftar untuk melanjutkan.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf
            <div>
                <label class="form-label" for="login-email">Email</label>
                <input id="login-email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@email.com">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="form-label" for="login-password">Kata sandi</label>
                <input id="login-password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="auth-form__row">
                <label class="check-label"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                <span>Butuh bantuan? Hubungi petugas.</span>
            </div>
            <button class="btn btn--primary w-full" type="submit">
                Masuk ke akun
                <x-customer.icon name="arrow-right" :size="16"></x-customer.icon>
            </button>
        </form>

        <p class="auth-footer">Belum punya akun pelanggan? <a href="{{ route('register') }}">Daftar sekarang</a></p>

        @if (app()->environment('local'))
            <div class="credential-note">
                <strong>Akun demo lokal:</strong><br>
                Konsumen: konsumen@benih.test / password<br>
                Petugas Layanan: layanan@benih.test / password<br>
                Petugas Gudang: gudang@benih.test / password
            </div>
        @endif
    </section>
</div>
@endsection
