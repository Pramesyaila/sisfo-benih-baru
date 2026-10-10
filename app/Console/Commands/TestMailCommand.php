<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'app:test-email
                            {email? : Alamat email tujuan. Bila dikosongkan memakai MAIL_FROM_ADDRESS}';

    protected $description = 'Mengirim email uji coba untuk memeriksa konfigurasi SMTP.';

    public function handle(): int
    {
        $target = $this->argument('email') ?: config('mail.from.address');

        if (! $target) {
            $this->error('Alamat tujuan belum ditentukan.');

            return self::FAILURE;
        }

        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $user = config('mail.mailers.smtp.username');

        $this->line("Mailer  : " . config('mail.default'));
        $this->line("Host   : {$host}:{$port}");
        $this->line("Pengirim: " . config('mail.from.address') . ' (' . config('mail.from.name') . ')');
        $this->line("Tujuan : {$target}");
        $this->newLine();

        // 1. Pastikan password sudah diisi.
        if (! config('mail.mailers.smtp.password')) {
            $this->error('MAIL_PASSWORD belum diisi di berkas .env.');
            $this->line('Isi dengan <fg=yellow>App Password</> Gmail (16 karakter), bukan password akun.');
            $this->line('Buat di: https://myaccount.google.com/apppasswords');
            $this->newLine();
            $this->line('Setelah diisi, jalankan: <info>php artisan config:clear</info> lalu ulangi perintah ini.');

            return self::FAILURE;
        }

        // 2. Pastikan server SMTP dapat dihubungi.
        $socket = @fsockopen($host, (int) $port, $errno, $errstr, 10);

        if (! $socket) {
            $this->error("Tidak dapat terhubung ke {$host}:{$port} ({$errstr}).");

            return self::FAILURE;
        }

        fclose($socket);
        $this->info('Koneksi ke server SMTP berhasil.');
        $this->newLine();

        // 3. Coba kirim email sungguhan.
        try {
            Mail::raw(
                "Halo,\n\nEmail ini adalah pesan uji coba dari " . config('app.name') . ".\n"
                . "Jika Anda menerima pesan ini, konfigurasi email sudah benar.\n\n"
                . 'Dikirim: ' . now()->translatedFormat('d F Y H:i'),
                function ($message) use ($target) {
                    $message->to($target)
                        ->subject('[' . config('app.name') . '] Uji coba email');
                }
            );
        } catch (\Throwable $e) {
            $this->error('Pengiriman gagal: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info("Email uji coba berhasil dikirim ke {$target}.");
        $this->line('Silakan periksa folder inbox dan folder spam.');

        return self::SUCCESS;
    }
}