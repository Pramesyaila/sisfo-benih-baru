<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
{
    $products = Product::with('category.parent.parent')
        ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%' . $request->q . '%'))
        ->when($request->filled('category'), fn ($query) => $query->whereIn('category_id', $this->categoryIds($request->category)))
        ->when($request->filled('variety'), fn ($query) => $query->whereIn('category_id', $this->categoryIds($request->variety)))
        ->orderBy('name')
        ->paginate(10)
        ->withQueryString();

    return view('admin.products.index', [
        'products' => $products,
        'categories' => $this->categoryTree(),
    ]);
}

    public function create()
    {
        return view('admin.products.create', [
            'categories' => $this->categoryTree(),
            'leafCategories' => $this->leafCategories(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan ke katalog.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->categoryTree(),
            'leafCategories' => $this->leafCategories(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    protected function categoryTree()
    {
        return Category::whereNull('parent_id')
        ->with('children.children')
        ->orderBy('name')
        ->get();
    }

    // Node paling ujung untuk dropdown form produk,
// mis. "Padi → Padi Sawah" atau "Ayam KUB → Bibit Ayam → Grade A".
protected function leafCategories()
{
    return Category::whereDoesntHave('children')
        ->with('parent.parent')
        ->get()
        ->sortBy('full_path')
        ->values();
}

// ID kategori beserta seluruh turunannya, untuk filter.
protected function categoryIds(mixed $id): array
{
    if (blank($id)) {
        return [];
    }

    return Category::find($id)?->descendantsAndSelfIds() ?? [];
}

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'category_id' => [
            'bail',
            'required',
            'exists:categories,id',
            function (string $attribute, mixed $value, \Closure $fail) {
                if (Category::where('parent_id', $value)->exists()) {
                    $fail('Pilih kategori paling ujung (mis. Grade A atau varietas tanpa kelas), bukan kategori yang masih memiliki turunan.');
                }
            },
        ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'packaging_unit' => ['required', 'string', 'max:50'],
            'packaging_size' => ['nullable', 'string', 'max:50'],
            'price' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
