<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $data = [
            'user' => $user,
            'pendingOrdersCount' => Order::where('status', 'dipesan')->count(),
            'pendingPaymentsCount' => PaymentProof::where('status', 'menunggu')->count(),
        ];

        if ($user->isPetugasLayanan()) {
            $data['stats'] = [
                'Pesanan Baru' => $data['pendingOrdersCount'],
                'Sedang Diproses' => Order::where('status', 'diproses')->count(),
                'Menunggu Billing' => Order::where('status', 'diproses')
                    ->whereDoesntHave('billing')
                    ->count(),
                'Verifikasi Pembayaran' => $data['pendingPaymentsCount'],
                'Siap Diambil' => Order::where('status', 'siap_diambil')->count(),
                'Selesai' => Order::where('status', 'selesai')->count(),
            ];

            $data['recentOrders'] = Order::with('user')->latest()->limit(8)->get();

            $data['ordersWaitingBilling'] = Order::with('user')
                ->where('status', 'diproses')
                ->whereDoesntHave('billing')
                ->latest()
                ->limit(8)
                ->get();
        } else {
            $data['stats'] = [
                'Total Produk' => Product::count(),
                'Stok Tersedia' => Product::sum('stock'),
                'Stok Menipis' => Product::whereColumn('stock', '<=', 'min_stock')->count(),
                'Siap Diambil' => Order::where('status', 'siap_diambil')->count(),
                'Selesai Bulan Ini' => Order::where('status', 'selesai')
                    ->whereDate('taken_at', now()->startOfMonth())
                    ->count(),
                'Stok Masuk Hari Ini' => StockTransaction::where('type', 'masuk')
                    ->whereDate('created_at', today())
                    ->sum('qty'),
            ];

            $data['lowStockProducts'] = Product::whereColumn('stock', '<=', 'min_stock')->limit(8)->get();
        }

        return view('admin.dashboard.index', $data);
    }
}