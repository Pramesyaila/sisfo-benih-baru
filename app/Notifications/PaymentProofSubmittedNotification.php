<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Dikirim kepada Petugas Layanan ketika satu bukti pembayaran baru masuk.
 *
 * Satu bukti pembayaran = satu notifikasi ke tiap penerima, bukan satu per
 * item pesanan. Email penerima diambil dari data akun petugas.
 */
class PaymentProofSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public $proofId)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Bukti pembayaran masuk',
            'message' => 'Pesanan ' . $this->order->order_number . ' dari '
                . $this->order->user->name . ' menunggu verifikasi.',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'url' => route('admin.paymentProofs.index'),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bukti pembayaran baru ' . $this->order->order_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Konsumen telah mengunggah bukti pembayaran dan menunggu verifikasi.')
            ->line('Nomor pesanan: ' . $this->order->order_number)
            ->line('Konsumen: ' . $this->order->user->name)
            ->line('Total tagihan: ' . $this->order->formattedTotal())
            ->action('Verifikasi pembayaran', route('admin.paymentProofs.index'))
            ->line('Silakan periksa bukti pembayaran tersebut.');
    }
}