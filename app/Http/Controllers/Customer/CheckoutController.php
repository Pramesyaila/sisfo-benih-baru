<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use App\Services\NotifiesSafely;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    use NotifiesSafely;

    public function index(Request $request)
    {
        $cart = CartController::cartWithProducts($request);

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang anda masih kosong.');
        }

        $total = $cart->sum('subtotal');
        $pickupLocations = Order::pickupLocationOptions();

        return view('customer.checkout.index', compact('cart', 'total', 'pickupLocations'));
    }

    public function store(Request $request)
    {
        // Tiga hal wajib diisi konsumen sebelum pesanan dibuat:
        // 1) tujuan penggunaan, 2) tanggal pengambilan, 3) lokasi pengambilan.
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_location' => ['required', Rule::in(array_keys(Order::pickupLocationOptions()))],
            'nik' => ['nullable', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'provinsi' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
        ], [
            'notes.required' => 'Tujuan penggunaan wajib diisi.',
            'pickup_date.required' => 'Tanggal pengambilan wajib diisi.',
            'pickup_location.required' => 'Lokasi pengambilan wajib dipilih.',
            'pickup_location.in' => 'Lokasi pengambilan yang dipilih tidak valid.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
        ]);

        $cart = CartController::cartWithProducts($request);

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang anda masih kosong.');
        }

        foreach ($cart as $row) {
            if ($row['qty'] > $row['product']->stock) {
                return back()->with('error', "Stok {$row['product']->name} tidak mencukupi.");
            }
        }

        // Data identitas yang diisi konsumen ikut diperbarui di profilnya agar
        // Petugas Layanan tidak perlu menanyakan ulang, dan agar lokasi pada
        // surat permohonan serta faktur terisi otomatis.
        Auth::user()->fill([
            'nik' => $validated['nik'] ?? null,
            'instansi' => $validated['instansi'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'kelurahan' => $validated['kelurahan'] ?? null,
            'kecamatan' => $validated['kecamatan'] ?? null,
            'kabupaten_kota' => $validated['kabupaten_kota'] ?? null,
            'provinsi' => $validated['provinsi'] ?? null,
            'domisili' => $validated['kabupaten_kota'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
        ])->save();

        $order = DB::transaction(function () use ($cart, $validated) {
            $order = Order::create([
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'user_id' => Auth::id(),
                'status' => 'dipesan',
                'total' => $cart->sum('subtotal'),
                'notes' => $validated['notes'],
                'pickup_date' => $validated['pickup_date'],
                'pickup_location' => $validated['pickup_location'],
            ]);

            foreach ($cart as $row) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $row['product']->id,
                    'product_name' => $row['product']->name,
                    'packaging' => $row['product']->packagingLabel(),
                    'price' => $row['product']->price,
                    'qty' => $row['qty'],
                    'subtotal' => $row['subtotal'],
                ]);
            }

            return $order;
        });

        $this->notifyPetugasLayanan($order);

        session()->forget('cart');

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat. Silakan tunggu proses administrasi dari petugas.');
    }

    /**
     * Satu pesanan baru = satu notifikasi per Petugas Layanan,
     * berapa pun jumlah produk yang dipesan.
     */
    protected function notifyPetugasLayanan(Order $order): void
    {
        $order->loadMissing('user');

        // Pesanan baru diteruskan ke Super Admin dan Petugas Layanan
        // melalui email yang didaftarkan pada masing-masing akun.
        $this->notifyManySafely(
            $this->petugasPenerimaNotifikasi(),
            new OrderCreatedNotification($order)
        );
    }
}
