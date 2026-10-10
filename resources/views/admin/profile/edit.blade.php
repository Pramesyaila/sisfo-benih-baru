@extends('layouts.admin')

@section('title', 'Ubah Profil Petugas')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('profile.show') }}" class="text-sm text-primary-dark hover:underline">&larr; Kembali ke profil</a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h3 class="font-semibold text-primary-dark mb-3">Data utama</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-primary-dark mb-3">Data identitas</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nomor KTP</label>
                        <input type="text" name="nik" value="{{ old('nik', $user->nik) }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Instansi / Kelompok tani</label>
                        <input type="text" name="instansi" value="{{ old('instansi', $user->instansi) }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nomor HP</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nomor WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-primary-dark mb-3">Alamat</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Alamat (jalan)</label>
                        <textarea name="alamat" rows="2" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">{{ old('alamat', $user->alamat) }}</textarea>
                    </div>
                    @foreach (\App\Http\Controllers\ProfileController::addressFields() as $field)
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ $field['label'] }}</label>
                            <input type="text" name="{{ $field['name'] }}" value="{{ old($field['name'], $user->{$field['name']}) }}" placeholder="{{ $field['placeholder'] }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t pt-4">
                <label class="block text-sm font-medium mb-1">Foto profil</label>
                <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm">
                @error('profile_photo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2">
                <button class="bg-primary text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-primary-dark">Simpan Perubahan</button>
                <a href="{{ route('profile.show') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection