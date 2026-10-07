@extends('layouts.print')
@section('title', 'Surat Permohonan - ' . $order->order_number)

@section('content')
<p>Yth.<br>
Kepala Balai Besar Pengembangan dan Penerapan Modernisasi Pertanian<br>
di<br>
Tempat</p>

<p class="mt-4"><strong>Perihal: Permohonan Benih/Bibit/Produk Hasil Pertanian</strong></p>

<p class="mt-4">Dengan hormat,<br>
Saya yang bertanda tangan di bawah ini:</p>

<table class="mt-3 w-full">
    <tr><td class="w-56 align-top py-0.5">Nama</td><td class="align-top py-0.5">: {{ $order->user->name }}</td></tr>
    <tr><td class="align-top py-0.5">Nomor KTP</td><td class="align-top py-0.5">: {{ $order->user->nik ?? '............................' }}</td></tr>
    <tr><td class="align-top py-0.5">Instansi/Kelompok Tani</td><td class="align-top py-0.5">: ............................</td></tr>
    <tr><td class="align-top py-0.5">Alamat</td><td class="align-top py-0.5">: {{ $order->user->alamat ?? '............................' }}</td></tr>
    <tr><td class="align-top py-0.5">Kelurahan/Desa</td><td class="align-top py-0.5">: ............................</td></tr>
    <tr><td class="align-top py-0.5">Kecamatan</td><td class="align-top py-0.5">: ............................</td></tr>
    <tr><td class="align-top py-0.5">Kabupaten/Kota</td><td class="align-top py-0.5">: {{ $order->user->domisili ?? '............................' }}</td></tr>
    <tr><td class="align-top py-0.5">Provinsi</td><td class="align-top py-0.5">: ............................</td></tr>
    <tr><td class="align-top py-0.5">No. HP/WA</td><td class="align-top py-0.5">: {{ $order->user->phone ?? '............................' }}</td></tr>
</table>

<p class="mt-4">Dengan ini mengajukan permohonan benih/bibit/produk hasil pertanian dengan rincian sebagai berikut:</p>

<table class="mt-3 w-full border-collapse border border-black text-xs">
    <thead>
        <tr class="text-center">
            <th class="border border-black p-1 w-8">No.</th>
            <th class="border border-black p-1">Jenis Produk</th>
            <th class="border border-black p-1">Komoditas</th>
            <th class="border border-black p-1">Varietas dan Kelas Benih/Galur</th>
            <th class="border border-black p-1">Jumlah</th>
            <th class="border border-black p-1">Satuan</th>
            <th class="border border-black p-1">Harga (Rp)</th>
            <th class="border border-black p-1">Jumlah Harga (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $i => $item)
        <tr class="text-center">
            <td class="border border-black p-1">{{ $i + 1 }}</td>
            <td class="border border-black p-1 text-left">{{ $item->product_name }}</td>
            <td class="border border-black p-1">{{ $item->product->category->name ?? '-' }}</td>
            <td class="border border-black p-1">{{ $item->packaging ?? '-' }}</td>
            <td class="border border-black p-1">{{ $item->qty }}</td>
            <td class="border border-black p-1">{{ $item->product->packaging_unit ?? '-' }}</td>
            <td class="border border-black p-1 text-right">{{ number_format($item->price,0,',','.') }}</td>
            <td class="border border-black p-1 text-right">{{ number_format($item->subtotal,0,',','.') }}</td>
        </tr>
        @endforeach
        <tr class="font-bold">
            <td colspan="7" class="border border-black p-1 text-right">Total</td>
            <td class="border border-black p-1 text-right">{{ number_format($order->total,0,',','.') }}</td>
        </tr>
    </tbody>
</table>

<table class="mt-4 w-full">
    <tr><td class="w-64 align-top py-0.5">Tujuan penggunaan</td><td class="align-top py-0.5">: {{ $order->notes }}</td></tr>
    <tr><td class="align-top py-0.5">Tgl rencana pengambilan/pengiriman</td><td class="align-top py-0.5">: {{ optional($order->pickup_date)->format('d F Y') ?? '-' }}</td></tr>
    <tr><td class="align-top py-0.5">Lokasi pengambilan/pengiriman</td><td class="align-top py-0.5">: {{ $order->pickupLocationLabel() }}</td></tr>
</table>

<p class="mt-4">Penyiapan dan penyerahan benih/bibit/produk hasil pertanian akan diproses setelah pembayaran dilakukan melalui kode billing sesuai ketentuan yang berlaku.</p>
<p>Demikian permohonan ini disampaikan. Atas perhatiannya diucapkan terima kasih.</p>

{{-- Lokasi diambil dari Kabupaten/Kota pemohon pada data profil. --}}
<div class="flex justify-end mt-10">
    <div class="text-center">
        <p>{{ $order->documentLocation() }}, {{ $order->created_at->translatedFormat('d F Y') }}</p>
        <p>Pemohon,</p>
        <div class="h-20"></div>
        <p class="font-semibold">{{ $order->user->name }}</p>
    </div>
</div>
@endsection