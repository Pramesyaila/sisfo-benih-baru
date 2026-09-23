<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'nik', 'domisili', 'alamat', 'profile_photo', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ---------- Role helpers ----------
    public function isKonsumen(): bool
    {
        return $this->role === 'konsumen';
    }

    public function isAdminArea(): bool
    {
        return in_array($this->role, ['petugas_layanan', 'petugas_pnbp', 'manager_gudang']);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'konsumen' => 'Konsumen',
            'petugas_layanan' => 'Petugas Layanan',
            'petugas_pnbp' => 'Petugas Pengelola PNBP',
            'manager_gudang' => 'Manager / Petugas Gudang',
            default => $this->role,
        };
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
