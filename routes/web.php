<?php

use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LandingContentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\NotificationMailController;
use App\Http\Controllers\Admin\PaymentProofController;
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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman utama & Auth
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
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
});

/*
|--------------------------------------------------------------------------
| Sisi Konsumen
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

    // Konsumen mengunduh/cetak faktur yang telah diterbitkan petugas.
    Route::get('/pesanan-saya/{order}/faktur', [OrderController::class, 'invoice'])
        ->name('orders.invoice');
});

/*
|--------------------------------------------------------------------------
| Sisi Petugas / Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:petugas_layanan,petugas_gudang'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Petugas Layanan: administrasi, billing, faktur, pembayaran, akun, konten.
        Route::middleware('role:petugas_layanan')->group(function () {
            Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::post('/pesanan/{order}/proses', [AdminOrderController::class, 'process'])->name('orders.process');
            Route::post('/pesanan/{order}/batal', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
            Route::post('/pesanan/{order}/billing', [BillingController::class, 'store'])->name('orders.billing.store');
            Route::post('/pesanan/{order}/faktur', [InvoiceController::class, 'store'])->name('orders.invoice.store');
            Route::get('/pesanan/{order}/cetak/permohonan', [AdminOrderController::class, 'printPermohonan'])->name('orders.print.permohonan');

            Route::get('/bukti-pembayaran', [PaymentProofController::class, 'index'])->name('paymentProofs.index');
            Route::post('/bukti-pembayaran/{paymentProof}/verifikasi', [PaymentProofController::class, 'verify'])->name('paymentProofs.verify');

            Route::get('/landing-page', [LandingContentController::class, 'edit'])->name('landing.edit');
            Route::put('/landing-page', [LandingContentController::class, 'update'])->name('landing.update');

            Route::get('/notifikasi', [NotificationMailController::class, 'index'])->name('notifications.index');
            Route::post('/notifikasi/billing/{order}', [NotificationMailController::class, 'resendBilling'])->name('notifications.resendBilling');
            Route::post('/notifikasi/faktur/{order}', [NotificationMailController::class, 'resendInvoice'])->name('notifications.resendInvoice');
        });

        // Kelola Admin hanya untuk Super Admin.
        Route::middleware('role:petugas_layanan,superadmin')->group(function () {
            Route::resource('admins', AdminUserController::class)->except(['show']);
        });

        // Detail dokumen, pesanan, dan laporan dapat dibuka kedua petugas.
        Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/pesanan/{order}/cetak/faktur', [InvoiceController::class, 'show'])->name('orders.invoice.show');

        // Petugas Gudang: produk, kategori/varietas, stok, dan pengambilan.
        Route::middleware('role:petugas_gudang')->group(function () {
            Route::resource('categories', CategoryController::class)->except(['show', 'create']);
            Route::post('/categories/{category}/subcategories', [CategoryController::class, 'storeSubcategory'])->name('subcategories.store');
            Route::patch('/subcategories/{subcategory}', [CategoryController::class, 'updateSubcategory'])->name('subcategories.update');
            Route::delete('/subcategories/{subcategory}', [CategoryController::class, 'destroySubcategory'])->name('subcategories.destroy');

            Route::resource('products', ProductController::class)->except(['show']);

            Route::get('/stok', [StockController::class, 'index'])->name('stock.index');
            Route::get('/stok/riwayat', [StockController::class, 'history'])->name('stock.history');
            Route::post('/stok/{product}/masuk', [StockController::class, 'storeIn'])->name('stock.in');
            Route::post('/stok/{product}/keluar', [StockController::class, 'storeOut'])->name('stock.out');

            Route::get('/gudang/serah-terima', [WarehouseController::class, 'index'])->name('warehouse.index');
            Route::post('/gudang/serah-terima/{order}', [WarehouseController::class, 'release'])->name('warehouse.release');
            Route::post('/gudang/serah-terima/{order}/selesai', [WarehouseController::class, 'complete'])->name('warehouse.complete');
        });

        Route::get('/laporan/stok', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/laporan/penjualan', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/laporan/penjualan/cetak', [ReportController::class, 'printSales'])->name('reports.sales.print');
    });