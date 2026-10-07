@extends('layouts.admin')

@section('title', 'Pengambilan Benih (Gudang)')

@section('content')
<p class="text-sm text-gray-500 mb-4">
    Pesanan di bawah sudah lunas dan siap diserahkan. Catat stok keluar saat menyerahkan benih,
    lalu unggah faktur selesai untuk menutup pesanan menjadi status Selesai.
</p>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-base text-gray-500 text-left">
            <tr>
                <th class="px-4 py-3">No. Pesanan</th>
                <th class="px-4 py-3">Konsumen</th>
                <th class="px-4 py-3">Billing</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $order)
                @php
                    $released = $order->hasReleasedStock();
                    $hasReceipt = $order->completionReceipt()->exists();
                @endphp
                <tr>
                    <td class="px-4 py-3 font-medium">
                        {{ $order->order_number }}
                        <div class="mt-1">
                            <span class="badge {{ $order->statusBadgeColor() }}">{{ \App\Models\Order::statusLabel($order->status) }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $order->user->name }}</td>
                    <td class="px-4 py-3">
                        @if ($order->billing)
                            <a href="{{ asset('storage/' . $order->billing->file_path) }}" target="_blank" class="text-primary-dark hover:underline">{{ $order->billing->bill_number }}</a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $order->formattedTotal() }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-dark hover:underline">Detail</a>

                        @if (! $released)
                            @include('partials.confirm-form', [
                                'action' => route('admin.warehouse.release', $order),
                                'label' => 'Serahkan Benih',
                                'title' => 'Serahkan benih',
                                'message' => 'Serahkan benih untuk pesanan ' . $order->order_number . ' dan kurangi stok?',
                                'confirmLabel' => 'Ya, Serahkan',
                                'tone' => 'primary',
                            ])
                        @else
                            <span class="text-xs text-gray-400">Stok sudah keluar</span>
                        @endif

                        @if (! $hasReceipt)
                            <form action="{{ route('admin.warehouse.complete', $order) }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  class="inline-flex items-center gap-1 mt-2"
                                  data-confirm-title="Tandai pesanan selesai"
                                  data-confirm-message="Unggah faktur selesai untuk pesanan {{ $order->order_number }} dan tandai pesanan sebagai selesai?"
                                  data-confirm-button="Ya, Selesaikan"
                                  data-confirm-tone="primary">
                                @csrf
                                <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" required class="text-xs max-w-[8rem]">
                                <button class="bg-accent text-primary-dark px-3 py-1.5 rounded-md text-xs font-semibold hover:brightness-95">Faktur Selesai</button>
                            </form>
                        @else
                            <div class="mt-1">
                                <span class="text-xs text-primary-dark font-semibold">Faktur selesai diunggah</span>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada pesanan yang menunggu pengambilan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection