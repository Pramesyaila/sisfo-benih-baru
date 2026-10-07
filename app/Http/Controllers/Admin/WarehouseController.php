<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompletionReceipt;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class WarehouseController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items.product.category', 'billing'])
            ->where('status', 'siap_diambil')
            ->latest()
            ->paginate(10);

        return view('admin.orders.warehouse', compact('orders'));
    }

    public function release(Order $order)
    {
        if ($order->status !== 'siap_diambil') {
            return back()->with('error', 'Pesanan belum siap untuk diserahkan.');
        }

        if ($order->hasReleasedStock()) {
            return back()->with('error', 'Stok untuk pesanan ini sudah dicatat keluar sebelumnya.');
        }

        foreach ($order->items as $item) {
            if (! $item->product) {
                return back()->with('error', "Produk {$item->product_name} tidak ditemukan.");
            }

            if ($item->qty > $item->product->stock) {
                return back()->with('error', "Stok {$item->product_name} tidak mencukupi.");
            }
        }

        try {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

                    if (! $product || $product->stock < $item->qty) {
                        throw new RuntimeException("Stok {$item->product_name} tidak mencukupi.");
                    }

                    $before = $product->stock;
                    $after = $before - $item->qty;
                    $product->update(['stock' => $after]);

                    StockTransaction::create([
                        'product_id' => $product->id,
                        'type' => 'keluar',
                        'qty' => $item->qty,
                        'stock_before' => $before,
                        'stock_after' => $after,
                        'note' => "Distribusi pesanan {$order->order_number}",
                        'order_id' => $order->id,
                        'user_id' => Auth::id(),
                    ]);
                }
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Benih berhasil diserahkan dan stok berhasil dikurangi.');
    }

    public function complete(Request $request, Order $order)
    {
        if ($order->status !== 'siap_diambil') {
            return back()->with('error', 'Pesanan tidak dalam proses pengambilan.');
        }

        if ($order->completionReceipt()->exists()) {
            return back()->with('error', 'Faktur penyelesaian untuk pesanan ini sudah diunggah.');
        }

        if (! $order->hasReleasedStock()) {
            return back()->with('error', 'Catat serah terima benih terlebih dahulu sebelum mengunggah faktur selesai.');
        }

        $data = $request->validate([
            'receipt' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $path = $data['receipt']->store('completion-receipts', 'public');

        CompletionReceipt::create([
            'order_id' => $order->id,
            'receipt_number' => 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
            'completed_at' => now(),
        ]);

        $order->update([
            'status' => 'selesai',
            'taken_at' => now(),
        ]);

        return back()->with('success', 'Faktur penyelesaian berhasil diunggah dan pesanan ditandai selesai.');
    }
}
