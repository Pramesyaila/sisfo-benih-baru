@extends('layouts.admin')

@section('title', 'Kelola Kategori & Varietas')

@section('content')
<div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-4">
        @forelse($categories as $category)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-4 py-3 border-b bg-base">
                    <div>
                        <p class="font-semibold">{{ $category->name }}</p>
                        @if ($category->description)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $category->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500 whitespace-nowrap">
                        <span>{{ $category->products_total }} produk</span>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-primary-dark font-semibold hover:underline">Edit</a>
                        @include('partials.confirm-form', [
                            'action' => route('admin.categories.destroy', $category),
                            'method' => 'DELETE',
                            'label' => 'Hapus',
                            'title' => 'Hapus kategori',
                            'message' => 'Hapus kategori ' . $category->name . ' beserta varietasnya?',
                            'confirmLabel' => 'Ya, Hapus',
                            'buttonClass' => 'text-red-500 hover:underline',
                        ])
                    </div>
                </div>

                @if ($category->children->isNotEmpty())
                    <table class="w-full text-sm">
                        <thead class="text-gray-500 text-left text-xs">
                            <tr><th class="px-4 py-2">Varietas / Kategori Anak</th><th class="px-4 py-2">Produk</th><th class="px-4 py-2 text-right">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($category->children as $child)
                                <tr>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('admin.subcategories.update', $child) }}" method="POST" class="flex items-center gap-2">
                                            @csrf @method('PATCH')
                                            <input type="text" name="name" value="{{ $child->name }}" required class="flex-1 rounded-md border-gray-300 px-2 py-1 text-sm">
                                            <button class="text-xs text-primary-dark font-semibold hover:underline">Simpan</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-2 text-gray-500">{{ $child->products_total }}</td>
                                    <td class="px-4 py-2 text-right">
                                        @include('partials.confirm-form', [
                                                'action' => route('admin.subcategories.destroy', $child),
                                                'method' => 'DELETE',
                                                'label' => 'Hapus',
                                                'title' => 'Hapus varietas',
                                                'message' => 'Hapus varietas ' . $child->name . '?',
                                                'confirmLabel' => 'Ya, Hapus',
                                                'buttonClass' => 'text-red-500 text-xs hover:underline',
                                            ])
                                    </td>
                                </tr>

                                {{-- Kelas (level 3): hanya untuk kategori Ayam KUB --}}
                                @if ($category->isGradedRoot())
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-4 py-3">
                                            <p class="text-xs font-semibold text-gray-500 mb-2">Kelas {{ $child->name }}</p>

                                            <div class="space-y-2 pl-4 border-l-2 border-gray-200">
                                                @forelse ($child->children as $grade)
                                                    <div class="flex items-center gap-3">
                                                        <form action="{{ route('admin.grades.update', $grade) }}" method="POST" class="flex flex-1 items-center gap-2">
                                                            @csrf @method('PATCH')
                                                            <input type="text" name="name" value="{{ $grade->name }}" required class="flex-1 rounded-md border-gray-300 px-2 py-1 text-sm">
                                                            <button class="text-xs text-primary-dark font-semibold hover:underline">Simpan</button>
                                                        </form>
                                                        <span class="text-xs text-gray-500 whitespace-nowrap">{{ $grade->products_count }} produk</span>
                                                        @include('partials.confirm-form', [
                                                            'action' => route('admin.grades.destroy', $grade),
                                                            'method' => 'DELETE',
                                                            'label' => 'Hapus',
                                                            'title' => 'Hapus kelas',
                                                            'message' => 'Hapus kelas ' . $grade->name . ' dari ' . $child->name . '?',
                                                            'confirmLabel' => 'Ya, Hapus',
                                                            'buttonClass' => 'text-red-500 text-xs hover:underline',
                                                        ])
                                                    </div>
                                                @empty
                                                    <p class="text-xs text-gray-400">Belum ada kelas.</p>
                                                @endforelse

                                                <form action="{{ route('admin.grades.store', $child) }}" method="POST" class="flex items-center gap-2 pt-1">
                                                    @csrf
                                                    <input type="text" name="name" placeholder="Tambah kelas, mis. Grade A" required class="flex-1 rounded-md border-gray-300 px-3 py-1.5 text-sm">
                                                    <button class="bg-primary text-white px-3 py-1.5 rounded-md text-xs font-semibold hover:bg-primary-dark">Tambah Kelas</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="px-4 py-3 text-xs text-gray-400">Belum ada varietas/kategori anak.</p>
                @endif

                <div class="px-4 py-3 border-t bg-base">
                    <form action="{{ route('admin.subcategories.store', $category) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="name" placeholder="Tambah varietas, mis. Padi Sawah" required class="flex-1 rounded-md border-gray-300 px-3 py-2 text-sm">
                        <button class="bg-primary text-white px-3 py-2 rounded-md text-xs font-semibold hover:bg-primary-dark">Tambah</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center text-sm text-gray-400">Belum ada kategori.</div>
        @endforelse
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 h-fit">
        <h2 class="font-semibold mb-1">Tambah Kategori</h2>
        <p class="text-xs text-gray-500 mb-3">Kategori induk seperti Padi, Hortikultura, atau Ayam KUB.</p>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">{{ old('description') }}</textarea>
            </div>
            <button class="w-full bg-primary text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-primary-dark">Simpan Kategori</button>
        </form>
        <p class="mt-4 text-xs text-gray-400 leading-relaxed">
            Produk disimpan pada kategori paling ujung. Contoh: Padi → Padi Sawah → Inpari 32.
            Khusus Ayam KUB, produk disimpan pada kelas: Ayam KUB → Bibit Ayam → Grade A.
        </p>
    </div>
</div>
@endsection