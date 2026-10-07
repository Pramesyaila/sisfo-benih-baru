<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Notifications\PaymentProofSubmittedNotification;
use App\Services\NotifiesSafely;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use NotifiesSafely;

    public function index()
    {
        $orders = Order::with('billing')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorizeOwner($order);

        $order->load(['items', 'billing', 'invoice.issuer', 'paymentProofs.verifiedBy', 'completionReceipt.uploader']);

        return view('customer.orders.show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        $this->authorizeOwner($order);

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        if (! $order->billing || ! in_array($order->status, ['menunggu_pembayaran', 'pembayaran_ditolak'], true)) {
            return back()->with('error', 'Pesanan ini belum bisa menerima bukti pembayaran.');
        }

        $path = $request->file('file')->store('payment-proofs', 'public');

        $proof = DB::transaction(function () use ($order, $path) {
            $proof = PaymentProof::create([
                'order_id' => $order->id,
                'file_path' => $path,
                'status' => 'menunggu',
            ]);

            $order->update(['status' => 'menunggu_verifikasi']);

            return $proof;
        });

        $this->notifyPetugasLayanan($order, $proof->id);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah, menunggu verifikasi petugas.');
    }

    /**
     * Faktur yang telah diterbitkan petugas, diunduh/dicetak oleh konsumen.
     */
    public function invoice(Order $order)
    {
        $this->authorizeOwner($order);

        abort_unless($order->invoice, 404);

        $order->load(['user', 'items', 'invoice.issuer']);

        return view('admin.orders.print-faktur', [
            'order' => $order,
            'invoice' => $order->invoice,
        ]);
    }

    /**
     * Satu bukti pembayaran menghasilkan satu notifikasi untuk tiap Petugas Layanan,
     * bukan satu per item pesanan.
     */
    protected function notifyPetugasLayanan(Order $order, $proofId): void
    {
        $order->loadMissing('user');

        // Bukti pembayaran diteruskan ke Super Admin dan Petugas Layanan
        // melalui email yang didaftarkan pada masing-masing akun.
        $this->notifyManySafely(
            $this->petugasPenerimaNotifikasi(),
            new PaymentProofSubmittedNotification($order, $proofId)
        );
    }

    protected function authorizeOwner(Order $order): void
    {
        abort_unless($order->user_id === Auth::id(), 403);
    }
}