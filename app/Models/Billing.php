<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'order_id', 'bill_number', 'file_path', 'amount', 'notes', 'uploaded_by', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function formattedAmount(): string
    {
        return 'Rp' . number_format($this->amount, 0, ',', '.');
    }
}
