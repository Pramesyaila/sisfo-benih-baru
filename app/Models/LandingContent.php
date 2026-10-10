<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingContent extends Model
{
    protected $fillable = [
        'key', 'logo', 'hero_title', 'hero_subtitle', 'hero_image', 'announcement', 'updated_by',
    ];

    public const SINGLETON_KEY = 'default';

    /**
     * Konten landing page selalu berupa satu baris, dijamin unik oleh kolom 'key'.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['key' => self::SINGLETON_KEY],
            [
                'hero_title' => 'Benih & bibit pilihan untuk langkah tumbuh berikutnya.',
                'hero_subtitle' => 'Temukan produk, cek ketersediaan, lalu ajukan pesanan dengan proses yang jelas.',
                'announcement' => null,
            ]
        );
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}