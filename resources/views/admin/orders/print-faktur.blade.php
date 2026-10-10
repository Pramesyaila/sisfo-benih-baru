@php
    $invoiceRows = $order->items;
    $grandTotal = $invoiceRows->sum(fn ($item) => (int) $item->subtotal);
    $terbilang = \App\Services\IndonesianNumberToWords::rupiah($grandTotal);
    $docLocation = $order->documentLocation();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', Times, serif; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .print-page { box-shadow: none !important; margin: 0 !important; max-width: 100% !important; }
        }
    </style>
</head>
<body>
<div class="no-print fixed top-4 right-4 z-10 flex gap-2">
    <button onclick="window.print()" class="bg-green-700 text-white px-4 py-2 rounded-md shadow-lg hover:brightness-95 text-sm font-semibold">🖨️ Cetak / Simpan PDF</button>
    <a href="javascript:history.back()" class="bg-white border px-4 py-2 rounded-md shadow-lg hover:bg-gray-50 text-sm">← Kembali</a>
</div>

<div class="print-page max-w-3xl mx-auto bg-white p-10 my-8 shadow-lg text-sm leading-relaxed text-black">
    <div class="flex justify-between items-start border-b-2 border-black pb-3">
        <div>
            <p class="font-bold uppercase text-xs">Instansi Pemerintah</p>
            <p class="text-xs mt-0.5">UPTD Benih dan Bibit</p>
        </div>
        <div class="text-right">
            <p class="font-bold uppercase">Faktur</p>
            <p class="text-xs mt-0.5">No. {{ $invoice->invoice_number }}</p>
            <p class="text-xs">{{ $invoice->created_at->format('d M Y') }}</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-6">
        <div>
            <p class="text-xs uppercase text-gray-600">Kepada Yth.</p>
            <div class="mt-1 border-b border-black pb-8 min-h-[5.5rem]">
                {{-- purposefully blank: diisi manual setelah dicetak --}}
            </div>
        </div>
        <div class="text-xs space-y-1">
            <p><span class="text-gray-600">Nomor Pesanan:</span> {{ $order->order_number }}</p>
            <p><span class="text-gray-600">Tanggal Pengambilan:</span> {{ optional($invoice->pickup_date)->format('d M Y') ?: '-' }}</p>
            <p><span class="text-gray-600">Lokasi Pengambilan:</span> {{ $invoice->pickupLocationLabel() }}</p>
        </div>
    </div>

    <table class="w-full mt-6 border-collapse">
        <thead>
            <tr class="border-y border-black">
                <th class="py-2 px-2 text-left text-xs uppercase">Banyaknya</th>
                <th class="py-2 px-2 text-left text-xs uppercase">Nama Barang</th>
                <th class="py-2 px-2 text-right text-xs uppercase">Harga Satuan</th>
                <th class="py-2 px-2 text-right text-xs uppercase">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoiceRows as $item)
                <tr class="border-b border-gray-300">
                    <td class="py-2 px-2">{{ $item->qty }} {{ $item->packaging ? '(' . $item->packaging . ')' : '' }}</td>
                    <td class="py-2 px-2">{{ $item->product_name }}</td>
                    <td class="py-2 px-2 text-right">{{ $item->formattedPrice() }}</td>
                    <td class="py-2 px-2 text-right">{{ $item->formattedSubtotal() }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-black">
                <td colspan="3" class="py-2 px-2 text-right font-bold uppercase">Total</td>
                <td class="py-2 px-2 text-right font-bold">Rp{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-6">
        <p class="text-xs"><span class="text-gray-600">Terbilang:</span> <span class="italic">{{ ucfirst($terbilang) }}</span></p>
        <div class="mt-16"></div>
    </div>

    <div class="grid grid-cols-3 gap-6 mt-4 text-center text-xs">
        <div>
            <p class="font-semibold">Tanda Terima</p>
            <div class="mt-20"></div>
            <p class="border-t border-black pt-1">(............................)</p>
        </div>
        <div>
            <p class="font-semibold">Mengetahui</p>
            <div class="mt-20"></div>
            <p class="border-t border-black pt-1">(............................)</p>
        </div>
        <div>
            <p class="font-semibold">Hormat Kami</p>
            <div class="mt-20"></div>
            <p class="border-t border-black pt-1">(............................)</p>
        </div>
    </div>

    <p class="mt-8 text-xs text-gray-700">Diterbitkan di: {{ $docLocation }}, {{ now()->format('d F Y') }}</p>
</div>
</body>
</html>