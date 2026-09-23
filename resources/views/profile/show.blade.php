@extends($layout)

@section('title', 'Profile')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-primary-dark px-6 py-5 text-white">
            <h2 class="text-xl font-bold">Profile Saya</h2>
            <p class="text-sm text-white/70 mt-1">
                Informasi akun dan data diri Anda
            </p>
        </div>

        {{-- Isi Profile --}}
        <div class="p-6">

            {{-- Foto --}}
            <div class="flex justify-center mb-6">
                <div class="w-28 h-28 rounded-full bg-gray-100 border-4 border-white shadow flex items-center justify-center overflow-hidden">

                    @if($user->profile_photo)
                        <img
                            src="{{ asset('storage/' . $user->profile_photo) }}"
                            alt="Foto Profile"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <span class="text-4xl text-gray-400">
                            👤
                        </span>
                    @endif

                </div>
            </div>

            {{-- Nama --}}
            <div class="border-b py-4">
                <p class="text-sm text-gray-500">Nama</p>
                <p class="font-semibold text-gray-800">
                    {{ $user->name }}
                </p>
            </div>

            {{-- Email --}}
            <div class="border-b py-4">
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-semibold text-gray-800">
                    {{ $user->email }}
                </p>
            </div>

            {{-- Role --}}
            <div class="border-b py-4">
                <p class="text-sm text-gray-500">Role</p>
                <p class="font-semibold text-gray-800">
                    {{ $user->roleLabel() }}
                </p>
            </div>

            {{-- Nomor Telepon --}}
            <div class="border-b py-4">
                <p class="text-sm text-gray-500">Nomor Telepon</p>
                <p class="font-semibold text-gray-800">
                    {{ $user->phone ?: '-' }}
                </p>
            </div>

            {{-- NIK --}}
            <div class="border-b py-4">
                <p class="text-sm text-gray-500">NIK</p>
                <p class="font-semibold text-gray-800">
                    {{ $user->nik ?: '-' }}
                </p>
            </div>

            {{-- Domisili --}}
            <div class="border-b py-4">
                <p class="text-sm text-gray-500">Domisili</p>
                <p class="font-semibold text-gray-800">
                    {{ $user->domisili ?: '-' }}
                </p>
            </div>

            {{-- Alamat --}}
            <div class="py-4">
                <p class="text-sm text-gray-500">Alamat</p>
                <p class="font-semibold text-gray-800">
                    {{ $user->alamat ?: '-' }}
                </p>
            </div>

            {{-- Tombol --}}
            <div class="mt-6 flex justify-end">
                <a
                    href="{{ route('profile.edit') }}"
                    class="bg-primary text-white px-5 py-2 rounded-md font-semibold hover:bg-primary-dark"
                >
                    Edit Profile
                </a>
            </div>

        </div>

    </div>

</div>

@endsection
