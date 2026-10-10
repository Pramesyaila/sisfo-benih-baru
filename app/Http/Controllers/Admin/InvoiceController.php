<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Notifications\InvoiceReadyNotification;
use App\Services\NotifiesSafely;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    use NotifiesSafely;

    /**
     * Terbitkan faktur setelah pembayaran terverifikasi.
     * Satu pesanan hanya memiliki satu faktur.
     */
    public function store(Request $request, Order $order)
    {
        if ($order->invoice) {
            return back()->with('error', 'Faktur untuk pesanan ini sudah pernah diterbitkan.');
        }

        if (! in_array($order->status, ['siap_diambil', 'selesai'], true)) {
            return back()->with('error', 'Faktur hanya dapat diterbitkan setelah pembayaran diverifikasi.');
        }

        $invoice = DB::transaction(function () use ($order) {
            return Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV/' . now()->format('Ymd') . '/' . strtoupper(Str::random(5)),
                'total' => $order->itemsTotal() ?: $order->total,
                'pickup_date' => $order->pickup_date,
                'pickup_location' => $order->pickup_location,
                'issued_by' => Auth::id(),
            ]);
        });

        // Notifikasi dikirim setelah transaksi selesai agar kegagalan mail
        // tidak membatalkan penerbitan faktur.
        $this->notifySafely($order->user, new InvoiceReadyNotification($invoice));

        return back()->with('success', 'Faktur berhasil diterbitkan dan telah dikirim kepada konsumen.');
    }

    /**
     * Pratinjau faktur untuk petugas.
     */
    public function show(Order $order)
    {
        abort_unless($order->invoice, 404);

        $order->load(['user', 'items', 'invoice.issuer']);

        return view('admin.orders.print-faktur', [
            'order' => $order,
            'invoice' => $order->invoice,
        ]);
    }
}