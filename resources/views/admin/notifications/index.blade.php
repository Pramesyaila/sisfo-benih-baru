@extends('layouts.admin')

@section('title', 'Notifikasi Email')

@section('content')
<div class="w-full min-w-0">
    <p class="text-sm text-gray-500 mb-4">
        Notifikasi dikirim ke alamat email yang didaftarkan masing-masing akun pada saat registrasi.
        Alur lengkap notifikasi dapat dilihat pada tabel di bawah ini.
    </p>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <h2 class="font-semibold text-primary-dark mb-3">Alur notifikasi</h2>
        <ol class="space-y-3 text-sm">
            @foreach ([
                ['Konsumen membuat pesanan', 'Notifikasi dikirim ke email Super Admin dan Petugas Layanan.'],
                ['Petugas Layanan mengunggah kode billing', 'Notifikasi dikirim ke email konsumen, dilengkapi tautan untuk melihat pesanan.'],
                ['Konsumen mengunggah bukti pembayaran', 'Notifikasi dikirim ke email Petugas Layanan untuk diverifikasi.'],
                ['Faktur diterbitkan', 'Notifikasi dikirim ke email konsumen dengan tautan unduh faktur.'],
            ] as $index => $step)
                <li class="flex gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $step[0] }}</p>
                        <p class="text-gray-500">{{ $step[1] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-base text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($penerima as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $user->isSuperAdmin() ? 'bg-primary text-white' : 'bg-accent-light text-primary-dark' }}">
                                {{ $user->roleDisplayLabel() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada akun terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-primary-dark mb-2">Konfigurasi email</h2>
        <p class="text-sm text-gray-600 leading-relaxed">
            Pengiriman email mengikuti pengaturan pada berkas <code class="text-xs bg-base px-1 py-0.5 rounded">.env</code>.
            Bila server email tidak dapat dihubungi, notifikasi tetap tersimpan di dalam aplikasi
            dan proses bisnis tetap berjalan normal.
        </p>
        <div class="mt-3 text-xs text-gray-500 space-y-1">
            <p><span class="font-semibold">MAIL_MAILER</span> &mdash; jenis mailer yang dipakai, misalnya <code>smtp</code>.</p>
            <p><span class="font-semibold">MAIL_HOST</span> dan <span class="font-semibold">MAIL_PORT</span> &mdash; alamat server email.</p>
            <p><span class="font-semibold">MAIL_FROM_ADDRESS</span> &mdash; alamat pengirim yang tampil pada email.</p>
        </div>
    </div>
</div>
@endsection
