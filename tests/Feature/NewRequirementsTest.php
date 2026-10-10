<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\BillingAvailableNotification;
use App\Notifications\InvoiceReadyNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\PaymentProofSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewRequirementsTest extends TestCase
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
            'stock' => 20,
            'min_stock' => 2,
            'status' => 'aktif',
        ]);

        // Akun ini berperan sebagai Super Admin agar dapat mengakses Kelola Admin.
        $this->layanan = User::factory()->create([
            'role' => User::ROLE_PETUGAS_LAYANAN,
            'is_super_admin' => true,
        ]);
        $this->gudang = User::factory()->create(['role' => User::ROLE_PETUGAS_GUDANG]);
        $this->konsumen = User::factory()->create([
            'role' => User::ROLE_KONSUMEN,
            'kabupaten_kota' => 'Kabupaten Bogor',
        ]);
    }

    protected function makeOrder(string $status = 'dipesan', int $itemCount = 1): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'user_id' => $this->konsumen->id,
            'status' => $status,
            'total' => 50000 * $itemCount,
            'notes' => 'Kebutuhan tanam musim hujan.',
            'pickup_date' => now()->addDays(3)->toDateString(),
            'pickup_location' => 'brmp_penerapan',
        ]);

        for ($i = 0; $i < $itemCount; $i++) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
                'product_name' => $this->product->name . ' #' . ($i + 1),
                'qty' => 1,
                'price' => 50000,
                'packaging' => '5 kg / bungkus',
                'subtotal' => 50000,
            ]);
        }

        return $order;
    }

    // 1. Officer creation persists and page renders inside layout.
    public function test_officer_create_page_renders_with_admin_layout(): void
    {
        $response = $this->actingAs($this->layanan)->get(route('admin.admins.create'));

        $response->assertOk();
        $response->assertSee('cdn.tailwindcss.com', false);
    }

    public function test_officer_account_is_saved_to_database_with_identity_fields(): void
    {
        $this->actingAs($this->layanan)->post(route('admin.admins.store'), [
            'name' => 'Petugas Gudang Baru',
            'email' => 'gudang.baru@benih.test',
            'phone' => '08123456789',
            'whatsapp' => '08123456789',
            'nik' => '3201234567890001',
            'instansi' => 'Kelompok Tani Sejahtera',
            'alamat' => 'Jl. Raya No. 10',
            'kelurahan' => 'Sukamaju',
            'kecamatan' => 'Bogor Timur',
            'kabupaten_kota' => 'Kabupaten Bogor',
            'provinsi' => 'Jawa Barat',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => User::ROLE_PETUGAS_GUDANG,
            'is_active' => '1',
            'super_admin_password' => 'password',
        ])->assertRedirect(route('admin.admins.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'gudang.baru@benih.test',
            'role' => User::ROLE_PETUGAS_GUDANG,
            'nik' => '3201234567890001',
            'kabupaten_kota' => 'Kabupaten Bogor',
        ]);
    }

    // 2 + 16. Topbar order and admin profile mirrors officer form.
    public function test_admin_profile_shows_same_fields_as_officer_form(): void
    {
        $this->actingAs($this->gudang)->put(route('profile.update'), [
            'name' => 'Gudang Updated',
            'email' => $this->gudang->email,
            'nik' => '3209999999990001',
            'instansi' => 'UPTD Benih',
            'kelurahan' => 'Ciawi',
            'kecamatan' => 'Ciawi',
            'kabupaten_kota' => 'Kabupaten Bogor',
            'provinsi' => 'Jawa Barat',
            'phone' => '081200000000',
        ])->assertRedirect(route('profile.show'));

        $response = $this->actingAs($this->gudang)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSee('3209999999990001');
        $response->assertSee('UPTD Benih');
        $response->assertSee('Kabupaten Bogor');
    }

    // 3. One order = one notification regardless of item count.
    public function test_one_order_with_many_items_produces_single_notification(): void
    {
        $order = $this->makeOrder('diproses', itemCount: 5);

        $this->layanan->notify(new OrderCreatedNotification($order));

        // Notifikasi benar-benar tersimpan ke tabel notifications.
        $notifications = $this->layanan->notifications()->get();

        $this->assertCount(1, $notifications, 'Satu pesanan harus menghasilkan satu notifikasi');
        $this->assertSame($order->id, $notifications->first()->data['order_id']);
    }

    public function test_notifications_are_grouped_per_order(): void
    {
        $order = $this->makeOrder('diproses', itemCount: 3);

        // Beberapa notifikasi untuk pesanan yang sama.
        $this->layanan->notify(new OrderCreatedNotification($order));
        $this->layanan->notify(new PaymentProofSubmittedNotification($order, 1));

        $data = [
            'order_id' => $order->id,
            'url' => route('admin.orders.show', $order),
        ];

        $grouped = $this->layanan->notifications()->get()
            ->groupBy(fn ($n) => $n->data['order_id'] ?? $n->id);

        // Satu id pesanan harus menghasilkan satu grup.
        $this->assertCount(1, $grouped);
        $this->assertCount(2, $grouped->first());
    }

    // 4. Notification reaches email channel.
    public function test_billing_notification_is_sent_to_mail_channel(): void
    {
        $order = $this->makeOrder('diproses');

        $notification = new BillingAvailableNotification($order);

        $this->assertContains('mail', $notification->via($this->konsumen));
        $this->assertContains('database', $notification->via($this->konsumen));

        $mail = $notification->toMail($this->konsumen);
        $this->assertStringContainsString($order->order_number, $mail->subject);
    }

    public function test_invoice_notification_is_sent_to_mail_channel(): void
    {
        $order = $this->makeOrder('siap_diambil');

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV/TEST/001',
            'total' => 50000,
            'issued_by' => $this->layanan->id,
        ]);

        $notification = new InvoiceReadyNotification($invoice);

        $this->assertContains('mail', $notification->via($this->konsumen));
        $this->assertStringContainsString('INV/TEST/001', $notification->toMail($this->konsumen)->subject);
    }

    // 3. Billing tidak digenerate sistem; preview di halaman pesanan dihapus.
    public function test_billing_is_only_uploaded_file_without_preview_route(): void
    {
        $order = $this->makeOrder('menunggu_pembayaran');

        Billing::create([
            'order_id' => $order->id,
            'bill_number' => 'BILL/TEST/001',
            'file_path' => 'billings/test.pdf',
            'amount' => 50000,
            'sent_at' => now(),
        ]);

        // Route preview sudah tidak ada.
        $this->assertNull(
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.orders.billing.preview')
        );

        // Halaman detail pesanan tidak lagi menawarkan preview billing,
        // hanya tautan ke berkas yang diunggah petugas.
        $response = $this->actingAs($this->layanan)->get(route('admin.orders.show', $order));

        $response->assertOk();
        $response->assertSee('BILL/TEST/001');
        $response->assertSee('Lihat billing');
        $response->assertDontSee('Preview billing');
    }

    // 6. Sales report includes distribution columns in one page.
    public function test_sales_report_shows_consolidated_columns(): void
    {
        $order = $this->makeOrder('siap_diambil', itemCount: 2);

        $response = $this->actingAs($this->layanan)->get(route('admin.reports.sales'));

        $response->assertOk();
        $response->assertSee('No. Transaksi');
        $response->assertSee('Nama Konsumen');
        $response->assertSee('Jumlah');
        $response->assertSee('Harga');
        $response->assertSee($order->order_number);
        $response->assertSee($this->konsumen->name);

        // Distribusi sudah digabung, tidak lagi jadi halaman terpisah.
        $this->assertNull(
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.reports.distribution')
        );
    }

    // 8. Sidebar badge counts.
    public function test_dashboard_exposes_pending_counts(): void
    {
        $this->makeOrder('dipesan');
        $this->makeOrder('dipesan');

        $response = $this->actingAs($this->layanan)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('2 pesanan baru');
    }

    // 9. Registration collects full identity.
    public function test_registration_stores_full_identity(): void
    {
        $this->post(route('register'), [
            'name' => 'Konsumen Baru',
            'email' => 'baru@contoh.test',
            'nik' => '3273000000000001',
            'instansi' => 'Kelompok Tani Maju',
            'alamat' => 'Jl. Raya Bogor No. 5',
            'kelurahan' => 'Sukajadi',
            'kecamatan' => 'Bogor Selatan',
            'kabupaten_kota' => 'Kota Bogor',
            'provinsi' => 'Jawa Barat',
            'phone' => '081234567890',
            'whatsapp' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('catalog.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'baru@contoh.test',
            'nik' => '3273000000000001',
            'kabupaten_kota' => 'Kota Bogor',
            'role' => User::ROLE_KONSUMEN,
        ]);
    }

    // 10. Checkout requires purpose, date, location.
    public function test_checkout_requires_purpose_date_and_location(): void
    {
        $this->actingAs($this->konsumen)
            ->withSession(['cart' => [$this->product->id => 1]])
            ->post(route('checkout.store'), [])
            ->assertSessionHasErrors(['notes', 'pickup_date', 'pickup_location']);
    }

    public function test_checkout_saves_pickup_plan_and_identity(): void
    {
        $this->actingAs($this->konsumen)
            ->withSession(['cart' => [$this->product->id => 2]])
            ->post(route('checkout.store'), [
                'notes' => 'Penanaman jagung 1 ha.',
                'pickup_date' => now()->addWeek()->toDateString(),
                'pickup_location' => 'ip2mp_cipaku',
                'nik' => '3273000000000009',
                'kabupaten_kota' => 'Kota Bogor',
                'phone' => '081234567890',
            ])
            ->assertRedirect();

        $order = Order::latest('id')->first();

        $this->assertSame('ip2mp_cipaku', $order->pickup_location);
        $this->assertNotNull($order->pickup_date);
        $this->assertNotEmpty($order->notes);

        // Data identitas ikut tersimpan ke profil.
        $this->assertSame('3273000000000009', $this->konsumen->fresh()->nik);
        $this->assertSame('Kota Bogor', $this->konsumen->fresh()->kabupaten_kota);
    }

    // 11. Surat permohonan memuat lokasi dari kabupaten/kota.
    public function test_application_letter_shows_location_from_kabupaten_kota(): void
    {
        $order = $this->makeOrder('dipesan');

        $response = $this->actingAs($this->layanan)
            ->get(route('admin.orders.print.permohonan', $order));

        $response->assertOk();
        $response->assertSee('Kabupaten Bogor');
    }

    // 13. Edit/hapus mewajibkan password.
    public function test_admin_edit_and_delete_require_password_confirmation(): void
    {
        $this->actingAs($this->layanan)
            ->put(route('admin.admins.update', $this->gudang), [
                'name' => 'X',
                'email' => $this->gudang->email,
                'role' => User::ROLE_PETUGAS_GUDANG,
                'super_admin_password' => 'ketik-salah',
            ])
            ->assertSessionHasErrors('super_admin_password');

        $this->actingAs($this->layanan)
            ->delete(route('admin.admins.destroy', $this->gudang), ['super_admin_password' => 'ketik-salah'])
            ->assertSessionHasErrors('super_admin_password');
    }

    // 14 + 15. Logo and hero image upload without wiping existing file.
    public function test_logo_can_be_uploaded_and_replaced(): void
    {
        $this->actingAs($this->layanan)->put(route('admin.landing.update'), [
            'hero_title' => 'Judul Hero',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ])->assertRedirect();

        $content = \App\Models\LandingContent::current();
        $firstPath = $content->logo;

        $this->assertNotNull($firstPath);
        Storage::disk('public')->assertExists($firstPath);

        // Unggah logo kedua harus mengganti, bukan menambah.
        $this->actingAs($this->layanan)->put(route('admin.landing.update'), [
            'hero_title' => 'Judul Hero',
            'logo' => UploadedFile::fake()->image('logo2.png', 200, 200),
        ])->assertRedirect();

        $content->refresh();
        $this->assertNotSame($firstPath, $content->logo);
        Storage::disk('public')->assertExists($content->logo);
    }

    // 6. Field ubah gambar hero dihapus dari halaman Kelola Landing Page.
    public function test_hero_image_upload_is_removed_from_landing_page(): void
    {
        $response = $this->actingAs($this->layanan)->get(route('admin.landing.edit'));

        $response->assertOk();
        $response->assertDontSee('name="hero_image"', false);
        $response->assertSee('name="logo"', false);

        // Field hero_image tidak lagi diproses.
        $this->actingAs($this->layanan)->put(route('admin.landing.update'), [
            'hero_title' => 'Judul Baru',
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 800, 400),
        ])->assertRedirect();

        $this->assertNull(\App\Models\LandingContent::current()->hero_image);
    }

    public function test_logo_is_shown_in_customer_header(): void
    {
        $this->actingAs($this->layanan)->put(route('admin.landing.update'), [
            'hero_title' => 'Judul',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertSee(\App\Models\LandingContent::current()->logoUrl(), false);
    }

    // 17 + 18. Faktur generation and consumer download.
    public function test_invoice_is_generated_after_payment_verification(): void
    {
        $order = $this->makeOrder('siap_diambil', itemCount: 2);

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.invoice.store', $order))
            ->assertRedirect();

        $order->refresh();

        $this->assertNotNull($order->invoice);
        $this->assertSame($order->items->sum('subtotal'), $order->invoice->total);
        $this->assertSame('brmp_penerapan', $order->invoice->pickup_location);
    }

    public function test_invoice_page_contains_required_sections(): void
    {
        // 2 item x Rp50.000 = Rp100.000 => "seratus ribu rupiah"
        $order = $this->makeOrder('siap_diambil', itemCount: 2);

        $this->actingAs($this->layanan)->post(route('admin.orders.invoice.store', $order));

        $invoice = $order->fresh()->invoice;

        $response = $this->actingAs($this->layanan)
            ->get(route('admin.orders.invoice.show', $order));

        $response->assertOk();
        $response->assertSee($invoice->invoice_number);
        // Kolom tabel faktur.
        $response->assertSee('Banyaknya');
        $response->assertSee('Nama Barang');
        $response->assertSee('Harga Satuan');
        // Terbilang dan tiga kolom tanda tangan.
        $response->assertSee('Terbilang');
        $response->assertSee('Seratus ribu rupiah');
        $response->assertSee('Tanda Terima');
        $response->assertSee('Mengetahui');
        $response->assertSee('Hormat Kami');
        // "Kepada Yth" dikosongkan untuk diisi manual.
        $response->assertSee('Kepada Yth.');
    }

    public function test_invoice_cannot_be_generated_twice(): void
    {
        $order = $this->makeOrder('siap_diambil');

        $this->actingAs($this->layanan)->post(route('admin.orders.invoice.store', $order));

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.invoice.store', $order))
            ->assertSessionHas('error');

        $this->assertSame(1, Invoice::where('order_id', $order->id)->count());
    }

    public function test_invoice_requires_verified_payment(): void
    {
        $order = $this->makeOrder('menunggu_pembayaran');

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.invoice.store', $order))
            ->assertSessionHas('error');

        $this->assertNull($order->fresh()->invoice);
    }

    public function test_consumer_can_view_and_print_invoice(): void
    {
        $order = $this->makeOrder('siap_diambil');
        $order->update(['user_id' => $this->konsumen->id]);

        $this->actingAs($this->layanan)->post(route('admin.orders.invoice.store', $order));

        $invoice = $order->fresh()->invoice;

        // Konsumen melihat faktur di halaman pesanan.
        $this->actingAs($this->konsumen)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee($invoice->invoice_number);

        // Faktur dapat diunduh/dicetak dari sisi konsumen.
        $download = $this->actingAs($this->konsumen)->get(route('orders.invoice', $order));
        $download->assertOk();
        $download->assertSee($invoice->invoice_number);

        // Konsumen lain tidak boleh mengunduh faktur milik orang lain.
        $other = User::factory()->create(['role' => User::ROLE_KONSUMEN]);
        $this->actingAs($other)
            ->get(route('orders.invoice', $order))
            ->assertForbidden();
    }

    // 7. Filter navigation keeps scroll position flag.
    public function test_catalog_filter_preserves_scroll_position(): void
    {
        $response = $this->get(route('catalog.index', ['category' => 'padi']));

        $response->assertOk();

        // Penyimpanan dan pemulihan posisi gulir harus terpasang.
        $response->assertSee("catalogScrollY", false);
        $response->assertSee("scrollRestoration", false);
        $response->assertSee("pagehide", false);
        $response->assertSee('filter-list--sub', false);
    }

    public function test_catalog_filter_urls_keep_search_keyword(): void
    {
        $response = $this->get(route('catalog.index', ['category' => 'padi', 'q' => 'inpari']));

        $response->assertOk();
        $response->assertSee('q=inpari', false);
    }
}
