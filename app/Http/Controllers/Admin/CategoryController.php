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
            ->with([
                'children' => fn ($query) => $query->withCount('products'),
                'children.children' => fn ($query) => $query->withCount('products'),
            ])
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
        abort_if(! $category->isRoot(), 404);

        if ($category->products()->exists() || $category->children()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk atau varietas.');
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    /* -----------------------------------------------------------------
     | Varietas (level 2)
     |-----------------------------------------------------------------*/

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
        abort_if($subcategory->depth() !== 2, 404);

        $subcategory->update($this->validateData($request));

        return back()->with('success', 'Varietas/kategori anak berhasil diperbarui.');
    }

    public function destroySubcategory(Category $subcategory)
    {
        abort_if($subcategory->depth() !== 2, 404);

        if ($subcategory->products()->exists()) {
            return back()->with('error', 'Varietas/kategori anak tidak dapat dihapus karena masih memiliki produk.');
        }

        if ($subcategory->children()->exists()) {
            return back()->with('error', 'Varietas tidak dapat dihapus karena masih memiliki kelas. Hapus kelasnya terlebih dahulu.');
        }

        $subcategory->delete();

        return back()->with('success', 'Varietas/kategori anak berhasil dihapus.');
    }

    /* -----------------------------------------------------------------
     | Kelas (level 3, khusus Ayam KUB)
     |-----------------------------------------------------------------*/

    public function storeGrade(Request $request, Category $subcategory)
    {
        // Hanya varietas (level 2) milik kategori yang mengizinkan kelas.
        abort_if($subcategory->depth() !== 2 || ! $subcategory->canHaveChildren(), 404);

        $data = $this->validateData($request);
        $data['parent_id'] = $subcategory->id;
        $data['slug'] = Str::slug($subcategory->name . ' ' . $data['name']) . '-' . Str::random(4);

        Category::create($data);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function updateGrade(Request $request, Category $grade)
    {
        abort_if($grade->depth() !== 3, 404);

        $grade->update($this->validateData($request));

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroyGrade(Category $grade)
    {
        abort_if($grade->depth() !== 3, 404);

        if ($grade->products()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki produk.');
        }

        $grade->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}