<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Order;
use App\Notifications\BillingAvailableNotification;
use App\Services\NotifiesSafely;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    use NotifiesSafely;

    /**
     * Petugas Layanan mengunggah lalu mengirim billing kepada konsumen.
     */
    public function store(Request $request, Order $order)
    {
        if ($order->billing()->exists()) {
            return back()->with('error', 'Billing untuk pesanan ini sudah tersedia.');
        }

        // 'menunggu_pembayaran' tanpa billing hanya terjadi pada data lama
        // yang belum memiliki dokumen billing terunggah.
        if (! in_array($order->status, ['diproses', 'menunggu_pembayaran'], true)) {
            return back()->with('error', 'Billing hanya dapat dibuat untuk pesanan yang sudah diproses.');
        }

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $path = $data['file']->store('billings', 'public');

        DB::transaction(function () use ($order, $data, $path) {
            Billing::create([
                'order_id' => $order->id,
                'bill_number' => 'BILL/' . now()->format('Ymd') . '/' . strtoupper(Str::random(5)),
                'file_path' => $path,
                'amount' => $order->itemsTotal() ?: $order->total,
                'notes' => $data['notes'] ?? null,
                'uploaded_by' => Auth::id(),
                'sent_at' => now(),
            ]);

            $order->update(['status' => 'menunggu_pembayaran']);
        });

        // Notifikasi dikirim setelah transaksi selesai agar kegagalan mail
        // tidak membatalkan penerbitan billing.
        $this->notifySafely($order->user, new BillingAvailableNotification($order));

        return back()->with('success', 'Billing berhasil diunggah dan diteruskan kepada konsumen.');
    }
}