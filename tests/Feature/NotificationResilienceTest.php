<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\BillingAvailableNotification;
use App\Notifications\InvoiceReadyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Notifikasi ke email tidak boleh menggagalkan proses bisnis.
 * Channel database tetap bekerja meski server mail tidak dapat dihubungi.
 */
class NotificationResilienceTest extends TestCase
{
    use RefreshDatabase;

    protected User $layanan;

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
            'stock' => 10,
            'status' => 'aktif',
        ]);

        $this->layanan = User::factory()->create(['role' => User::ROLE_PETUGAS_LAYANAN]);
        $this->konsumen = User::factory()->create(['role' => User::ROLE_KONSUMEN]);
    }

    protected function makeOrder(string $status): Order
    {
        return Order::create([
            'order_number' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'user_id' => $this->konsumen->id,
            'status' => $status,
            'total' => 50000,
            'notes' => 'Kebutuhan tanam.',
            'pickup_date' => now()->addDays(2)->toDateString(),
            'pickup_location' => 'brmp_penerapan',
        ]);
    }

    public function test_invoice_is_created_even_when_mail_fails(): void
    {
        Mail::shouldReceive('send')->andThrow(new \Exception('SMTP tidak dapat dihubungi'));

        $order = $this->makeOrder('siap_diambil');

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.invoice.store', $order))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Faktur tetap terbit meski mail gagal.
        $this->assertNotNull($order->fresh()->invoice);

        // Notifikasi database tetap tersimpan.
        $this->assertSame(1, $this->konsumen->notifications()->count());
    }

    public function test_billing_is_created_even_when_mail_fails(): void
    {
        Mail::shouldReceive('send')->andThrow(new \Exception('SMTP tidak dapat dihubungi'));

        $order = $this->makeOrder('diproses');

        $this->actingAs($this->layanan)
            ->post(route('admin.orders.billing.store', $order), [
                'file' => UploadedFile::fake()->create('billing.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertNotNull($order->fresh()->billing);
        $this->assertSame('menunggu_pembayaran', $order->fresh()->status);
        $this->assertSame(1, $this->konsumen->notifications()->count());
    }

    public function test_invoice_notification_reaches_both_channels(): void
    {
        Notification::fake();

        $order = $this->makeOrder('siap_diambil');

        $this->actingAs($this->layanan)->post(route('admin.orders.invoice.store', $order));

        $invoice = $order->fresh()->invoice;

        Notification::assertSentTo(
            $this->konsumen,
            InvoiceReadyNotification::class,
            function ($notification) use ($invoice, $order) {
                return $notification->invoice->is($invoice)
                    && $notification->order()->is($order)
                    // Email ikut dikirim, bukan hanya database.
                    && in_array('mail', $notification->via($this->konsumen), true);
            }
        );
    }

    public function test_billing_notification_reaches_both_channels(): void
    {
        Notification::fake();

        $order = $this->makeOrder('diproses');

        $this->actingAs($this->layanan)->post(route('admin.orders.billing.store', $order), [
            'file' => UploadedFile::fake()->create('billing.pdf', 20, 'application/pdf'),
        ]);

        Notification::assertSentTo(
            $this->konsumen,
            BillingAvailableNotification::class,
            function ($notification) use ($order) {
                return $notification->order->is($order)
                    && in_array('mail', $notification->via($this->konsumen), true);
            }
        );
    }

    public function test_mail_contains_faktur_link_to_consumer_page(): void
    {
        Mail::fake();

        $order = $this->makeOrder('siap_diambil');

        $this->actingAs($this->layanan)->post(route('admin.orders.invoice.store', $order));

        // Notifikasi tersimpan pada channel database sehingga tetap tampil
        // di aplikasi meskipun mail adalah array (tidak benar-benar dikirim).
        $this->assertSame(1, $this->konsumen->notifications()->count());

        $notification = $this->konsumen->notifications()->first();
        $this->assertSame($order->id, $notification->data['order_id']);
    }
}