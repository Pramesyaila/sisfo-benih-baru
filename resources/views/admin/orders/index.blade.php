@extends('layouts.admin')

@section('title', 'Kelola Pesanan')

@section('content')
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. pesanan / konsumen..." class="rounded-md border-gray-300 px-3 py-2 border text-sm">
    <select name="status" class="rounded-md border-gray-300 px-3 py-2 border text-sm">
        <option value="">Semua Status</option>
        @foreach(['dipesan','diproses','menunggu_pembayaran','menunggu_verifikasi','pembayaran_ditolak','siap_diambil','selesai','dibatalkan'] as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ \App\Models\Order::statusLabel($s) }}</option>
        @endforeach
    </select>
    <button class="bg-base border border-gray-200 px-3 py-2 rounded-md text-sm hover:border-primary">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-base text-gray-500 text-left">
            <tr><th class="px-4 py-3">No. Pesanan</th><th class="px-4 py-3">Konsumen</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $order)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                <td class="px-4 py-3">{{ $order->user->name }}</td>
                <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3">{{ $order->formattedTotal() }}</td>
                <td class="px-4 py-3"><span class="badge {{ $order->statusBadgeColor() }}">{{ \App\Models\Order::statusLabel($order->status) }}</span></td>
                <td class="px-4 py-3 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="text-primary-dark hover:underline">Kelola</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
