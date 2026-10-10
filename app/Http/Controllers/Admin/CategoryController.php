<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children' => fn ($query) => $query->withCount('products')])
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);

        Category::create($data);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        abort_if(! $category->isRoot(), 404);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        abort_if(! $category->isRoot(), 404);

        $category->update($this->validateData($request));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists() || $category->children()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk atau varietas.');
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function storeSubcategory(Request $request, Category $category)
    {
        abort_if(! $category->isRoot(), 404);

        $data = $this->validateData($request);
        $data['parent_id'] = $category->id;
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);

        Category::create($data);

        return back()->with('success', 'Varietas/kategori anak berhasil ditambahkan.');
    }

    public function updateSubcategory(Request $request, Category $subcategory)
    {
        abort_if($subcategory->parent_id === null, 404);

        $subcategory->update($this->validateData($request));

        return back()->with('success', 'Varietas/kategori anak berhasil diperbarui.');
    }

    public function destroySubcategory(Category $subcategory)
    {
        abort_if($subcategory->parent_id === null, 404);

        if ($subcategory->products()->exists()) {
            return back()->with('error', 'Varietas/kategori anak tidak dapat dihapus karena masih memiliki produk.');
        }

        $subcategory->delete();

        return back()->with('success', 'Varietas/kategori anak berhasil dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
