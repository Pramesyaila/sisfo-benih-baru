<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Daftar notifikasi untuk pengguna yang sedang login.
     *
     * Notifikasi digabung berdasarkan id pesanan sehingga satu pesanan dihitung
     * satu notifikasi, berapa pun jumlah produk atau jumlah pembaruan di dalamnya.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $groups = $user->notifications()
            ->latest()
            ->limit(100)
            ->get()
            ->groupBy(fn ($notification) => $notification->data['order_id'] ?? $notification->id);

        return view('notifications.index', [
            'groups' => $groups,
        ]);
    }

    public function read(Request $request, string $notification)
    {
        $notificationModel = $request->user()->notifications()->findOrFail($notification);
        $notificationModel->markAsRead();

        $data = $notificationModel->data;
        $orderId = $data['order_id'] ?? null;

        if ($orderId && $request->user()->isKonsumen()) {
            return redirect()->route('orders.show', $orderId);
        }

        if ($orderId) {
            return redirect()->route('admin.orders.show', $orderId);
        }

        return redirect()->back();
    }

    /**
     * Tandai semua notifikasi milik satu order sebagai dibaca.
     */
    public function readOrder(Request $request, int $orderId)
    {
        $request->user()->unreadNotifications()
            ->where('data->order_id', $orderId)
            ->get()
            ->each->markAsRead();

        return back();
    }
}