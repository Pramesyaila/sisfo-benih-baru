@extends('layouts.admin')

@section('title', 'Verifikasi Bukti Pembayaran')

@section('content')
<div class="flex items-center justify-between mb-4 gap-3">
    <p class="text-sm text-gray-500">
        Verifikasi bukti pembayaran. Setelah pembayaran valid, faktur dapat diterbitkan dan dikirim kepada konsumen.
    </p>
    <a href="{{ route('admin.reports.sales') }}" class="bg-base border border-gray-200 px-4 py-2 rounded-md text-sm hover:border-primary shrink-0">Lihat Laporan</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <select name="status" class="rounded-md border-gray-300 px-3 py-2 border text-sm" onchange="this.form.submit()">
        <option value="menunggu" @selected(request('status', 'menunggu') === 'menunggu')>Menunggu Verifikasi</option>
        <option value="valid" @selected(request('status') === 'valid')>Valid</option>
        <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
    </select>
</form>

<div class="grid gap-4">
    @forelse($proofs as $proof)
        @php $order = $proof->order; @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col md:flex-row md:items-center gap-4">
            <div class="flex-1">
                <p class="font-semibold">
                    {{ $order->order_number }}
                    <span class="text-gray-300">&middot;</span>
                    {{ $order->user->name }}
                </p>
                <p class="text-xs text-gray-500">
                    Total {{ $order->formattedTotal() }}
                    @if ($order->invoice)
                        &middot; Faktur {{ $order->invoice->invoice_number }}
                    @endif
                </p>
                <p class="text-xs text-gray-500">Diunggah {{ $proof->created_at->format('d M Y H:i') }}</p>
                <a href="{{ asset('storage/' . $proof->file_path) }}" target="_blank" class="text-primary-dark text-sm hover:underline">Lihat berkas bukti bayar &rarr;</a>
            </div>

            <span class="badge {{ $proof->status === 'valid' ? 'bg-primary text-white' : ($proof->status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-accent-light text-primary-dark') }}">
                {{ ucfirst($proof->status) }}
            </span>

            @if ($proof->status === 'menunggu')
                <div class="flex gap-2">
                    <form action="{{ route('admin.paymentProofs.verify', $proof) }}" method="POST">
                        @csrf
                        <input type="hidden" name="decision" value="valid">
                        <button class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark">Valid</button>
                    </form>
                    <form action="{{ route('admin.paymentProofs.verify', $proof) }}" method="POST">
                        @csrf
                        <input type="hidden" name="decision" value="ditolak">
                        <button class="bg-white border border-red-300 text-red-600 px-4 py-2 rounded-md text-sm hover:bg-red-50">Tolak</button>
                    </form>
                </div>
            @elseif ($proof->status === 'valid' && ! $order->invoice)
                <a href="{{ route('admin.orders.show', $order) }}" class="bg-accent text-primary-dark px-4 py-2 rounded-md text-sm font-semibold hover:brightness-95 shrink-0">
                    Terbitkan Faktur
                </a>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-xl p-10 text-center text-gray-400 shadow-sm">Tidak ada data.</div>
    @endforelse
</div>
<div class="mt-4">{{ $proofs->links() }}</div>
@endsection