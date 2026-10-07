<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillingAvailableNotification extends Notification
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
            'title' => 'Billing pesanan tersedia',
            'message' => 'Billing untuk pesanan ' . $this->order->order_number . ' sudah dikirimkan.',
            'order_id' => $this->order->id,
            'url' => route('orders.show', $this->order),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Billing pesanan ' . $this->order->order_number . ' tersedia')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Billing untuk pesanan ' . $this->order->order_number . ' sudah kami terbitkan.')
            ->line('Total tagihan: ' . $this->order->formattedTotal())
            ->action('Lihat pesanan', route('orders.show', $this->order))
            ->line('Silakan selesaikan pembayaran sesuai instruksi pada billing.');
    }
}