@php
    $isEdit = $mode === 'edit';
    $action = $isEdit ? route('admin.admins.update', $admin) : route('admin.admins.store');
@endphp

<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-md text-sm">
            <p class="font-semibold mb-1">Periksa kembali data berikut:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <h3 class="font-semibold text-primary-dark mb-3">Data utama</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tipe Role</label>
                <select name="role" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                    <option value="petugas_layanan" @selected(old('role', $admin->role ?? '') === 'petugas_layanan')>Petugas Layanan (Super Admin)</option>
                    <option value="petugas_gudang" @selected(old('role', $admin->role ?? '') === 'petugas_gudang')>Petugas Gudang</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Petugas Layanan menangani administrasi; Petugas Gudang menangani produk, stok, dan pengambilan.</p>
            </div>
        </div>
    </div>

    <div>
        <h3 class="font-semibold text-primary-dark mb-3">Data identitas</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nomor KTP</label>
                <input type="text" name="nik" value="{{ old('nik', $admin->nik ?? '') }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Instansi / Kelompok Tani</label>
                <input type="text" name="instansi" value="{{ old('instansi', $admin->instansi ?? '') }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nomor HP</label>
                <input type="text" name="phone" value="{{ old('phone', $admin->phone ?? '') }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Nomor WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $admin->whatsapp ?? '') }}" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
        </div>
    </div>

    <div>
        <h3 class="font-semibold text-primary-dark mb-3">Alamat</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Alamat (Jalan)</label>
                <textarea name="alamat" rows="2" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">{{ old('alamat', $admin->alamat ?? '') }}</textarea>
            </div>
            @foreach (\App\Http\Controllers\ProfileController::addressFields() as $field)
                <div>
                    <label class="block text-sm font-medium mb-1">{{ $field['label'] }}</label>
                    <input
                        type="text"
                        name="{{ $field['name'] }}"
                        value="{{ old($field['name'], $admin->{$field['name']} ?? '') }}"
                        placeholder="{{ $field['placeholder'] }}"
                        class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm"
                    >
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <h3 class="font-semibold text-primary-dark mb-3">Akses</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kata Sandi {{ $isEdit ? '(kosongkan bila tidak diubah)' : '' }}</label>
                <input type="password" name="password" {{ $isEdit ? '' : 'required' }} minlength="8" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Ulangi Kata Sandi</label>
                <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }} minlength="8" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm mt-4">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $admin->is_active ?? true)) class="rounded border-gray-300 text-primary focus:ring-primary">
            Akun aktif dan dapat login
        </label>
    </div>

    @if ($isEdit)
        <div class="border-t pt-4">
            <h3 class="font-semibold text-primary-dark mb-1">Konfirmasi Super Admin</h3>
            <p class="text-xs text-gray-500 mb-3">
                Untuk keamanan, setiap perubahan pada Kelola Admin memerlukan kata sandi Super Admin.
            </p>
            <label class="block text-sm font-medium mb-1">Kata Sandi Super Admin</label>
            <input type="password" name="super_admin_password" required class="w-full md:w-72 rounded-md border-gray-300 px-3 py-2 border text-sm">
            @error('super_admin_password')
                <p class="form-error text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div class="flex items-center gap-2">
        <button class="bg-primary text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-primary-dark">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Petugas' }}
        </button>
        <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm hover:bg-gray-50">Batal</a>
    </div>
</form>