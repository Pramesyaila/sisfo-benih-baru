<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'order_id', 'invoice_number', 'total', 'pickup_date', 'pickup_location', 'recipient', 'issued_by',
    ];

    protected $casts = [
        'pickup_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function formattedTotal(): string
    {
        return 'Rp' . number_format($this->total, 0, ',', '.');
    }

    public function pickupLocationLabel(): string
    {
        return match ($this->pickup_location) {
            'brmp_penerapan' => 'BRMP Penerapan',
            'ip2mp_cipaku' => 'IP2MP Cipaku',
            'dikirim' => 'Dikirim ke alamat pemohon',
            default => '-',
        };
    }
}