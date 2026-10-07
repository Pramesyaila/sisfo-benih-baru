<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Notifications\BillingAvailableNotification;
use App\Notifications\InvoiceReadyNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\PaymentProofSubmittedNotification;
use App\Services\NotifiesSafely;
use Illuminate\Http\Request;

/**
 * Notification management for admin.
 *
 * Alur notifikasi mengikuti aktivitas pemesanan:
 *
 * 1. Konsumen membuat pesanan  -> notifikasi ke Super Admin / Petugas Layanan.
 * 2. Petugas Layanan mengunggah kode billing -> notifikasi ke email konsumen.
 * 3. Konsumen mengunggah bukti pembayaran -> notifikasi ke Petugas Layanan.
 * 4. Faktur diterbitkan -> notifikasi ke email konsumen.
 *
 * Alamat email diambil otomatis dari data akun yang didaftarkan saat registrasi.
 */
class NotificationMailController extends Controller
{
    use NotifiesSafely;

    /**
     * Daftar alamat email penerima notifikasi.
     */
    public function index()
    {
        $penerima = \App\Models\User::query()
            ->whereNotNull('email')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'is_super_admin']);

        return view('admin.notifications.index', compact('penerima'));
    }

    /**
     * Kirim ulang notifikasi billing kepada konsumen.
     */
    public function resendBilling(Request $request, Order $order)
    {
        abort_unless($order->billing, 404);

        $this->notifySafely($order->user, new BillingAvailableNotification($order));

        return back()->with('success', "Notifikasi billing telah dikirim ke email {$order->user->email}.");
    }

    /**
     * Kirim ulang notifikasi faktur kepada konsumen.
     */
    public function resendInvoice(Request $request, Order $order)
    {
        abort_unless($order->invoice, 404);

        $this->notifySafely($order->user, new InvoiceReadyNotification($order->invoice));

        return back()->with('success', "Notifikasi faktur telah dikirim ke email {$order->user->email}.");
    }
}