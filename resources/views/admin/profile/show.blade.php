@extends('layouts.admin')

@section('title', 'Profil Petugas')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between gap-3 mb-4">
        <p class="text-sm text-gray-500">
            Data profil menampilkan data yang diisi pada halaman tambah petugas.
        </p>
        <a href="{{ route('profile.edit') }}" class="bg-primary hover:bg-primary-dark text-white font-semibold px-4 py-2 rounded-md text-sm shrink-0">Ubah Profil</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 pb-5 border-b border-gray-100">
            @if ($user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover shrink-0">
            @else
                <span class="w-16 h-16 rounded-full bg-accent-light text-primary-dark flex items-center justify-center text-xl font-bold uppercase shrink-0">
                    {{ str($user->name)->substr(0, 1) }}
                </span>
            @endif
            <div class="min-w-0">
                <p class="text-lg font-bold text-primary-dark break-words">{{ $user->name }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                    <span class="badge {{ $user->isSuperAdmin() ? 'bg-primary text-white' : ($user->isPetugasLayanan() ? 'bg-primary/10 text-primary-dark' : 'bg-accent text-primary-dark') }}">
                        {{ $user->roleDisplayLabel() }}
                    </span>
                    <span class="badge {{ $user->is_active ? 'bg-primary/10 text-primary-dark' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>

        @php
            $identity = [
                'Nama lengkap' => $user->name,
                'Email' => $user->email,
                'Nomor KTP' => $user->nik,
                'Instansi / Kelompok tani' => $user->instansi,
                'Nomor HP' => $user->phone,
                'Nomor WhatsApp' => $user->whatsapp,
            ];

            $address = [
                'Alamat' => $user->alamat,
                'Kelurahan/Desa' => $user->kelurahan,
                'Kecamatan' => $user->kecamatan,
                'Kabupaten/Kota' => $user->kabupaten_kota,
                'Provinsi' => $user->provinsi,
            ];
        @endphp

        <div class="space-y-6 mt-5">
            <div>
                <h3 class="font-semibold text-primary-dark text-sm mb-3">Data identitas</h3>
                <dl class="grid md:grid-cols-2 gap-x-6 gap-y-3">
                    @foreach ($identity as $label => $value)
                        <div class="flex flex-col">
                            <dt class="text-xs text-gray-500">{{ $label }}</dt>
                            <dd class="text-sm break-words {{ $value ? 'text-gray-800' : 'text-gray-400' }}">{{ $value ?: 'Belum diisi' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div>
                <h3 class="font-semibold text-primary-dark text-sm mb-3">Alamat</h3>
                <dl class="grid md:grid-cols-2 gap-x-6 gap-y-3">
                    @foreach ($address as $label => $value)
                        <div class="flex flex-col">
                            <dt class="text-xs text-gray-500">{{ $label }}</dt>
                            <dd class="text-sm break-words {{ $value ? 'text-gray-800' : 'text-gray-400' }}">{{ $value ?: 'Belum diisi' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div>
                <h3 class="font-semibold text-primary-dark text-sm mb-3">Hak akses</h3>
                <dl class="grid md:grid-cols-2 gap-x-6 gap-y-3">
                    <div class="flex flex-col">
                        <dt class="text-xs text-gray-500">Tipe role</dt>
                        <dd class="text-sm text-gray-800">{{ $user->roleLabel() }}</dd>
                    </div>
                    <div class="flex flex-col">
                        <dt class="text-xs text-gray-500">Super Admin</dt>
                        <dd class="text-sm {{ $user->isSuperAdmin() ? 'text-gray-800' : 'text-gray-400' }}">
                            {{ $user->isSuperAdmin() ? 'Ya' : 'Tidak' }}
                        </dd>
                    </div>
                </dl>

                @if ($user->isSuperAdmin())
                    <p class="mt-3 text-xs text-gray-500">
                        Sebagai Super Admin, Anda dapat mengelola fitur Kelola Admin.
                        Setiap aksi pada fitur tersebut memerlukan kata sandi Super Admin.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection