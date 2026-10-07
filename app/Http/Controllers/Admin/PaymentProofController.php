<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Order;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentProofController extends Controller
{
    public function index(Request $request)
    {
        $proofs = PaymentProof::with(['order.user', 'verifiedBy'])
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->status),
                fn ($query) => $query->where('status', 'menunggu')
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.payment-proofs.index', compact('proofs'));
    }

    public function verify(Request $request, PaymentProof $paymentProof)
    {
        if ($paymentProof->status !== 'menunggu') {
            return back()->with('error', 'Bukti pembayaran ini sudah diverifikasi.');
        }

        $data = $request->validate([
            'decision' => ['required', 'in:valid,ditolak'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($paymentProof, $data) {
            $paymentProof->update([
                'status' => $data['decision'],
                'note' => $data['note'] ?? null,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            $paymentProof->order->update([
                'status' => $data['decision'] === 'valid' ? 'siap_diambil' : 'pembayaran_ditolak',
            ]);
        });

        return back()->with(
            'success',
            $data['decision'] === 'valid'
                ? 'Pembayaran valid. Pesanan siap diambil dan faktur dapat diterbitkan.'
                : 'Pembayaran ditolak. Konsumen dapat mengunggah ulang bukti pembayaran.'
        );
    }

    }