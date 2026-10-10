<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Cetak Dokumen')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .print-page { box-shadow: none !important; margin: 0 !important; max-width: 100% !important; }
        }
        body { font-family: 'Times New Roman', Times, serif; background: #e5e7eb; }
    </style>
</head>
<body>
    <div class="no-print fixed top-4 right-4 z-10">
        <button onclick="window.print()" class="bg-green-700 text-white px-4 py-2 rounded-md shadow-lg hover:brightness-95 text-sm font-semibold">🖨️ Cetak / Simpan sebagai PDF</button>
        <a href="javascript:history.back()" class="ml-2 bg-white border px-4 py-2 rounded-md shadow-lg hover:bg-gray-50 text-sm">← Kembali</a>
    </div>

    <div class="print-page max-w-3xl mx-auto bg-white p-10 my-8 shadow-lg text-sm leading-relaxed">
        @yield('content')
    </div>
</body>
</html>