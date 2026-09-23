@extends($layout)

@section('title', 'Edit Profile')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-primary-dark px-6 py-5 text-white">
            <h2 class="text-xl font-bold">Edit Profile</h2>
            <p class="text-sm text-white/70 mt-1">
                Perbarui informasi akun dan data diri Anda
            </p>
        </div>

        {{-- Form --}}
        <form
            action="{{ route('profile.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="p-6"
        >

            @csrf
            @method('PUT')

            {{-- Foto Profile --}}
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Foto Profile
                </label>

                <div class="flex items-center gap-5">

                    <div class="w-24 h-24 rounded-full bg-gray-100 border overflow-hidden flex items-center justify-center">

                        @if($user->profile_photo)
                            <img
                                src="{{ asset('storage/' . $user->profile_photo) }}"
                                alt="Foto Profile"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <span class="text-3xl text-gray-400">
                                👤
                            </span>
                        @endif

                    </div>

                    <div>
                        <input
                            type="file"
                            name="profile_photo"
                            accept="image/*"
                            class="block w-full text-sm text-gray-600"
                        >

                        <p class="text-xs text-gray-500 mt-1">
                            JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                        </p>

                        @error('profile_photo')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- Nama --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >

                @error('name')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >

                @error('email')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Nomor Telepon --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $user->phone) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >

                @error('phone')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- NIK --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    NIK
                </label>

                <input
                    type="text"
                    name="nik"
                    value="{{ old('nik', $user->nik) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >

                @error('nik')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Domisili --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Domisili
                </label>

                <input
                    type="text"
                    name="domisili"
                    value="{{ old('domisili', $user->domisili) }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >

                @error('domisili')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat
                </label>

                <textarea
                    name="alamat"
                    rows="4"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                >{{ old('alamat', $user->alamat) }}</textarea>

                @error('alamat')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="mt-6 flex justify-end gap-3">

                <a
                    href="{{ route('profile.show') }}"
                    class="px-5 py-2 rounded-md border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="bg-primary text-white px-5 py-2 rounded-md font-semibold hover:bg-primary-dark"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
