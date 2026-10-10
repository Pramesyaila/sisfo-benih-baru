@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')
<div class="flex items-center justify-between mb-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
        <select name="category" class="rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="variety" class="rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
            <option value="">Semua Varietas</option>
            @foreach($categories as $cat)
                @foreach($cat->children as $child)
                    <option value="{{ $child->id }}" @selected(request('variety') == $child->id)>{{ $cat->name }} &rsaquo; {{ $child->name }}</option>
                @endforeach
            @endforeach
        </select>
        <button class="bg-base border border-gray-200 px-3 py-2 rounded-md text-sm hover:border-primary">Filter</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="bg-primary hover:bg-primary-dark text-white font-semibold px-4 py-2 rounded-md text-sm">+ Tambah Produk</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-base text-gray-500 text-left">
            <tr>
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Harga</th>
                <th class="px-4 py-3">Stok</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($products as $product)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $product->name }} <span class="text-xs text-gray-400">({{ $product->packagingLabel() }})</span></td>
                <td class="px-4 py-3">{{ $product->category->name }}</td>
                <td class="px-4 py-3">{{ $product->formattedPrice() }}</td>
                <td class="px-4 py-3 {{ $product->isLowStock() ? 'text-accent font-semibold' : '' }}">{{ $product->stock }}</td>
                <td class="px-4 py-3">
                    <span class="badge {{ $product->status === 'aktif' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($product->status) }}</span>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="text-primary-dark hover:underline">Edit</a>
                    @include('partials.confirm-form', [
                        'action' => route('admin.products.destroy', $product),
                        'method' => 'DELETE',
                        'label' => 'Hapus',
                        'title' => 'Hapus produk',
                        'message' => 'Hapus produk ' . $product->name . ' dari katalog?',
                        'confirmLabel' => 'Ya, Hapus',
                        'buttonClass' => 'text-red-500 hover:underline',
                    ])
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
