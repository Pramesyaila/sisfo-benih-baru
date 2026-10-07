<div>
    <label class="block text-sm font-medium mb-1">Kategori / Varietas</label>
    <select name="category_id" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
        @foreach($categories as $cat)
            <optgroup label="{{ $cat->name }}">
                @if ($cat->children->isEmpty())
                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? null) == $cat->id)>{{ $cat->name }} (tanpa varietas)</option>
                @else
                    @foreach ($cat->children as $child)
                        <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id ?? null) == $child->id)>{{ $cat->name }} &rsaquo; {{ $child->name }}</option>
                    @endforeach
                @endif
            </optgroup>
        @endforeach
    </select>
    <p class="text-xs text-gray-400 mt-1">Pilih kategori anak (varietas) agar konsumen dapat memfilternya.</p>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Nama Produk</label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
</div>
<div>
    <label class="block text-sm font-medium mb-1">Deskripsi</label>
    <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Ukuran Kemasan (mis. 5 kg)</label>
        <input type="text" name="packaging_size" value="{{ old('packaging_size', $product->packaging_size ?? '') }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Satuan (mis. bungkus/ekor)</label>
        <input type="text" name="packaging_unit" value="{{ old('packaging_unit', $product->packaging_unit ?? 'bungkus') }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Harga (Rp)</label>
        <input type="number" name="price" value="{{ old('price', $product->price ?? 0) }}" min="0" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Batas Stok Menipis</label>
        <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 10) }}" min="0" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
    </div>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Status</label>
    <select name="status" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary px-3 py-2 border text-sm">
        <option value="aktif" @selected(old('status', $product->status ?? 'aktif') === 'aktif')>Aktif (tampil di katalog)</option>
        <option value="nonaktif" @selected(old('status', $product->status ?? '') === 'nonaktif')>Nonaktif</option>
    </select>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Foto Produk</label>
    <input type="file" name="image" class="w-full text-sm">
    @if(!empty($product) && $product->image)
        <img src="{{ asset('storage/'.$product->image) }}" class="mt-2 h-20 rounded-md">
    @endif
</div>
@if(!empty($product))
<p class="text-xs text-gray-400">Stok saat ini: <strong>{{ $product->stock }}</strong> (ubah stok melalui menu Stok Masuk/Keluar)</p>
@endif
