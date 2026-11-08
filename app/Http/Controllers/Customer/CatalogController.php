<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LandingContent;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
{
    $categories = Category::whereNull('parent_id')
        ->with('children.children')
        ->orderBy('name')
        ->get();

    $selectedCategory = $request->string('category')->toString();
    $selectedVariety = $request->string('variety')->toString();
    $selectedGrade = $request->string('grade')->toString();
    $keyword = $request->string('q')->toString();

    $categoryIds = $this->resolveCategoryIds($selectedCategory, $selectedVariety, $selectedGrade);

    $products = Product::with(['category.parent.parent'])
        ->where('status', 'aktif')
        // null = tanpa filter kategori; array kosong = tidak ada yang cocok.
        ->when($categoryIds !== null, fn ($query) => $query->whereIn('category_id', $categoryIds))
        ->when($keyword !== '', fn ($query) => $query->where('name', 'like', '%' . $keyword . '%'))
        ->orderBy('name')
        ->paginate(9)
        ->withQueryString();

    return view('customer.catalog.index', [
        'categories' => $categories,
        'products' => $products,
        'selectedCategory' => $selectedCategory,
        'selectedVariety' => $selectedVariety,
        'selectedGrade' => $selectedGrade,
        'keyword' => $keyword,
        'landing' => LandingContent::current(),
    ]);
}

    public function show(Product $product)
    {
        abort_unless($product->status === 'aktif', 404);

        $product->load('category.parent.parent');

        $related = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'aktif')
            ->limit(4)
            ->get();

        return view('customer.catalog.show', compact('product', 'related'));
    }

    protected function resolveCategoryIds(string $categorySlug, string $varietySlug, string $gradeSlug = ''): ?array
{
    // Kelas dipilih: hanya produk di kelas itu, dan harus berada di bawah varietas yang dipilih.
    if ($gradeSlug !== '') {
        $grade = Category::where('slug', $gradeSlug)->first();

        if (! $grade || ! $grade->parent) {
            return [];
        }

        if ($varietySlug !== '' && $grade->parent->slug !== $varietySlug) {
            return [];
        }

        return [$grade->id];
    }

    if ($varietySlug !== '') {
        $variety = Category::where('slug', $varietySlug)->first();

        if (! $variety) {
            return [];
        }

        if ($categorySlug !== '' && $variety->root()->slug !== $categorySlug) {
            return [];
        }

        return $variety->descendantsAndSelfIds();
    }

    if ($categorySlug !== '') {
        $category = Category::where('slug', $categorySlug)->first();

        return $category ? $category->descendantsAndSelfIds() : [];
    }

    return null;
}
}