<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Mengirim notifikasi tanpa membiarkan kegagalan mail menjatuhkan proses bisnis.
 *
 * Notifikasi selalu tersimpan di channel database sehingga tetap tampil di
 * aplikasi meskipun server email tidak dapat dihubungi. Bila channel mail
 * gagal, kegagalannya hanya dicatat ke log dan transaksi bisnis tetap berhasil.
 */
trait NotifiesSafely
{
    /**
     * Kirim notifikasi ke satu pengguna.
     */
    protected function notifySafely(?User $recipient, object $notification): void
    {
        // Akun tanpa email (mis. data lama) dilewati agar tidak error.
        if (! $recipient || blank($recipient->email)) {
            return;
        }

        $this->notifyManySafely([$recipient], $notification);
    }

    /**
     * Kirim notifikasi yang sama ke banyak pengguna.
     *
     * @param  iterable<User>  $recipients
     */
    protected function notifyManySafely(iterable $recipients, object $notification): void
    {
        foreach ($recipients as $recipient) {
            try {
                $recipient->notify($notification);
            } catch (Throwable $e) {
                // Kegagalan mail tidak boleh menggagalkan transaksi bisnis.
                Log::warning('Gagal mengirim notifikasi: ' . $e->getMessage(), [
                    'notification' => $notification::class,
                    'recipient' => $recipient->id,
                ]);
            }
        }
    }

    /**
     * Penerima notifikasi administrasi: Super Admin didahulukan, disusul
     * Petugas Layanan lain.
     *
     * Hanya akun aktif yang memiliki alamat email dari data registrasi.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    protected function petugasPenerimaNotifikasi()
    {
        return User::query()
            ->where('role', User::ROLE_PETUGAS_LAYANAN)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderByDesc('is_super_admin')
            ->orderBy('id')
            ->get();
    }
}