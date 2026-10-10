<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $layanan;

    protected User $gudang;

    protected User $konsumen;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $padi = Category::create(['name' => 'Padi', 'slug' => 'padi']);
        $sawah = Category::create([
            'name' => 'Padi Sawah',
            'slug' => 'padi-sawah',
            'parent_id' => $padi->id,
        ]);

        $this->product = Product::create([
            'category_id' => $sawah->id,
            'name' => 'Padi Inpari 32',
            'slug' => 'padi-inpari-32',
            'packaging_unit' => 'bungkus',
            'packaging_size' => '5 kg',
            'price' => 50000,
            'stock' => 10,
            'min_stock' => 2,
            'status' => 'aktif',
        ]);

        // Akun ini berperan sebagai Super Admin agar dapat mengakses Kelola Admin.
        $this->layanan = User::factory()->create([
            'role' => User::ROLE_PETUGAS_LAYANAN,
            'is_super_admin' => true,
        ]);
        $this->gudang = User::factory()->create(['role' => User::ROLE_PETUGAS_GUDANG]);
        $this->konsumen = User::factory()->create(['role' => User::ROLE_KONSUMEN]);
    }

    protected function makeOrder(string $status = 'dipesan'): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => $this->konsumen->id,
            'status' => $status,
            'total' => 50000,
            'notes' => 'Permohonan benih untuk lahan 1 hectare.',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'qty' => 1,
            'price' => 50000,
            'packaging' => '5 kg / bungkus',
            'subtotal' => 50000,
        ]);

        return $order;
    }

    public function test_petugas_layanan_processes_order_then_uploads_billing_and_notifies_customer(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.process', $order))
            ->assertRedirect();

        $this->assertSame('diproses', $order->fresh()->status);

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.billing.store', $order), [
                'file' => UploadedFile::fake()->create('billing.pdf', 20, 'application/pdf'),
                'notes' => 'Transfer ke rekening instansi.',
            ])
            ->assertRedirect();

        $order->refresh();

        $this->assertSame('menunggu_pembayaran', $order->status);
        $this->assertNotNull($order->billing);
        $this->assertSame($order->total, $order->billing->amount);
        $this->assertNotNull($order->billing->sent_at);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->konsumen->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_billing_cannot_be_uploaded_twice(): void
    {
        $order = $this->makeOrder('diproses');

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.billing.store', $order), [
                'file' => UploadedFile::fake()->create('billing.pdf', 20, 'application/pdf'),
            ]);

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.billing.store', $order), [
                'file' => UploadedFile::fake()->create('billing-duplikat.pdf', 20, 'application/pdf'),
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, Billing::where('order_id', $order->id)->count());
    }

    public function test_order_can_be_cancelled_before_billing_but_not_after(): void
    {
        $order = $this->makeOrder('diproses');

        $this->assertTrue($order->canBeCancelled());

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.cancel', $order))
            ->assertRedirect();

        $this->assertSame('dibatalkan', $order->fresh()->status);

        $second = $this->makeOrder('diproses');

        Billing::create([
            'order_id' => $second->id,
            'bill_number' => 'BILL-TEST-1',
            'file_path' => 'billings/test.pdf',
            'amount' => 50000,
            'sent_at' => now(),
        ]);

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.cancel', $second))
            ->assertSessionHas('error');

        $this->assertSame('diproses', $second->fresh()->status);
    }

    public function test_consumer_cannot_upload_payment_proof_without_billing(): void
    {
        $order = $this->makeOrder('menunggu_pembayaran');

        $this->actingAs($this->konsumen)
            ->post(route('orders.uploadProof', $order), [
                'file' => UploadedFile::fake()->create('bukti.jpg'),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('payment_proofs', 0);
    }

    public function test_payment_verification_marks_order_ready_for_pickup(): void
    {
        $order = $this->makeOrder('menunggu_verifikasi');

        $proof = $order->paymentProofs()->create([
            'file_path' => 'payment-proofs/bukti.jpg',
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->layanan)
            ->post(route('admin.paymentProofs.verify', $proof), ['decision' => 'valid'])
            ->assertRedirect();

        $this->assertSame('siap_diambil', $order->fresh()->status);
    }

    public function test_warehouse_release_reduces_stock_once_and_completion_closes_order(): void
    {
        $order = $this->makeOrder('siap_diambil');
        $stockBefore = $this->product->stock;

        $this->actingAs($this->gudang)
            ->post(route('admin.warehouse.release', $order))
            ->assertRedirect();

        $this->assertSame($stockBefore - 1, $this->product->fresh()->stock);
        $this->assertTrue($order->fresh()->hasReleasedStock());

        // Pelepasan kedua ditolak agar stok tidak berkurang dua kali.
        $this->actingAs($this->gudang)
            ->post(route('admin.warehouse.release', $order))
            ->assertSessionHas('error');

        $this->assertSame($stockBefore - 1, $this->product->fresh()->stock);

        $this->actingAs($this->gudang)
            ->post(route('admin.warehouse.complete', $order), [
                'receipt' => UploadedFile::fake()->create('faktur.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertSame('selesai', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->taken_at);
        $this->assertDatabaseHas('completion_receipts', ['order_id' => $order->id]);
    }

    public function test_completion_is_blocked_until_stock_is_released(): void
    {
        $order = $this->makeOrder('siap_diambil');

        $this->actingAs($this->gudang)
            ->post(route('admin.warehouse.complete', $order), [
                'receipt' => UploadedFile::fake()->create('faktur.pdf', 20, 'application/pdf'),
            ])
            ->assertSessionHas('error');

        $this->assertSame('siap_diambil', $order->fresh()->status);
        $this->assertDatabaseCount('completion_receipts', 0);
    }

    public function test_roles_are_separated(): void
    {
        // Petugas Gudang tidak boleh memproses pesanan.
        $this->actingAs($this->gudang)
            ->get(route('admin.orders.index'))
            ->assertForbidden();

        // Konsumen tidak boleh menyentuh area admin.
        $this->actingAs($this->konsumen)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        // Petugas Layanan tidak boleh mengelola produk, stok, dan pengambilan.
        $this->actingAs($this->layanan)
            ->get(route('admin.products.index'))
            ->assertForbidden();

        $this->actingAs($this->layanan)
            ->get(route('admin.stock.index'))
            ->assertForbidden();

        $this->actingAs($this->layanan)
            ->get(route('admin.warehouse.index'))
            ->assertForbidden();

        // Petugas Gudang boleh mengelola produk dan stok.
        $this->actingAs($this->gudang)
            ->get(route('admin.products.index'))
            ->assertOk();

        $this->actingAs($this->gudang)
            ->get(route('admin.stock.index'))
            ->assertOk();

        // Petugas Layanan sebagai Super Admin mengelola admin dan landing page.
        $this->actingAs($this->layanan)
            ->get(route('admin.admins.index'))
            ->assertOk();

        $this->actingAs($this->layanan)
            ->get(route('admin.landing.edit'))
            ->assertOk();

        // Petugas Gudang tidak boleh mengelola admin.
        $this->actingAs($this->gudang)
            ->get(route('admin.admins.index'))
            ->assertForbidden();
    }

    public function test_consumer_cannot_view_another_consumers_order(): void
    {
        $order = $this->makeOrder();
        $other = User::factory()->create(['role' => User::ROLE_KONSUMEN]);

        $this->actingAs($other)
            ->get(route('orders.show', $order))
            ->assertForbidden();
    }

    public function test_stock_transaction_requires_description(): void
    {
        $this->actingAs($this->gudang)
            ->post(route('admin.stock.in', $this->product), ['qty' => 5])
            ->assertSessionHasErrors('note');

        $this->actingAs($this->gudang)
            ->post(route('admin.stock.in', $this->product), [
                'qty' => 5,
                'note' => 'Penambahan stok produksi 2026.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(15, $this->product->fresh()->stock);
        $this->assertDatabaseHas('stock_transactions', [
            'product_id' => $this->product->id,
            'type' => 'masuk',
            'qty' => 5,
            'note' => 'Penambahan stok produksi 2026.',
        ]);
    }

    public function test_stock_out_cannot_exceed_available_stock(): void
    {
        $this->actingAs($this->gudang)
            ->post(route('admin.stock.out', $this->product), [
                'qty' => 999,
                'note' => 'Pengujian batas stok.',
            ])
            ->assertSessionHas('error');

        $this->assertSame(10, $this->product->fresh()->stock);
    }

    public function test_admin_account_only_accepts_internal_roles(): void
    {
        // Role konsumen ditolak.
        $this->actingAs($this->layanan)
            ->post(route('admin.admins.store'), [
                'name' => 'Petugas Baru',
                'email' => 'baru@benih.test',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => 'konsumen',
                'super_admin_password' => 'password',
            ])
            ->assertSessionHasErrors('role');

        // Role petugas gudang diterima dengan konfirmasi sandi Super Admin.
        $this->actingAs($this->layanan)
            ->post(route('admin.admins.store'), [
                'name' => 'Petugas Gudang Baru',
                'email' => 'gudang.baru@benih.test',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => User::ROLE_PETUGAS_GUDANG,
                'super_admin_password' => 'password',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'gudang.baru@benih.test',
            'role' => User::ROLE_PETUGAS_GUDANG,
        ]);
    }

    public function test_creating_officer_requires_super_admin_password(): void
    {
        $this->actingAs($this->layanan)
            ->post(route('admin.admins.store'), [
                'name' => 'Petugas Tanpa Konfirmasi',
                'email' => 'tanpa-sandi@benih.test',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => User::ROLE_PETUGAS_GUDANG,
                'super_admin_password' => 'salah-total',
            ])
            ->assertSessionHasErrors('super_admin_password');

        $this->assertDatabaseMissing('users', ['email' => 'tanpa-sandi@benih.test']);
    }

    public function test_only_super_admin_can_access_kelola_admin(): void
    {
        $layananBiasa = User::factory()->create([
            'role' => User::ROLE_PETUGAS_LAYANAN,
            'is_super_admin' => false,
        ]);

        // Petugas Layanan biasa tidak boleh membuka Kelola Admin.
        $this->actingAs($layananBiasa)
            ->get(route('admin.admins.index'))
            ->assertForbidden();

        $this->actingAs($layananBiasa)
            ->get(route('admin.admins.create'))
            ->assertForbidden();

        // Super Admin boleh.
        $this->actingAs($this->layanan)
            ->get(route('admin.admins.index'))
            ->assertOk();
    }

    public function test_super_admin_account_cannot_be_deleted(): void
    {
        $this->actingAs($this->layanan)
            ->delete(route('admin.admins.destroy', $this->layanan), [
                'super_admin_password' => 'password',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $this->layanan->id]);
    }
    public function test_petugas_layanan_cannot_delete_own_account(): void
    {
        // Aksi hapus memerlukan konfirmasi kata sandi petugas yang sedang login.
        $this->actingAs($this->layanan)
            ->delete(route('admin.admins.destroy', $this->layanan), [
                'super_admin_password' => 'password',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $this->layanan->id]);
    }

    public function test_deleting_officer_requires_correct_password(): void
    {
        $gudang = $this->gudang;

        // Tanpa kata sandi: ditolak.
        $this->actingAs($this->layanan)
            ->delete(route('admin.admins.destroy', $gudang), ['super_admin_password' => 'salah-total'])
            ->assertSessionHasErrors('super_admin_password');

        $this->assertDatabaseHas('users', ['id' => $gudang->id]);

        // Dengan kata sandi yang benar: berhasil.
        $this->actingAs($this->layanan)
            ->delete(route('admin.admins.destroy', $gudang), ['super_admin_password' => 'password'])
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $gudang->id]);
    }

    public function test_updating_officer_requires_correct_password(): void
    {
        $gudang = $this->gudang;

        $this->actingAs($this->layanan)
            ->put(route('admin.admins.update', $gudang), [
                'name' => 'Nama Berubah',
                'email' => $gudang->email,
                'role' => User::ROLE_PETUGAS_GUDANG,
                'super_admin_password' => 'salah',
            ])
            ->assertSessionHasErrors('super_admin_password');

        $this->assertDatabaseHas('users', ['id' => $gudang->id, 'name' => $gudang->name]);

        $this->actingAs($this->layanan)
            ->put(route('admin.admins.update', $gudang), [
                'name' => 'Nama Berubah',
                'email' => $gudang->email,
                'role' => User::ROLE_PETUGAS_GUDANG,
                'is_active' => '1',
                'super_admin_password' => 'password',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $gudang->id, 'name' => 'Nama Berubah']);
    }

    public function test_petugas_layanan_can_edit_landing_content(): void
    {
        $this->actingAs($this->layanan)
            ->put(route('admin.landing.update'), [
                'hero_title' => 'Benih berkualitas untuk petani Indonesia.',
                'hero_subtitle' => 'Pilih varietas, pesan, dan pantau prosesnya.',
                'announcement' => 'Pengambilan libur pada hari nasional.',
            ])
            ->assertRedirect();

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Pengambilan libur pada hari nasional.');
    }

    public function test_landing_content_is_stored_as_a_single_row(): void
    {
        $this->get(route('catalog.index'))->assertOk();
        $this->get(route('catalog.index'))->assertOk();

        $this->actingAs($this->layanan)
            ->put(route('admin.landing.update'), [
                'hero_title' => 'Benih berkualitas untuk petani Indonesia.',
                'announcement' => 'Pengambilan libur pada hari nasional.',
            ])
            ->assertRedirect();

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Pengambilan libur pada hari nasional.');

        $this->assertSame(1, \DB::table('landing_contents')->count());
    }

    public function test_legacy_pnbp_and_contract_tables_are_no_longer_active(): void
    {
        // Kontrak dan tagihan PNBP diarsipkan, bukan dipakai lagi.
        $this->assertFalse(Schema::hasTable('contracts'));
        $this->assertFalse(Schema::hasTable('pnbp_bills'));
        $this->assertTrue(Schema::hasTable('archived_contracts'));
        $this->assertTrue(Schema::hasTable('archived_pnbp_bills'));
        $this->assertFalse(Schema::hasColumn('payment_proofs', 'pnbp_bill_id'));
    }

    public function test_catalog_filters_by_category_then_variety(): void
    {
        $hortikultura = Category::create(['name' => 'Hortikultura', 'slug' => 'hortikultura']);
        $sayuran = Category::create([
            'name' => 'Sayuran',
            'slug' => 'sayuran',
            'parent_id' => $hortikultura->id,
        ]);

        Product::create([
            'category_id' => $sayuran->id,
            'name' => 'Benih Cabai Rawit',
            'slug' => 'benih-cabai-rawit',
            'packaging_unit' => 'sachet',
            'price' => 15000,
            'stock' => 5,
            'status' => 'aktif',
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Benih Cabai Rawit');

        $this->get(route('catalog.index', ['category' => 'hortikultura']))
            ->assertOk()
            ->assertSee('Benih Cabai Rawit');

        $this->get(route('catalog.index', ['category' => 'hortikultura', 'variety' => 'sayuran']))
            ->assertOk()
            ->assertSee('Benih Cabai Rawit');

        // Varietas Sayuran tidak boleh berada di bawah kategori Padi.
        $this->get(route('catalog.index', ['category' => 'padi', 'variety' => 'sayuran']))
            ->assertOk()
            ->assertDontSee('Benih Cabai Rawit');
    }
}



