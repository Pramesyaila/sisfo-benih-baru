<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('order_number', 'like', '%' . $request->q . '%')
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%' . $request->q . '%'));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'items.product.category.parent',
            'user',
            'billing',
            'paymentProofs.verifiedBy',
            'completionReceipt.uploader',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function process(Order $order)
    {
        if ($order->status !== 'dipesan') {
            return back()->with('error', 'Pesanan sudah diproses sebelumnya.');
        }

        foreach ($order->items as $item) {
            if ($item->product && $item->qty > $item->product->stock) {
                return back()->with('error', "Stok {$item->product_name} tidak mencukupi untuk memproses pesanan ini.");
            }
        }

        $order->update([
            'status' => 'diproses',
            'processed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Pesanan sedang diproses. Silakan upload billing kepada konsumen.');
    }

    public function cancel(Order $order)
    {
        if (! $order->canBeCancelled()) {
            return back()->with('error', 'Pesanan hanya dapat dibatalkan sebelum billing dikirim.');
        }

        $order->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function printPermohonan(Order $order)
    {
        $order->load('user', 'items.product.category.parent');

        return view('admin.orders.print-permohonan', compact('order'));
    }
}
