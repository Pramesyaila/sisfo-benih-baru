<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\BillingAvailableNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\PaymentProofSubmittedNotification;
use App\Services\NotifiesSafely;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminAndAlertsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $layanan;

    protected User $gudang;

    protected User $konsumen;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $kategori = Category::create(['name' => 'Padi', 'slug' => 'padi']);
        $sawah = Category::create([
            'name' => 'Padi Sawah',
            'slug' => 'padi-sawah',
            'parent_id' => $kategori->id,
        ]);

        $this->product = Product::create([
            'category_id' => $sawah->id,
            'name' => 'Padi Inpari 32',
            'slug' => 'padi-inpari-32',
            'packaging_unit' => 'bungkus',
            'price' => 50000,
            'stock' => 20,
            'status' => 'aktif',
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => User::ROLE_PETUGAS_LAYANAN,
            'is_super_admin' => true,
            'email' => 'superadmin@benih.test',
        ]);

        $this->layanan = User::factory()->create([
            'role' => User::ROLE_PETUGAS_LAYANAN,
            'is_super_admin' => false,
            'email' => 'layanan@benih.test',
        ]);

        $this->gudang = User::factory()->create([
            'role' => User::ROLE_PETUGAS_GUDANG,
            'email' => 'gudang@benih.test',
        ]);

        $this->konsumen = User::factory()->create([
            'role' => User::ROLE_KONSUMEN,
            'email' => 'konsumen@benih.test',
        ]);
    }

    protected function makeOrder(string $status = 'dipesan'): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'user_id' => $this->konsumen->id,
            'status' => $status,
            'total' => 50000,
            'notes' => 'Kebutuhan tanam.',
            'pickup_date' => now()->addDays(3)->toDateString(),
            'pickup_location' => 'brmp_penerapan',
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

    // 4. Super admin bawaan dan pembatasan Kelola Admin.
    public function test_super_admin_is_the_only_account_allowed_on_kelola_admin(): void
    {
        // Petugas Layanan biasa dan Petugas Gudang ditolak.
        $this->actingAs($this->layanan)->get(route('admin.admins.index'))->assertForbidden();
        $this->actingAs($this->gudang)->get(route('admin.admins.index'))->assertForbidden();
        $this->actingAs($this->konsumen)->get(route('admin.admins.index'))->assertForbidden();

        // Super Admin boleh.
        $this->actingAs($this->superAdmin)->get(route('admin.admins.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('admin.admins.create'))->assertOk();
    }

    public function test_kelola_admin_link_only_visible_for_super_admin(): void
    {
        $superPage = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'))->getContent();
        $this->assertStringContainsString('Kelola Admin', $superPage);

        $biasaPage = $this->actingAs($this->layanan)->get(route('admin.dashboard'))->getContent();
        $this->assertStringNotContainsString('Kelola Admin', $biasaPage);
    }

    public function test_every_kelola_admin_action_requires_super_admin_password(): void
    {
        $payload = [
            'name' => 'Petugas Baru',
            'email' => 'baru@benih.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => User::ROLE_PETUGAS_GUDANG,
        ];

        // Tanpa kata sandi.
        $this->actingAs($this->superAdmin)
            ->post(route('admin.admins.store'), $payload)
            ->assertSessionHasErrors('super_admin_password');

        // Kata sandi salah.
        $this->actingAs($this->superAdmin)
            ->post(route('admin.admins.store'), $payload + ['super_admin_password' => 'salah'])
            ->assertSessionHasErrors('super_admin_password');

        // Kata sandi benar.
        $this->actingAs($this->superAdmin)
            ->post(route('admin.admins.store'), $payload + ['super_admin_password' => 'password'])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'baru@benih.test']);
    }

    public function test_super_admin_seed_marks_exactly_one_account(): void
    {
        $this->artisan('db:seed', ['--class' => 'UserSeeder', '--force' => true])->assertExitCode(0);

        $superAdmins = User::where('is_super_admin', true)->get();

        $this->assertCount(1, $superAdmins);
        $this->assertSame('superadmin@benih.test', $superAdmins->first()->email);
    }

    // 1. Alur notifikasi email.
    public function test_billing_upload_sends_email_notification_to_consumer(): void
    {
        Notification::fake();

        $order = $this->makeOrder('diproses');

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.billing.store', $order), [
                'file' => UploadedFile::fake()->create('billing.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect();

        Notification::assertSentTo(
            $this->konsumen,
            BillingAvailableNotification::class,
            function ($notification) use ($order) {
                return $notification->order->is($order)
                    && in_array('mail', $notification->via($this->konsumen), true);
            }
        );

        // Alamat email diambil dari akun yang didaftarkan.
        $this->assertSame('konsumen@benih.test', $this->konsumen->email);
    }

    public function test_new_order_notifies_officers_by_email(): void
    {
        Notification::fake();

        $order = $this->makeOrder();

        $this->actingAs($this->konsumen)
            ->withSession(['cart' => [$this->product->id => 1]])
            ->post(route('checkout.store'), [
                'notes' => 'Kebutuhan musim hujan.',
                'pickup_date' => now()->addWeek()->toDateString(),
                'pickup_location' => 'brmp_penerapan',
            ]);

        // Super Admin dan Petugas Layanan sama-sama diberi tahu.
        Notification::assertSentTo($this->superAdmin, OrderCreatedNotification::class);
        Notification::assertSentTo($this->layanan, OrderCreatedNotification::class);

        // Satu id pesanan = satu notifikasi per penerima.
        Notification::assertSentToTimes($this->superAdmin, OrderCreatedNotification::class, 1);
    }

    public function test_payment_proof_upload_notifies_officers(): void
    {
        Notification::fake();

        $order = $this->makeOrder('menunggu_pembayaran');

        Billing::create([
            'order_id' => $order->id,
            'bill_number' => 'BILL/TEST/100',
            'file_path' => 'billings/test.pdf',
            'amount' => 50000,
            'sent_at' => now(),
        ]);

        $this->actingAs($this->konsumen)
            ->post(route('orders.uploadProof', $order), [
                'file' => UploadedFile::fake()->create('bukti.jpg'),
            ])
            ->assertRedirect();

        Notification::assertSentTo($this->superAdmin, PaymentProofSubmittedNotification::class);
        Notification::assertSentTo($this->layanan, PaymentProofSubmittedNotification::class);
    }

    // 2. Profil menampilkan data registrasi.
    public function test_consumer_profile_shows_registration_data(): void
    {
        $this->konsumen->update([
            'nik' => '3273000000000001',
            'instansi' => 'Kelompok Tani Sejahtera',
            'alamat' => 'Jl. Raya No. 1',
            'kelurahan' => 'Sukajadi',
            'kecamatan' => 'Bogor Selatan',
            'kabupaten_kota' => 'Kota Bogor',
            'provinsi' => 'Jawa Barat',
            'whatsapp' => '081234567890',
        ]);

        $response = $this->actingAs($this->konsumen)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSee('3273000000000001');
        $response->assertSee('Kelompok Tani Sejahtera');
        $response->assertSee('Jl. Raya No. 1');
        $response->assertSee('Sukajadi');
        $response->assertSee('Bogor Selatan');
        $response->assertSee('Kota Bogor');
        $response->assertSee('Jawa Barat');
        $response->assertSee('081234567890');
    }

    public function test_officer_profile_shows_registration_data(): void
    {
        $this->gudang->update([
            'nik' => '3209999999990001',
            'instansi' => 'UPTD Benih',
            'kabupaten_kota' => 'Kabupaten Bogor',
            'provinsi' => 'Jawa Barat',
        ]);

        $response = $this->actingAs($this->gudang)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSee('3209999999990001');
        $response->assertSee('UPTD Benih');
        $response->assertSee('Kabupaten Bogor');
        $response->assertSee('Jawa Barat');
    }

    // 3. Billing tanpa preview.
    public function test_billing_preview_route_is_removed(): void
    {
        $this->assertNull(
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.orders.billing.preview')
        );
    }

    // 5. Alert konsisten.
    public function test_admin_and_customer_use_shared_alert_components(): void
    {
        $adminPage = $this->actingAs($this->layanan)
            ->withSession(['success' => 'Data berhasil disimpan.'])
            ->get(route('admin.orders.index'))
            ->getContent();

        $customerPage = $this->actingAs($this->konsumen)
            ->withSession(['success' => 'Data berhasil disimpan.'])
            ->get(route('catalog.index'))
            ->getContent();

        // Keduanya merender elemen alert dari komponen masing-masing.
        $this->assertStringContainsString('class="alert alert--', $adminPage);
        $this->assertStringContainsString('class="flash flash--', $customerPage);

        // Komponen alert memakai penanda aksesibilitas yang sama.
        $this->assertStringContainsString('role="status"', $adminPage);
        $this->assertStringContainsString('role="status"', $customerPage);
    }

    public function test_flash_message_renders_consistent_alert(): void
    {
        $response = $this->actingAs($this->konsumen)
            ->withSession(['success' => 'Berhasil disimpan.'])
            ->get(route('catalog.index'));

        $response->assertOk();
        $response->assertSee('flash flash--success');
        $response->assertSee('Berhasil disimpan.');
    }

    public function test_error_message_renders_consistent_alert(): void
    {
        $response = $this->actingAs($this->layanan)
            ->withSession(['error' => 'Gagal diproses.'])
            ->get(route('admin.orders.index'));

        $response->assertOk();
        $response->assertSee('alert alert--error');
        $response->assertSee('Gagal diproses.');
    }

    public function test_validation_errors_are_shown_in_indonesian(): void
    {
        $response = $this->actingAs($this->konsumen)
            ->withSession(['cart' => [$this->product->id => 1]])
            ->post(route('checkout.store'), []);

        // Pesan validasi memakai bahasa Indonesia, bukan bawaan Laravel.
        $sessionErrors = session('errors')->getBag('default')->all();

        $this->assertNotEmpty($sessionErrors);
        foreach ($sessionErrors as $error) {
            $this->assertMatchesRegularExpression('/[a-z]/', $error);
            $this->assertStringNotContainsString('field is required', $error);
            $this->assertStringNotContainsString('must be', $error);
        }
    }

    // 7. Laporan cetak.
    public function test_printable_sales_report_page_exists(): void
    {
        $this->makeOrder('siap_diambil');

        $response = $this->actingAs($this->layanan)
            ->get(route('admin.reports.sales.print'));

        $response->assertOk();
        $response->assertSee('Laporan Penjualan', false);
        $response->assertSee('No. Transaksi');
        $response->assertSee('Nama Konsumen');
        $response->assertSee('Jumlah');
        $response->assertSee('Harga');
        $response->assertSee('@media print');
        $response->assertSee('report-signature', false);
    }

    // 8. Tampilan responsif.
    public function test_admin_layout_has_mobile_navigation(): void
    {
        $content = $this->actingAs($this->layanan)->get(route('admin.dashboard'))->getContent();

        $this->assertStringContainsString('admin-mobile-toggle', $content);
        $this->assertStringContainsString('admin-mobile-drawer', $content);
        $this->assertStringContainsString('admin-drawer-backdrop', $content);
        $this->assertStringContainsString('@media (min-width: 1024px)', $content, false);
    }

    // 9. Skala font konsisten.
    public function test_customer_stylesheet_defines_typography_scale(): void
    {
        $content = $this->get(route('catalog.index'))->getContent();

        // Skala tipografi terpusat dan tidak ada ukuran sangat kecil.
        $this->assertStringContainsString('--text-xs: 12px', $content);
        $this->assertStringContainsString('--text-sm: 13.5px', $content);
        $this->assertStringNotContainsString('font-size: 8px', $content);
    }

    // 10. Angka notifikasi keranjang.
    public function test_cart_badge_counts_products_not_units(): void
    {
        // Satu produk dengan 5 unit: badge harus menunjukkan 1.
        $response = $this->actingAs($this->konsumen)
            ->withSession(['cart' => [$this->product->id => 5]])
            ->get(route('catalog.index'));

        $response->assertOk();

        preg_match('~<span class="cart-count">(\d+)</span>~', $response->getContent(), $match);

        $this->assertNotEmpty($match, 'Badge keranjang tidak ditemukan');
        $this->assertSame('1', $match[1], 'Badge harus menghitung jumlah produk, bukan jumlah unit');
    }

    public function test_cart_badge_counts_each_product_once(): void
    {
        $lain = Product::create([
            'category_id' => $this->product->category_id,
            'name' => 'Padi Ciherang',
            'slug' => 'padi-ciherang',
            'packaging_unit' => 'bungkus',
            'price' => 48000,
            'stock' => 10,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->konsumen)
            ->withSession(['cart' => [$this->product->id => 5, $lain->id => 3]])
            ->get(route('catalog.index'));

        preg_match('~<span class="cart-count">(\d+)</span>~', $response->getContent(), $match);

        $this->assertSame('2', $match[1]);
    }

    // 6. Field gambar hero dihapus.
    public function test_landing_page_has_no_hero_image_field(): void
    {
        $response = $this->actingAs($this->layanan)->get(route('admin.landing.edit'));

        $response->assertOk();
        $response->assertDontSee('name="hero_image"', false);
    }

    // Notifikasi Email.
    public function test_officer_can_open_notification_email_page(): void
    {
        $response = $this->actingAs($this->layanan)->get(route('admin.notifications.index'));

        $response->assertOk();
        $response->assertSee('Alur notifikasi');
        $response->assertSee('konsumen@benih.test');
        $response->assertSee('layanan@benih.test');
    }
}