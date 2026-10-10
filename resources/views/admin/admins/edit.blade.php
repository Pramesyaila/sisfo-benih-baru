@extends('layouts.admin')

@section('title', 'Ubah Akun Petugas')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.admins.index') }}" class="text-sm text-primary-dark hover:underline">&larr; Kembali ke daftar petugas</a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
        <p class="text-sm text-gray-500 mb-5">
            Perubahan require konfirmasi kata sandi petugas yang sedang masuk.
        </p>

        @include('admin.admins._form', ['admin' => $admin, 'mode' => 'edit'])
    </div>
</div>
@endsection