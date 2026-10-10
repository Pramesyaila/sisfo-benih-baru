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
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        $selectedCategory = $request->string('category')->toString();
        $selectedVariety = $request->string('variety')->toString();
        $keyword = $request->string('q')->toString();

        $products = Product::with(['category.parent'])
            ->where('status', 'aktif')
            ->when($selectedVariety !== '', function ($query) use ($selectedVariety, $selectedCategory) {
                $query->whereHas('category', function ($categoryQuery) use ($selectedVariety, $selectedCategory) {
                    $categoryQuery->where('slug', $selectedVariety)
                        ->when(
                            $selectedCategory !== '',
                            fn ($parentQuery) => $parentQuery->whereHas('parent', fn ($parent) => $parent->where('slug', $selectedCategory))
                        );
                });
            })
            ->when($selectedVariety === '' && $selectedCategory !== '', function ($query) use ($selectedCategory) {
                // Kategori tanpa varietas tetap ikut tampil saat kategori induknya dipilih.
                $query->whereHas('category', function ($categoryQuery) use ($selectedCategory) {
                    $categoryQuery->where('slug', $selectedCategory)
                        ->orWhereHas('parent', fn ($parent) => $parent->where('slug', $selectedCategory));
                });
            })
            ->when($keyword !== '', fn ($query) => $query->where('name', 'like', '%' . $keyword . '%'))
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        return view('customer.catalog.index', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $selectedCategory,
            'selectedVariety' => $selectedVariety,
            'keyword' => $keyword,
            'landing' => LandingContent::current(),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->status === 'aktif', 404);

        $product->load('category.parent');

        $related = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'aktif')
            ->limit(4)
            ->get();

        return view('customer.catalog.show', compact('product', 'related'));
    }
}