<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Order;
use App\Services\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Lacak pesanan tanpa login: pelanggan memasukkan kode pesanan + nomor HP.
 * Kode yang sudah terverifikasi disimpan di session supaya halaman bisa dibuka lagi.
 */
class TrackController extends Controller
{
    private const SESSION_KEY = 'tracked_orders';

    public function form()
    {
        return view('public.track');
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:30'],
            'hp'   => ['required', 'string', 'max:20'],
        ]);

        $order = Order::where('order_code', strtoupper(trim($data['kode'])))->first();

        if (! $order || Order::normalizePhone($order->customer_phone) !== Order::normalizePhone($data['hp'])) {
            return back()->withInput()->with('error', 'Kode pesanan dan nomor HP tidak cocok.');
        }

        self::remember($order);

        return redirect()->route('track.show', $order->order_code);
    }

    public function show(Order $order, WhatsAppMessage $wa)
    {
        if ($redirect = $this->guard($order)) {
            return $redirect;
        }

        Order::expireOverdue();
        $order->refresh()->load('details');

        return view('public.order', [
            'order'   => $order,
            'profile' => BusinessProfile::current(),
            'waLink'  => $wa->helpUrl($order),
        ]);
    }

    /** Upload bukti pembayaran. */
    public function proof(Request $request, Order $order)
    {
        if ($redirect = $this->guard($order)) {
            return $redirect;
        }

        Order::expireOverdue();
        $order->refresh();

        if (! $order->canPay()) {
            return back()->with('error', 'Pesanan ini belum bisa dibayar atau sudah dibayar.');
        }

        $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ], [
            'proof.required' => 'Pilih file bukti pembayaran.',
            'proof.mimes'    => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'proof.max'      => 'Ukuran bukti maksimal 3 MB.',
        ]);

        if ($order->payment_proof) {
            Storage::disk('local')->delete($order->payment_proof);
        }

        $order->update([
            'payment_proof'  => $request->file('proof')->store('payment-proofs', 'local'),
            'payment_status' => 'menunggu_verifikasi',
            'payment_note'   => null,
        ]);

        return back()->with('success', 'Bukti pembayaran terkirim. Admin akan memverifikasinya.');
    }

    public static function remember(Order $order): void
    {
        $codes = session(self::SESSION_KEY, []);
        $codes[] = $order->order_code;
        session([self::SESSION_KEY => array_values(array_unique($codes))]);
    }

    private function guard(Order $order)
    {
        if (in_array($order->order_code, session(self::SESSION_KEY, []), true)) {
            return null;
        }

        return redirect()->route('track.form')->with('error', 'Masukkan kode pesanan dan nomor HP untuk membuka pesanan.');
    }
}
