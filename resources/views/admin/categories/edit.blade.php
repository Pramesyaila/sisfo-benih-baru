@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-xl">
    <a href="{{ route('admin.categories.index') }}" class="text-sm text-primary-dark hover:underline">&larr; Kembali ke daftar kategori</a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="font-semibold mb-4">Ubah kategori {{ $category->name }}</h2>
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium mb-1">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <button class="bg-primary text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-primary-dark">Simpan Perubahan</button>
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-sm hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection