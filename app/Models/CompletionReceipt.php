<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletionReceipt extends Model
{
    protected $fillable = [
        'order_id', 'receipt_number', 'file_path', 'uploaded_by', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
