@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Distribusi')

@section('content')
<form method="GET" class="mb-4 flex flex-wrap gap-2 items-end bg-white p-4 rounded-xl border border-gray-100 print:hidden" id="report-filter">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Dari</label>
        <input type="date" name="from" value="{{ $from }}" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Sampai</label>
        <input type="date" name="to" value="{{ $to }}" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
            <option value="">Semua</option>
            @foreach(['dipesan','diproses','menunggu_pembayaran','menunggu_verifikasi','pembayaran_ditolak','siap_diambil','selesai','dibatalkan'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ \App\Models\Order::statusLabel($s) }}</option>
            @endforeach
        </select>
    </div>
    <button class="bg-primary text-white px-4 py-2 rounded-md text-sm hover:bg-primary-dark">Tampilkan</button>
    <button type="button" data-print-report class="bg-accent text-primary-dark px-4 py-2 rounded-md text-sm font-semibold hover:brightness-95">🖨️ Cetak Laporan</button>
</form>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div class="bg-white rounded-xl p-4 border">
        <p class="text-xs text-gray-500">Nilai Penjualan (terverifikasi &amp; selesai)</p>
        <p class="text-xl font-bold text-primary-dark">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border">
        <p class="text-xs text-gray-500">Total Produk Terjual</p>
        <p class="text-xl font-bold text-primary-dark">{{ number_format($totalProdukTerjual, 0, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-base text-gray-500 text-left">
            <tr>
                <th class="px-4 py-3">No.</th>
                <th class="px-4 py-3">No. Transaksi</th>
                <th class="px-4 py-3">Nama Konsumen</th>
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3 text-right">Jumlah</th>
                <th class="px-4 py-3 text-right">Harga</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $index => $order)
                <tr>
                    <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $order->order_number }}</p>
                        @if ($order->invoice)
                            <a href="{{ route('admin.orders.invoice.show', $order) }}" target="_blank" class="text-xs text-primary-dark hover:underline">
                                {{ $order->invoice->invoice_number }}
                            </a>
                        @elseif ($order->billing)
                            <span class="text-xs text-gray-500">{{ $order->billing->bill_number }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $order->user->name }}</td>
                    <td class="px-4 py-3 text-gray-500">
                        @forelse ($order->items as $item)
                            <span class="block">{{ $item->product_name }}</span>
                        @empty
                            <span>-</span>
                        @endforelse
                    </td>
                    <td class="px-4 py-3 text-right">{{ $order->totalQuantity() }}</td>
                    <td class="px-4 py-3 text-right">{{ $order->formattedTotal() }}</td>
                    <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $order->statusBadgeColor() }}">{{ \App\Models\Order::statusLabel($order->status) }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-base font-semibold">
                <td colspan="4" class="px-4 py-3 text-right">Jumlah</td>
                <td class="px-4 py-3 text-right">{{ number_format($totalProdukTerjual, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    // Tombol cetak membuka tampilan laporan khusus, bukan sekadar
    // mencetak halaman berfilter yang memuat sidebar dan tombol.
    (function () {
        var form = document.getElementById('report-filter');
        var buttons = document.querySelectorAll('[data-print-report]');

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var params = new URLSearchParams(new FormData(form));
                window.open(
                    "{{ route('admin.reports.sales.print') }}?" + params.toString(),
                    '_blank'
                );
            });
        });
    })();
</script>
@endsection