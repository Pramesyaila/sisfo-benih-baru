@extends('layouts.admin')

@section('title', 'Riwayat Stok')

@section('content')
<form method="GET" class="mb-4 flex flex-wrap gap-2 items-end bg-white p-4 rounded-xl border border-gray-100">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Kategori</label>
        <select name="category" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
            <option value="">Semua</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Varietas</label>
        <select name="variety" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
            <option value="">Semua</option>
            @foreach($categories as $cat)
                @foreach($cat->children as $child)
                    <option value="{{ $child->id }}" @selected(request('variety') == $child->id)>{{ $cat->name }} &rsaquo; {{ $child->name }}</option>
                @endforeach
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Produk</label>
        <select name="product_id" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
            <option value="">Semua</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Jenis</label>
        <select name="type" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
            <option value="">Semua</option>
            <option value="masuk" @selected(request('type')==='masuk')>Masuk</option>
            <option value="keluar" @selected(request('type')==='keluar')>Keluar</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Dari</label>
        <input type="date" name="from" value="{{ request('from') }}" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Sampai</label>
        <input type="date" name="to" value="{{ request('to') }}" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
    </div>
    <button class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-base text-gray-500 text-left">
            <tr>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Jenis</th>
                <th class="px-4 py-3">Jumlah</th>
                <th class="px-4 py-3">Stok Sebelum &rarr; Sesudah</th>
                <th class="px-4 py-3">Berita Acara</th>
                <th class="px-4 py-3">Petugas</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($transactions as $t)
            <tr>
                <td class="px-4 py-3">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 font-medium">{{ $t->product->name ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500">
                    {{ optional(optional($t->product)->category?->parent)->name }}
                    @if (optional($t->product)->category?->parent)
                        &rsaquo;
                    @endif
                    {{ optional(optional($t->product)->category)->name }}
                </td>
                <td class="px-4 py-3">
                    <span class="badge {{ $t->type === 'masuk' ? 'bg-primary text-white' : 'bg-accent text-primary-dark' }}">{{ ucfirst($t->type) }}</span>
                </td>
                <td class="px-4 py-3">{{ $t->qty }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $t->stock_before }} &rarr; {{ $t->stock_after }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $t->note ?: '-' }}</td>
                <td class="px-4 py-3">{{ $t->user->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada riwayat pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
