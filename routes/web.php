<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentProofController;
use App\Http\Controllers\Admin\PnbpController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman utama & Auth (satu halaman login untuk semua role)
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/katalog');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| SISI KONSUMEN (seperti e-commerce)
|--------------------------------------------------------------------------
*/
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware(['auth', 'role:konsumen'])->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{product}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan-saya/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan-saya/{order}/bukti-bayar', [OrderController::class, 'uploadProof'])->name('orders.uploadProof');
});

/*
|--------------------------------------------------------------------------
| SISI ADMIN / PENGELOLA (dashboard) - petugas layanan, PNBP, manager gudang
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:petugas_layanan,petugas_pnbp,manager_gudang'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kelola Katalog (Manager/Gudang)
    Route::middleware('role:manager_gudang')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
        Route::resource('products', ProductController::class)->except(['show']);

        Route::get('/stok', [StockController::class, 'index'])->name('stock.index');
        Route::get('/stok/riwayat', [StockController::class, 'history'])->name('stock.history');
        Route::post('/stok/{product}/masuk', [StockController::class, 'storeIn'])->name('stock.in');
        Route::post('/stok/{product}/keluar', [StockController::class, 'storeOut'])->name('stock.out');

        Route::get('/gudang/serah-terima', [WarehouseController::class, 'index'])->name('warehouse.index');
        Route::post('/gudang/serah-terima/{order}', [WarehouseController::class, 'release'])->name('warehouse.release');
    });

    // Pesanan (Petugas Layanan)
    Route::middleware('role:petugas_layanan')->group(function () {
        Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::post('/pesanan/{order}/proses', [AdminOrderController::class, 'process'])->name('orders.process');
        Route::post('/pesanan/{order}/kontrak', [AdminOrderController::class, 'generateContract'])->name('orders.contract');
        Route::post('/pesanan/{order}/tagihan', [AdminOrderController::class, 'generateBill'])->name('orders.bill');
        Route::post('/pesanan/{order}/selesai', [AdminOrderController::class, 'markTaken'])->name('orders.markTaken');
        Route::post('/pesanan/{order}/batal', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

        Route::get('/bukti-pembayaran', [PaymentProofController::class, 'index'])->name('paymentProofs.index');
        Route::post('/bukti-pembayaran/{paymentProof}/verifikasi', [PaymentProofController::class, 'verify'])->name('paymentProofs.verify');
    });

    // Bisa dilihat lintas role (detail pesanan)
    Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

    // PNBP (Petugas PNBP)
    Route::middleware('role:petugas_pnbp,petugas_layanan')->group(function () {
        Route::get('/pnbp', [PnbpController::class, 'index'])->name('pnbp.index');
    });

    // Laporan - semua role admin boleh melihat laporan
    Route::get('/laporan/stok', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/laporan/penjualan', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/laporan/distribusi', [ReportController::class, 'distribution'])->name('reports.distribution');
    Route::get('/laporan/pnbp', [ReportController::class, 'pnbp'])->name('reports.pnbp');
});
