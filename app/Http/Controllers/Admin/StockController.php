<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $categories = $this->categoryTree();

        $products = Product::with(['category.parent'])
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%' . $request->q . '%'))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('id', $request->category)->orWhereHas('parent', fn ($parent) => $parent->where('id', $request->category))))
            ->when($request->filled('variety'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('id', $request->variety)))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.stock.index', compact('products', 'categories'));
    }

    public function history(Request $request)
    {
        $transactions = StockTransaction::with(['product.category.parent', 'user'])
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->product_id))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('product', fn ($product) => $product->whereHas('category', fn ($category) => $category->where('id', $request->category)->orWhereHas('parent', fn ($parent) => $parent->where('id', $request->category)))))
            ->when($request->filled('variety'), fn ($query) => $query->whereHas('product', fn ($product) => $product->whereHas('category', fn ($category) => $category->where('id', $request->variety))))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock.history', [
            'transactions' => $transactions,
            'products' => Product::with('category')->orderBy('name')->get(),
            'categories' => $this->categoryTree(),
        ]);
    }

    public function storeIn(Request $request, Product $product)
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
            'note' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($product, $data) {
            $lockedProduct = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $before = $lockedProduct->stock;
            $after = $before + $data['qty'];

            $lockedProduct->update(['stock' => $after]);

            StockTransaction::create([
                'product_id' => $lockedProduct->id,
                'type' => 'masuk',
                'qty' => $data['qty'],
                'stock_before' => $before,
                'stock_after' => $after,
                'note' => $data['note'],
                'user_id' => Auth::id(),
            ]);
        });

        return back()->with('success', "Stok {$product->name} berhasil ditambahkan.");
    }

    public function storeOut(Request $request, Product $product)
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
            'note' => ['required', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($product, $data) {
                $lockedProduct = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

                if ($data['qty'] > $lockedProduct->stock) {
                    throw new RuntimeException('Jumlah stok keluar melebihi stok yang tersedia.');
                }

                $before = $lockedProduct->stock;
                $after = $before - $data['qty'];
                $lockedProduct->update(['stock' => $after]);

                StockTransaction::create([
                    'product_id' => $lockedProduct->id,
                    'type' => 'keluar',
                    'qty' => $data['qty'],
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'note' => $data['note'],
                    'user_id' => Auth::id(),
                ]);
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Stok {$product->name} berhasil dikurangi.");
    }

    protected function categoryTree()
    {
        return Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
    }
}
