<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceReadyNotification extends Notification
{
    use Queueable;

    public function __construct(public Invoice $invoice)
    {
    }

    public function order(): Order
    {
        return $this->invoice->order;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Faktur pesanan tersedia',
            'message' => 'Faktur ' . $this->invoice->invoice_number . ' untuk pesanan '
                . $this->invoice->order->order_number . ' sudah diterbitkan.',
            'order_id' => $this->invoice->order_id,
            'url' => route('orders.show', $this->invoice->order),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Faktur ' . $this->invoice->invoice_number . ' tersedia')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Faktur untuk pesanan ' . $this->invoice->order->order_number . ' sudah diterbitkan.')
            ->line('Nomor faktur: ' . $this->invoice->invoice_number)
            ->line('Total: ' . $this->invoice->formattedTotal())
            ->action('Unduh faktur', route('orders.show', $this->invoice->order))
            ->line('Silakan unduh faktur melalui halaman pesanan Anda.');
    }
}