@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-bold">{{ $order->order_number }}</h2>
        <p class="text-sm text-gray-500">{{ $order->user->name }} &middot; {{ $order->created_at->format('d M Y H:i') }}</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.orders.print.permohonan', $order) }}" target="_blank" class="text-sm bg-white border border-gray-200 px-3 py-1.5 rounded-md hover:border-primary">🖨️ Preview Surat Permohonan</a>
        <span class="badge {{ $order->statusBadgeColor() }}">{{ \App\Models\Order::statusLabel($order->status) }}</span>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y">
            @foreach($order->items as $item)
                <div class="p-4 flex items-center gap-4">
                    <div class="flex-1">
                        <p class="font-semibold">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->qty }} {{ $item->packaging }} &times; {{ $item->formattedPrice() }}
                            @if($item->product)
                                <span class="ml-2 text-gray-400">(stok tersedia: {{ $item->product->stock }})</span>
                            @endif
                        </p>
                    </div>
                    <p class="font-semibold text-primary-dark">{{ $item->formattedSubtotal() }}</p>
                </div>
            @endforeach
            <div class="p-4 flex justify-between font-bold text-primary-dark">
                <span>Total</span><span>{{ $order->formattedTotal() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-sm text-gray-500 mb-1">Tujuan penggunaan</p>
            <p class="text-sm">{{ $order->notes ?: '-' }}</p>
            <p class="text-xs text-gray-500 mt-3">Rencana pengambilan: {{ optional($order->pickup_date)->format('d M Y') ?: '-' }} &middot; {{ $order->pickupLocationLabel() }}</p>
        </div>

        @if($order->billing)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold">Billing {{ $order->billing->bill_number }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Dikirim {{ optional($order->billing->sent_at)->format('d M Y H:i') }}</p>
                    </div>
                    <a href="{{ asset('storage/' . $order->billing->file_path) }}" target="_blank" class="text-sm text-primary-dark font-semibold hover:underline">Lihat billing →</a>
                </div>
                @if($order->billing->notes)
                    <p class="text-xs text-gray-600 mt-3">{{ $order->billing->notes }}</p>
                @endif
            </div>
        @endif

        @if($order->invoice)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold">Faktur {{ $order->invoice->invoice_number }}</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $order->invoice->formattedTotal() }}
                            &middot; diterbitkan {{ $order->invoice->created_at->format('d M Y H:i') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Pengambilan {{ optional($order->invoice->pickup_date)->format('d M Y') ?: '-' }}
                            di {{ $order->invoice->pickupLocationLabel() }}
                        </p>
                    </div>
                    <a href="{{ route('admin.orders.invoice.show', $order) }}" target="_blank" class="text-sm text-primary-dark font-semibold hover:underline">Preview / cetak faktur →</a>
                </div>
            </div>
        @endif

        @if($order->paymentProofs->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold mb-3">Bukti Pembayaran</h3>
                <ul class="text-sm divide-y">
                    @foreach($order->paymentProofs as $proof)
                        <li class="py-2 flex justify-between items-center">
                            <a href="{{ asset('storage/'.$proof->file_path) }}" target="_blank" class="text-primary-dark hover:underline">Lihat berkas ({{ $proof->created_at->format('d/m/Y H:i') }})</a>
                            <span class="badge {{ $proof->status === 'valid' ? 'bg-primary text-white' : ($proof->status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-accent-light text-primary-dark') }}">{{ ucfirst($proof->status) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($order->completionReceipt)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold mb-2">Bukti Penyelesaian</h3>
                <p class="text-xs text-gray-500 mb-3">{{ $order->completionReceipt->receipt_number }} &middot; {{ optional($order->completionReceipt->completed_at)->format('d M Y H:i') }}</p>
                <a href="{{ asset('storage/' . $order->completionReceipt->file_path) }}" target="_blank" class="text-sm text-primary-dark font-semibold hover:underline">Lihat faktur selesai →</a>
            </div>
        @endif
    </div>

    <div class="space-y-4">
        @if(auth()->user()->isPetugasLayanan())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
                <h3 class="font-semibold">Aksi Petugas Layanan</h3>

                @if($order->status === 'dipesan')
                    <form action="{{ route('admin.orders.process', $order) }}" method="POST">
                        @csrf
                        <button class="w-full bg-primary text-white py-2 rounded-md text-sm hover:bg-primary-dark">Proses Pesanan (Cek Stok)</button>
                    </form>
                    <p class="text-xs text-gray-400">Preview surat permohonan tersedia di atas sebelum diproses.</p>
                @endif

                @if($order->status === 'diproses' && !$order->billing)
                    <button type="button" class="w-full bg-accent text-primary-dark py-2 rounded-md text-sm font-semibold hover:brightness-95" data-modal-open="billing-modal">Upload &amp; Kirim Billing</button>
                    <p class="text-xs text-gray-400">Billing dikirim kepada konsumen dan pesanan menunggu pembayaran.</p>
                @endif

                @if(in_array($order->status, ['siap_diambil', 'selesai'], true) && !$order->invoice)
                    @include('partials.confirm-form', [
                        'action' => route('admin.orders.invoice.store', $order),
                        'label' => 'Terbitkan Faktur',
                        'title' => 'Terbitkan faktur',
                        'message' => 'Terbitkan faktur untuk pesanan ' . $order->order_number . '? Faktur akan langsung dikirim kepada konsumen.',
                        'confirmLabel' => 'Ya, Terbitkan',
                        'tone' => 'primary',
                        'class' => 'block',
                        'buttonClass' => 'w-full bg-primary text-white py-2 rounded-md text-sm hover:bg-primary-dark',
                    ])
                    <p class="text-xs text-gray-400">Faktur dibuat otomatis dari data pesanan dan langsung dikirim ke konsumen.</p>
                @endif

                @if($order->invoice)
                    <a href="{{ route('admin.orders.invoice.show', $order) }}" target="_blank" class="block w-full bg-base border border-gray-200 py-2 rounded-md text-sm hover:border-primary text-center">Lihat Faktur {{ $order->invoice->invoice_number }}</a>
                @endif

                @if($order->canBeCancelled())
                    @include('partials.confirm-form', [
                        'action' => route('admin.orders.cancel', $order),
                        'label' => 'Batalkan Pesanan',
                        'title' => 'Batalkan pesanan',
                        'message' => 'Batalkan pesanan ' . $order->order_number . '?',
                        'confirmLabel' => 'Ya, Batalkan',
                        'class' => 'block',
                        'buttonClass' => 'w-full bg-white border border-red-300 text-red-600 py-2 rounded-md text-sm hover:bg-red-50',
                    ])
                @endif
            </div>
        @endif

        @if(auth()->user()->isPetugasGudang() && $order->status === 'siap_diambil' && !$order->completionReceipt)
    @if (! $order->hasReleasedStock())
        <div class="bg-accent-light border border-accent text-primary-dark rounded-xl p-5">
            <h3 class="font-semibold mb-1">Belum tercatat keluar</h3>
            <p class="text-xs leading-relaxed">Catat serah terima benih terlebih dahulu di halaman Pengambilan Benih agar stok berkurang sebelum faktur selesai diunggah.</p>
        </div>
    @endif
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
                <h3 class="font-semibold">Penyelesaian Pengambilan</h3>
                <p class="text-xs text-gray-500">Upload faktur yang sudah selesai diambil untuk menutup pesanan.</p>
                <form action="{{ route('admin.warehouse.complete', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-xs">
                    <button class="w-full bg-primary text-white py-2 rounded-md text-sm hover:bg-primary-dark">Upload Faktur Selesai</button>
                </form>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold mb-3">Alur Status</h3>
            <ol class="relative border-l-2 border-accent-light pl-4 space-y-2 text-xs">
                @foreach(['dipesan','diproses','menunggu_pembayaran','menunggu_verifikasi','siap_diambil','selesai'] as $step)
                    <li class="{{ $order->status === $step ? 'text-primary-dark font-bold' : 'text-gray-400' }}">{{ \App\Models\Order::statusLabel($step) }}</li>
                @endforeach
            </ol>
        </div>
    </div>
</div>

@if(auth()->user()->isPetugasLayanan() && $order->status === 'diproses' && !$order->billing)
<dialog id="billing-modal" class="rounded-xl p-0 w-full max-w-lg">
    <form method="POST" action="{{ route('admin.orders.billing.store', $order) }}" enctype="multipart/form-data" class="p-6">
        @csrf
        <div class="flex items-start justify-between gap-3 mb-5">
            <div>
                <h3 class="text-lg font-bold text-primary-dark">Upload Billing</h3>
                <p class="text-xs text-gray-500 mt-1">Pesanan {{ $order->order_number }} akan masuk ke status menunggu pembayaran.</p>
            </div>
            <button type="button" class="text-gray-400" data-modal-close="billing-modal">✕</button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">File Billing</label>
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-sm">
                <p class="text-xs text-gray-500 mt-1">PDF/JPG/PNG, maksimal 10 MB.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" class="px-4 py-2 rounded-md border border-gray-300 text-sm" data-modal-close="billing-modal">Batal</button>
            <button class="px-4 py-2 rounded-md bg-primary text-white text-sm font-semibold hover:bg-primary-dark">Kirim Billing</button>
        </div>
    </form>
</dialog>
@endif

<script>
    document.querySelectorAll('[data-modal-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            var modal = document.getElementById(button.dataset.modalOpen);
            if (modal && typeof modal.showModal === 'function') modal.showModal();
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            var modal = document.getElementById(button.dataset.modalClose);
            if (modal && typeof modal.close === 'function') modal.close();
        });
    });
</script>
@endsection
