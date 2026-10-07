@php
    use App\Services\IndonesianNumberToWords;
    use App\Models\Order;

    $reportDate = now()->translatedFormat('d F Y');
    $periodText = \Carbon\Carbon::parse($from)->translatedFormat('d M Y') . ' s.d. '
        . \Carbon\Carbon::parse($to)->translatedFormat('d M Y');
    $printedBy = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan &amp; Distribusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; }
        .report-page { max-width: 100%; padding: 1.5cm; background: #fff; }

        .report-header {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .report-title { font-size: 15px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .report-subtitle { font-size: 12px; margin-top: 2px; }

        .report-meta { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; font-size: 11px; }
        .report-meta dt { font-weight: 700; }
        .report-meta dd { margin: 0 0 2px; }

        .report-summary { width: 100%; border-collapse: collapse; margin: 12px 0 18px; font-size: 12px; }
        .report-summary th, .report-summary td { border: 1px solid #000; padding: 6px 10px; }
        .report-summary th { background: #F1F5F9; text-align: left; }
        .report-summary td.value { text-align: right; font-weight: 700; }

        .report-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .report-table thead th {
            border: 1px solid #000;
            background: #F1F5F9;
            padding: 7px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .report-table tbody td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        .report-table tbody tr:nth-child(even) td { background: #FAFAFA; }
        .report-table .num { text-align: right; white-space: nowrap; }
        .report-table tfoot td { border: 1px solid #000; padding: 7px 6px; font-weight: 700; background: #F8FAFC; }

        .report-signature { margin-top: 28px; display: flex; justify-content: flex-end; }
        .report-signature__box { width: 260px; text-align: center; font-size: 11px; }

        @page { size: A4 landscape; margin: 1cm; }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; background: #fff; }
            .report-page { box-shadow: none !important; margin: 0 !important; max-width: none !important; padding: 0 !important; }
            .report-table { page-break-inside: auto; }
            .report-table tr { page-break-inside: avoid; page-break-after: auto; }
            .report-table thead { display: table-header-group; }
            .report-signature { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
<div class="no-print fixed top-4 right-4 z-10 flex gap-2">
    <button onclick="window.print()" class="bg-green-700 text-white px-4 py-2 rounded-md shadow-lg text-sm font-semibold">🖨️ Cetak Laporan</button>
    <a href="javascript:history.back()" class="bg-white border px-4 py-2 rounded-md shadow-lg text-sm">← Kembali</a>
</div>

<div class="report-page mx-auto my-8 shadow-lg">
    <div class="report-header flex items-start justify-between gap-6">
        <div>
            <p class="report-title">Laporan Penjualan &amp; Distribusi</p>
            <p class="report-subtitle">Sistem Informasi Pengelolaan dan Penjualan Benih/Bibit</p>
        </div>
        <div class="text-right">
            <p class="report-subtitle">Tanggal cetak: {{ $reportDate }}</p>
            <p class="report-subtitle">Dicetak oleh: {{ $printedBy?->name ?? '-' }}</p>
        </div>
    </div>

    <dl class="report-meta">
        <div>
            <dt>Periode laporan</dt>
            <dd>{{ $periodText }}</dd>
            @if (request('status'))
                <dt>Status pesanan</dt>
                <dd>{{ \App\Models\Order::statusLabel(request('status')) }}</dd>
            @endif
        </div>
        <div class="text-right">
            <dt>Jumlah data</dt>
            <dd>{{ $orders->count() }} pesanan</dd>
        </div>
    </dl>

    <table class="report-summary">
        <thead>
            <tr>
                <th colspan="2">Ringkasan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nilai penjualan (pembayaran terverifikasi &amp; selesai)</td>
                <td class="value">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total produk terjual</td>
                <td class="value">{{ number_format($totalProdukTerjual, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 14%">No. Transaksi</th>
                <th style="width: 16%">Nama Konsumen</th>
                <th style="width: 26%">Produk</th>
                <th style="width: 8%" class="num">Jumlah</th>
                <th style="width: 14%" class="num">Harga</th>
                <th style="width: 11%">Tanggal</th>
                <th style="width: 11%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td>
                        {{ $order->order_number }}
                        @if ($order->invoice)
                            <br><span style="font-size: 9px">{{ $order->invoice->invoice_number }}</span>
                        @elseif ($order->billing)
                            <br><span style="font-size: 9px">{{ $order->billing->bill_number }}</span>
                        @endif
                    </td>
                    <td>{{ $order->user->name }}</td>
                    <td>
                        @foreach ($order->items as $item)
                            <div>{{ $item->product_name }}</div>
                        @endforeach
                    </td>
                    <td class="num">{{ $order->totalQuantity() }}</td>
                    <td class="num">{{ $order->formattedTotal() }}</td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>{{ \App\Models\Order::statusLabel($order->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 18px;">Tidak ada data pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right">Jumlah</td>
                <td class="num">{{ number_format($totalProdukTerjual, 0, ',', '.') }}</td>
                <td class="num">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="report-signature">
        <div class="report-signature__box">
            <p>{{ $printedBy?->locationLabel() ?? 'Bogor' }}, {{ now()->format('d F Y') }}</p>
            <p style="font-weight:700">Kepala Unit</p>
            <div style="height: 70px"></div>
            <p style="border-top:1px solid #000; padding-top:4px">(............................)</p>
        </div>
    </div>
</div>
</body>
</html>