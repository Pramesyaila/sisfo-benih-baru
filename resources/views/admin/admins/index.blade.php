@extends('layouts.admin')

@section('title', 'Kelola Admin / Petugas')

@section('content')
<div class="flex items-center justify-between mb-4 gap-3">
    <p class="text-sm text-gray-500">
        Tambah dan kelola akun petugas internal. Hanya ada dua jenis role: Petugas Layanan dan Petugas Gudang.
    </p>
    <a href="{{ route('admin.admins.create') }}" class="bg-primary hover:bg-primary-dark text-white font-semibold px-4 py-2 rounded-md text-sm shrink-0">+ Tambah Petugas</a>
</div>

<div class="bg-accent-light border border-accent text-primary-dark px-4 py-3 rounded-md text-sm mb-4">
    Fitur ini hanya dapat diakses oleh <strong>Super Admin</strong>.
    Setiap aksi tambah, ubah, dan hapus memerlukan kata sandi Super Admin sebagai konfirmasi.
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-base text-gray-500 text-left">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Kontak</th>
                <th class="px-4 py-3">Tipe Role</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
            <tr>
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $user->name }}</p>
                    @if ($user->nik)
                        <p class="text-xs text-gray-500">KTP {{ $user->nik }}</p>
                    @endif
                    @if ($user->instansi)
                        <p class="text-xs text-gray-400">{{ $user->instansi }}</p>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500">
                    <p>{{ $user->email }}</p>
                    @if ($user->phone)
                        <p class="text-xs">{{ $user->phone }}</p>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="badge {{ $user->isSuperAdmin() ? 'bg-primary text-white' : ($user->isPetugasLayanan() ? 'bg-primary/10 text-primary-dark' : 'bg-accent text-primary-dark') }}">
                        {{ $user->roleDisplayLabel() }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="badge {{ $user->is_active ? 'bg-primary/10 text-primary-dark' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('admin.admins.edit', $user) }}" class="text-primary-dark hover:underline">Edit</a>
                    @if ($user->isSuperAdmin())
                        <span class="text-xs text-gray-400 ml-2">(akun bawaan)</span>
                    @elseif (auth()->id() !== $user->id)
                        <button type="button"
                            class="text-red-500 hover:underline ml-2 js-confirm-delete"
                            data-name="{{ $user->name }}"
                            data-action="{{ route('admin.admins.destroy', $user) }}">
                            Hapus
                        </button>
                    @else
                        <span class="text-xs text-gray-400 ml-2">(akun Anda)</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada akun petugas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>

<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h2 class="font-semibold text-primary-dark">Tentang dua role petugas</h2>
    <div class="grid md:grid-cols-2 gap-4 mt-3 text-sm text-gray-600">
        <div>
            <p class="font-semibold text-gray-700">Petugas Layanan (Super Admin)</p>
            <p class="mt-1 leading-relaxed">Mengelola seluruh administrasi: pesanan, billing, verifikasi pembayaran, pembuatan faktur, akun petugas, dan konten landing page konsumen.</p>
        </div>
        <div>
            <p class="font-semibold text-gray-700">Petugas Gudang</p>
            <p class="mt-1 leading-relaxed">Mengelola produk, kategori dan varietas, seluruh pencatatan stok masuk/keluar, serta proses pengambilan benih sampai pesanan selesai.</p>
        </div>
    </div>
</div>

<dialog id="confirm-delete-modal" class="rounded-xl p-0 w-full max-w-md">
    <form method="POST" id="confirm-delete-form" class="p-6">
        @csrf
        @method('DELETE')
        <h3 class="text-lg font-bold text-primary-dark mb-2">Hapus akun petugas</h3>
        <p class="text-sm text-gray-600 mb-4">
            Anda akan menghapus akun <strong id="confirm-delete-name"></strong>. Tindakan ini tidak dapat dibatalkan.
        </p>
        <label class="block text-sm font-medium mb-1">Kata sandi Super Admin</label>
        <input type="password" name="super_admin_password" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
        <p class="text-xs text-gray-500 mt-1">Wajib diisi sebagai syarat menghapus akun petugas.</p>
        @error('super_admin_password')
            <p class="form-error text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" class="px-4 py-2 rounded-md border border-gray-300 text-sm" data-modal-close="confirm-delete-modal">Batal</button>
            <button class="px-4 py-2 rounded-md bg-red-600 text-white text-sm font-semibold hover:bg-red-700">Hapus Akun</button>
        </div>
    </form>
</dialog>

<script>
    (function () {
        var modal = document.getElementById('confirm-delete-modal');
        var form = document.getElementById('confirm-delete-form');
        var nameTarget = document.getElementById('confirm-delete-name');

        document.querySelectorAll('.js-confirm-delete').forEach(function (button) {
            button.addEventListener('click', function () {
                form.action = button.dataset.action;
                nameTarget.textContent = button.dataset.name;
                if (typeof modal.showModal === 'function') modal.showModal();
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                var target = document.getElementById(button.dataset.modalClose);
                if (target && typeof target.close === 'function') target.close();
            });
        });
    })();
</script>
@endsection