<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_KONSUMEN = 'konsumen';
    public const ROLE_PETUGAS_LAYANAN = 'petugas_layanan';
    public const ROLE_PETUGAS_GUDANG = 'petugas_gudang';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_super_admin', 'phone', 'whatsapp', 'nik', 'instansi',
        'alamat', 'kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi', 'domisili',
        'profile_photo', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function isKonsumen(): bool
    {
        return $this->role === self::ROLE_KONSUMEN;
    }

    public function isPetugasLayanan(): bool
    {
        return $this->role === self::ROLE_PETUGAS_LAYANAN;
    }

    public function isPetugasGudang(): bool
    {
        return $this->role === self::ROLE_PETUGAS_GUDANG;
    }

    /**
     * Super admin adalah akun bawaan sistem yang menjadi satu-satunya pihak
     * berwenang mengelola fitur Kelola Admin.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isPetugas(): bool
    {
        return $this->isAdminArea();
    }

    public function isAdminArea(): bool
    {
        return $this->isPetugasLayanan() || $this->isPetugasGudang();
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_KONSUMEN => 'Konsumen',
            self::ROLE_PETUGAS_LAYANAN => 'Petugas Layanan',
            self::ROLE_PETUGAS_GUDANG => 'Petugas Gudang',
            default => $this->role,
        };
    }

    /**
     * Label peran yang ditampilkan di antarmuka, membedakan super admin.
     */
    public function roleDisplayLabel(): string
    {
        return $this->isSuperAdmin() ? 'Super Admin' : $this->roleLabel();
    }

    /**
     @return array<int, array<string, string>>
     */
    public static function addressFieldDefinitions(): array
    {
        return [
            ['name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'placeholder' => 'Contoh: Desa Sukamaju'],
            ['name' => 'kecamatan', 'label' => 'Kecamatan', 'placeholder' => 'Contoh: Bogor Timur'],
            ['name' => 'kabupaten_kota', 'label' => 'Kabupaten/Kota', 'placeholder' => 'Contoh: Kabupaten Bogor'],
            ['name' => 'provinsi', 'label' => 'Provinsi', 'placeholder' => 'Contoh: Jawa Barat'],
        ];
    }

    /**
     * Ringkasan data registrasi untuk ditampilkan pada halaman profil.
     *
     * @return array<string, array{label: string, value: string|null}>
     */
    public function registrationSummary(): array
    {
        return [
            'Nama lengkap' => $this->name,
            'Email' => $this->email,
            'Nomor KTP' => $this->nik,
            'Instansi / Kelompok tani' => $this->instansi,
            'Nomor HP' => $this->phone,
            'Nomor WhatsApp' => $this->whatsapp,
            'Alamat' => $this->alamat,
            'Kelurahan/Desa' => $this->kelurahan,
            'Kecamatan' => $this->kecamatan,
            'Kabupaten/Kota' => $this->kabupaten_kota,
            'Provinsi' => $this->provinsi,
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function processedOrders()
    {
        return $this->hasMany(Order::class, 'processed_by');
    }

    public function uploadedBillings()
    {
        return $this->hasMany(Billing::class, 'uploaded_by');
    }

    public function uploadedCompletionReceipts()
    {
        return $this->hasMany(CompletionReceipt::class, 'uploaded_by');
    }

    public function issuedInvoices()
    {
        return $this->hasMany(Invoice::class, 'issued_by');
    }

    /**
     * Ringkasan alamat untuk dipakai pada surat permohonan, faktur, dan profil.
     */
    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->kelurahan ? 'Kelurahan/Desa ' . $this->kelurahan : null,
            $this->kecamatan ? 'Kecamatan ' . $this->kecamatan : null,
            $this->kabupaten_kota ? 'Kabupaten/Kota ' . $this->kabupaten_kota : null,
            $this->provinsi ? 'Provinsi ' . $this->provinsi : null,
        ]);

        return $parts ? implode(', ', $parts) : '-';
    }

    /**
     * Lokasi singkat berdasarkan kabupaten/kota, dipakai pada kop dan tanda tangan dokumen.
     */
    public function locationLabel(): string
    {
        return $this->kabupaten_kota ?: ($this->domisili ?: '-');
    }

    public function contactLabel(): string
    {
        return $this->phone ?: '-';
    }

    public function whatsappLabel(): string
    {
        return $this->whatsapp ?: ($this->phone ?: '-');
    }
}
