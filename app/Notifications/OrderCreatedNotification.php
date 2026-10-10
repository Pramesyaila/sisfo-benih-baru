<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Dikirim kepada Petugas Layanan ketika satu pesanan baru masuk.
 *
 * Satu id pesanan hanya menghasilkan satu notifikasi ke tiap penerima,
 * berapa pun jumlah produk di dalamnya.
 */
class OrderCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Pesanan baru masuk',
            'message' => 'Pesanan ' . $this->order->order_number . ' dari '
                . $this->order->user->name . ' menunggu diproses.',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'url' => route('admin.orders.show', $this->order),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pesanan baru ' . $this->order->order_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Ada pesanan baru yang menunggu diproses.')
            ->line('Nomor pesanan: ' . $this->order->order_number)
            ->line('Konsumen: ' . $this->order->user->name)
            ->line('Total: ' . $this->order->formattedTotal())
            ->action('Proses pesanan', route('admin.orders.show', $this->order))
            ->line('Silakan periksa dan proses pesanan tersebut.');
    }
}