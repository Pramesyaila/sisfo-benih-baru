<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarBadgeTest extends TestCase
{
    use RefreshDatabase;

    protected User $layanan;

    protected User $konsumen;

    protected function setUp(): void
    {
        parent::setUp();

        $kategori = Category::create(['name' => 'Padi', 'slug' => 'padi']);
        $sawah = Category::create([
            'name' => 'Padi Sawah',
            'slug' => 'padi-sawah',
            'parent_id' => $kategori->id,
        ]);

        $product = Product::create([
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

        $this->makeOrder($product, 'dipesan');
        $this->makeOrder($product, 'dipesan');
        $this->makeOrder($product, 'menunggu_verifikasi');
    }

    protected function makeOrder(Product $product, string $status): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'user_id' => $this->konsumen->id,
            'status' => $status,
            'total' => 50000,
            'notes' => 'Kebutuhan.',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_location' => 'brmp_penerapan',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => 1,
            'price' => 50000,
            'packaging' => '5 kg',
            'subtotal' => 50000,
        ]);

        if ($status === 'menunggu_verifikasi') {
            PaymentProof::create([
                'order_id' => $order->id,
                'file_path' => 'payment-proofs/test.jpg',
                'status' => 'menunggu',
            ]);
        }

        return $order;
    }

    public function test_sidebar_shows_pending_order_count(): void
    {
        $response = $this->actingAs($this->layanan)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('2 pesanan baru');
    }

    public function test_sidebar_shows_pending_verification_count(): void
    {
        $response = $this->actingAs($this->layanan)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('1 verifikasi');
    }

    public function test_counts_disappear_when_nothing_pending(): void
    {
        Order::where('status', 'dipesan')->update(['status' => 'diproses']);
        PaymentProof::where('status', 'menunggu')->update(['status' => 'valid']);

        $response = $this->actingAs($this->layanan)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('pesanan baru');
        $response->assertDontSee('verifikasi<');
    }

    public function test_topbar_order_is_katalog_pesanan_keranjang_profil(): void
    {
        $response = $this->actingAs($this->konsumen)->get(route('catalog.index'));

        $response->assertOk();

        $content = $response->getContent();

        preg_match('~<nav class="desktop-nav" aria-label="Navigasi pelanggan">(.*?)</nav>~s', $content, $nav);
        $this->assertNotEmpty($nav[1] ?? '', 'Nav desktop pelanggan tidak ditemukan');

        preg_match_all('~<a class="nav-link[^>]*>(.*?)</a>~s', $nav[1], $blocks);

        $order = [];
        foreach ($blocks[1] as $block) {
            // Buang ikon dan jumlah keranjang, sisakan teks tautan.
            $text = preg_replace('~<span class="nav-icon">.*?</span>~s', '', $block);
            $text = preg_replace('~<span class="cart-count">.*?</span>~s', '', $text);
            $text = trim(preg_replace('~\s+~', ' ', strip_tags($text)));

            if ($text !== '') {
                $order[] = $text;
            }
        }

        $this->assertSame(['Katalog', 'Pesanan', 'Keranjang', 'Profil'], $order);
    }
}