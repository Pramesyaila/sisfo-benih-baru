<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Laporan penjualan. Distribusi digabung ke halaman yang sama karena
     * keduanya sama-sama originates dari satu pesanan yang sama.
     */
    public function sales(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $orders = Order::with([
            'user',
            'items.product.category.parent',
            'billing',
            'invoice',
        ])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->get();

        $totalPenjualan = $orders
            ->whereIn('status', ['siap_diambil', 'selesai'])
            ->sum('total');

        $totalProdukTerjual = $orders
            ->whereIn('status', ['siap_diambil', 'selesai'])
            ->sum(fn ($order) => $order->totalQuantity());

        return view('admin.reports.sales', [
            'orders' => $orders,
            'from' => $from,
            'to' => $to,
            'totalPenjualan' => $totalPenjualan,
            'totalProdukTerjual' => $totalProdukTerjual,
        ]);
    }

    /**
     * Tampilan khusus cetak laporan penjualan.
     */
    public function printSales(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $orders = Order::with(['user', 'items', 'billing', 'invoice'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->get();

        $totalPenjualan = $orders
            ->whereIn('status', ['siap_diambil', 'selesai'])
            ->sum('total');

        $totalProdukTerjual = $orders
            ->whereIn('status', ['siap_diambil', 'selesai'])
            ->sum(fn ($order) => $order->totalQuantity());

        return view('admin.reports.print-sales', compact(
            'orders',
            'from',
            'to',
            'totalPenjualan',
            'totalProdukTerjual'
        ));
    }

    public function stock(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $transactions = StockTransaction::with(['product.category.parent', 'user'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->product_id))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('product', fn ($product) => $product->whereHas('category', fn ($category) => $category->where('id', $request->category)->orWhereHas('parent', fn ($parent) => $parent->where('id', $request->category)))))
            ->when($request->filled('variety'), fn ($query) => $query->whereHas('product', fn ($product) => $product->whereHas('category', fn ($category) => $category->where('id', $request->variety))))
            ->latest()
            ->get();

        return view('admin.reports.stock', [
            'transactions' => $transactions,
            'from' => $from,
            'to' => $to,
            'totalMasuk' => $transactions->where('type', 'masuk')->sum('qty'),
            'totalKeluar' => $transactions->where('type', 'keluar')->sum('qty'),
            'categories' => \App\Models\Category::whereNull('parent_id')->with('children')->get(),
        ]);
    }
}