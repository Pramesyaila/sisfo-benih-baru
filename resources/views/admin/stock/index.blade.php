@extends('layouts.admin')

@section('title', 'Stok Masuk / Keluar')

@section('content')
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="rounded-md border-gray-300 px-3 py-2 border text-sm">
    <select name="category" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="variety" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
        <option value="">Semua Varietas</option>
        @foreach($categories as $cat)
            @foreach($cat->children as $child)
                <option value="{{ $child->id }}" @selected(request('variety') == $child->id)>{{ $cat->name }} &rsaquo; {{ $child->name }}</option>
            @endforeach
        @endforeach
    </select>
    <button class="bg-base border border-gray-200 px-3 py-2 rounded-md text-sm hover:border-primary">Filter</button>
    <a href="{{ route('admin.stock.history') }}" class="ml-auto bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark">Lihat Riwayat Stok &rarr;</a>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-base text-gray-500 text-left">
            <tr>
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Stok Saat Ini</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($products as $product)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                <td class="px-4 py-3 text-gray-500">
                    {{ optional($product->category->parent)->name }}
                    @if ($product->category->parent)
                        &rsaquo;
                    @endif
                    {{ $product->category->name }}
                </td>
                <td class="px-4 py-3 {{ $product->isLowStock() ? 'text-accent font-semibold' : '' }}">{{ $product->stock }} {{ $product->packaging_unit }}</td>
                <td class="px-4 py-3 text-right">
                    <button type="button" class="bg-primary text-white px-3 py-1.5 rounded-md text-xs hover:bg-primary-dark" data-modal-open="stock-modal-{{ $product->id }}">Catat Stok</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Produk tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>

@foreach($products as $product)
    <dialog id="stock-modal-{{ $product->id }}" class="rounded-xl p-0 w-full max-w-lg">
        <form method="POST" action="{{ route('admin.stock.in', $product) }}" class="p-6">
            @csrf
            <div class="flex items-start justify-between gap-3 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-primary-dark">Pencatatan Stok</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $product->name }} &middot; stok saat ini {{ $product->stock }} {{ $product->packaging_unit }}</p>
                </div>
                <button type="button" class="text-gray-400" data-modal-close="stock-modal-{{ $product->id }}">✕</button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Jumlah</label>
                    <input type="number" name="qty" min="1" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Berita Acara / Deskripsi</label>
                    <textarea name="note" rows="3" required placeholder="Contoh: stok bertambah dariereum produksi 2026, atau berkurang karena kerusakan di gudang." class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm"></textarea>
                    <p class="text-xs text-gray-400 mt-1">Wajib diisi sebagai dokumentasi transaksi stok.</p>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2 mt-6">
                <button type="button" class="px-4 py-2 rounded-md border border-gray-300 text-sm" data-modal-close="stock-modal-{{ $product->id }}">Batal</button>
                <button formaction="{{ route('admin.stock.out', $product) }}" class="px-4 py-2 rounded-md bg-accent text-primary-dark text-sm font-semibold hover:brightness-95">Simpan sebagai Keluar</button>
                <button class="px-4 py-2 rounded-md bg-primary text-white text-sm font-semibold hover:bg-primary-dark">Simpan sebagai Masuk</button>
            </div>
        </form>
    </dialog>
@endforeach

<script>
    document.querySelectorAll('[data-modal-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            var modal = document.getElementById(button.dataset.modalOpen);
            if (modal && typeof modal.showModal === 'function') modal.showModal();
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            var modal = document.getElementById(button.dataset.modalClose);
            if (modal && typeof modal.close === 'function') modal.close();
        });
    });
</script>
@endsection