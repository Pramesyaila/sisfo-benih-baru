<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $layanan;

    protected User $gudang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => User::ROLE_PETUGAS_LAYANAN,
            'is_super_admin' => true,
            'email' => 'superadmin@benih.test',
        ]);

        $this->layanan = User::factory()->create([
            'role' => User::ROLE_PETUGAS_LAYANAN,
            'is_super_admin' => false,
        ]);

        $this->gudang = User::factory()->create([
            'role' => User::ROLE_PETUGAS_GUDANG,
        ]);
    }

    /**
     * Urutan menu yang diharapkan pada sidebar petugas layanan.
     *
     * @return array<int, string>
     */
    private function expectedLayananOrder(): array
    {
        return [
            'Dashboard',
            'Administrasi',
            'Pesanan',
            'Verifikasi Pembayaran',
            'Notifikasi Email',
            'Sistem',
            'Kelola Admin',
            'Landing Page',
            'Laporan',
            'Laporan Penjualan',
            'Laporan Stok',
            'Lainnya',
            'Profile',
            'Lihat Katalog',
        ];
    }

    /**
     * Mengambil urutan label menu dari sidebar sesuai urutan tampilnya.
     *
     * @return array<int, string>
     */
    private function sidebarLabels(string $html): array
    {
        // Ambil hanya isi sidebar pertama.
        preg_match('~<aside class="admin-sidebar.*?</aside>~s', $html, $aside);
        $sidebar = $aside[0] ?? '';

        // Judul grup dan tautan dikumpulkan berurutan sesuai kemunculannya.
        preg_match_all(
            '~<p class="nav-heading">([^<]+)</p>|<span class="nav-item">\s*<span class="nav-icon">[^<]*</span>\s*([^<\n]+)~',
            $sidebar,
            $matches,
            PREG_SET_ORDER
        );

        $labels = [];

        foreach ($matches as $match) {
            $label = trim($match[2] ?? '') !== '' ? $match[2] : ($match[1] ?? '');
            $labels[] = trim(preg_replace('~\s+~', ' ', $label));
        }

        return $labels;
    }

    public function test_sidebar_uses_fixed_width_not_full_width(): void
    {
        $html = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'))->getContent();

        // Sidebar harus berbekas lebar tetap, bukan memenuhi layar.
        $this->assertStringNotContainsString('admin-sidebar w-full', $html);

        preg_match('~<style>(.*?)</style>~s', $html, $style);
        $css = $style[1] ?? '';

        $this->assertStringContainsString('.admin-sidebar', $css);
        $this->assertStringContainsString('width: 16rem', $css);
        $this->assertStringContainsString('flex-shrink: 0', $css);
    }

    public function test_sidebar_menu_order_is_correct_for_petugas_layanan(): void
    {
        $html = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'))->getContent();
        $labels = $this->sidebarLabels($html);

        $this->assertSame($this->expectedLayananOrder(), $labels);
    }

    public function test_sidebar_menu_order_is_correct_for_petugas_gudang(): void
    {
        $html = $this->actingAs($this->gudang)->get(route('admin.dashboard'))->getContent();
        $labels = $this->sidebarLabels($html);

        $expected = [
            'Dashboard',
            'Katalog &amp; Stok',
            'Kategori &amp; Varietas',
            'Produk',
            'Stok Masuk/Keluar',
            'Riwayat Stok',
            'Pengambilan Benih',
            'Laporan',
            'Laporan Penjualan',
            'Laporan Stok',
            'Lainnya',
            'Profile',
            'Lihat Katalog',
        ];

        $this->assertSame($expected, $labels);
    }

    public function test_sidebar_and_mobile_drawer_share_same_menu(): void
    {
        $html = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'))->getContent();

        // Hanya satu partial yang dipakai dua kali.
        $this->assertSame(
            2,
            substr_count($html, 'class="admin-nav px-3 py-4 space-y-1 text-sm"'),
            'Sidebar dan drawer harus memakai sumber menu yang sama'
        );

        // Drawer dan sidebar memuat tautan yang sama.
        preg_match('~<aside class="admin-sidebar.*?</aside>~s', $html, $aside);
        preg_match('~<div id="admin-mobile-drawer".*?</div>\s*</div>~s', $html, $drawer);

        $this->assertNotEmpty($aside[0] ?? null);
        $this->assertNotEmpty($drawer[0] ?? null);

        foreach (['Pesanan', 'Verifikasi Pembayaran', 'Kelola Admin', 'Laporan Stok'] as $label) {
            $this->assertStringContainsString($label, $aside[0]);
            $this->assertStringContainsString($label, $drawer[0]);
        }
    }

    public function test_mobile_drawer_has_backdrop_and_close_controls(): void
    {
        $html = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'))->getContent();

        $this->assertStringContainsString('id="admin-drawer-backdrop"', $html);
        $this->assertStringContainsString('id="admin-drawer-close"', $html);
        $this->assertStringContainsString('admin-drawer-backdrop.is-open', $html);
        $this->assertStringContainsString('admin-mobile-drawer.is-open', $html);
    }

    public function test_kelola_admin_hidden_for_non_super_admin(): void
    {
        $layananHtml = $this->actingAs($this->layanan)->get(route('admin.dashboard'))->getContent();
        $gudangHtml = $this->actingAs($this->gudang)->get(route('admin.dashboard'))->getContent();

        $this->assertStringNotContainsString('Kelola Admin', $layananHtml);
        $this->assertStringNotContainsString('Kelola Admin', $gudangHtml);
    }

    public function test_sidebar_renders_on_all_admin_pages(): void
    {
        $halaman = [
            [route('admin.dashboard'), $this->superAdmin],
            [route('admin.orders.index'), $this->superAdmin],
            [route('admin.landing.edit'), $this->superAdmin],
            [route('admin.products.index'), $this->gudang],
            [route('admin.stock.index'), $this->gudang],
            [route('admin.categories.index'), $this->gudang],
        ];

        foreach ($halaman as [$url, $user]) {
            $html = $this->actingAs($user)->get($url);

            $html->assertOk();
            $html->assertSee('<aside class="admin-sidebar', false);
        }
    }
}