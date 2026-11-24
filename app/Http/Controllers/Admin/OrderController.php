<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\StockService;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Kelola pesanan: lihat, input manual, tentukan ongkir, verifikasi pembayaran,
 * ubah status, hapus.
 */
class OrderController extends Controller
{
    public function __construct(private StockService $stock)
    {
    }

    public function index(Request $request)
    {
        Order::expireOverdue();

        $orders = Order::withCount('details')
            ->when($request->query('status'), fn ($q, $s) => $q->where('order_status', $s))
            ->when($request->query('bayar'), fn ($q, $s) => $q->where('payment_status', $s))
            ->when($request->query('q'), fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('customer_name', 'like', "%{$s}%")->orWhere('order_code', 'like', "%{$s}%");
            }))
            ->latest('order_date')->paginate(15)->withQueryString();

        return view('admin.orders.index', ['orders' => $orders]);
    }

    public function create()
    {
        return view('admin.orders.create', [
            'variants' => ProductVariant::with('product')
                ->where('is_available', true)->get()
                ->sortBy(fn ($v) => $v->product->product_name)->values(),
        ]);
    }

    /** Catat pesanan yang masuk lewat telepon / datang langsung ke toko. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'      => ['required', 'string', 'max:100'],
            'customer_phone'     => ['required', 'string', 'max:20'],
            'customer_address'   => ['required', 'string', 'max:500'],
            'delivery_method'    => ['required', 'in:kirim,ambil'],
            'shipping_cost'      => ['nullable', 'numeric', 'min:0'],
            'shipping_note'      => ['nullable', 'string', 'max:255'],
            'paid'               => ['nullable', 'boolean'],
            'notes'              => ['nullable', 'string', 'max:500'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'exists:product_variants,id'],
            'items.*.qty'        => ['required', 'integer', 'min:1', 'max:9999'],
        ], ['items.required' => 'Tambahkan minimal satu produk.']);

        try {
            $order = DB::transaction(function () use ($data) {
            $order = new Order([
                'order_code'       => Order::makeCode(),
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'delivery_method'  => $data['delivery_method'],
                'shipping_note'    => $data['shipping_note'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'order_date'       => now(),
                'source'           => 'admin',
                'total_price'      => 0,
                'payment_status'   => 'belum_bayar',
            ]);

            $cost = $data['delivery_method'] === 'ambil' ? 0 : ($data['shipping_cost'] ?? null);
            $order->shipping_cost = $cost;

            if ($cost === null) {
                $order->order_status = 'menunggu_ongkir';
            } elseif (! empty($data['paid'])) {
                $order->order_status = 'diproses';
                $order->payment_status = 'lunas';
                $order->paid_at = now();
            } else {
                $order->openForPayment();
            }

            $order->save();

            $total = 0;
            foreach ($data['items'] as $item) {
                $variant = ProductVariant::with('product')->find($item['variant_id']);
                $subtotal = $variant->price * $item['qty'];
                $total += $subtotal;

                $order->details()->create([
                    'product_id'    => $variant->product_id,
                    'variant_id'    => $variant->id,
                    'product_name'  => $variant->product->product_name,
                    'variant_label' => $variant->label(),
                    'quantity'      => $item['qty'],
                    'price'         => $variant->price,
                    'subtotal'      => $subtotal,
                ]);
            }

            $order->update(['total_price' => $total]);

            // Dibayar langsung -> stok langsung dikurangi (gagal kalau stok tidak cukup)
            if ($order->payment_status === 'lunas') {
                $this->stock->deduct($order);
            }

            return $order;
            });
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Pesanan berhasil dicatat.');
    }

    public function show(Order $order)
    {
        Order::expireOverdue();
        $order->refresh()->load('details');

        return view('admin.orders.show', [
            'order'  => $order,
            'waLink' => 'https://wa.me/'.Order::normalizePhone($order->customer_phone)
                .'?text='.rawurlencode("Halo {$order->customer_name}, terkait pesanan {$order->order_code} di Izam Jaya Roster."),
        ]);
    }

    /** Admin menentukan ongkir -> pesanan terbuka untuk dibayar. */
    public function shipping(Request $request, Order $order)
    {
        if ($order->isPickup()) {
            return back()->with('error', 'Pesanan ambil di toko tidak memakai ongkir.');
        }

        if (! in_array($order->order_status, ['menunggu_ongkir', 'menunggu_pembayaran'], true)) {
            return back()->with('error', 'Ongkir tidak bisa diubah setelah pesanan dibayar atau diproses.');
        }

        $data = $request->validate([
            'shipping_cost' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'shipping_note' => ['nullable', 'string', 'max:255'],
        ]);

        $order->shipping_cost = $data['shipping_cost'];
        $order->shipping_note = $data['shipping_note'] ?? null;

        if ($order->payment_status === 'menunggu_verifikasi') {
            // Bukti sudah masuk, total tidak boleh berubah diam-diam
            return back()->with('error', 'Bukti pembayaran sudah masuk. Verifikasi dulu sebelum mengubah ongkir.');
        }

        if ($order->order_status === 'menunggu_ongkir') {
            $order->openForPayment();
        }

        $order->save();

        return back()->with('success', 'Ongkir disimpan. Pelanggan sekarang bisa membayar.');
    }

    /** Terima atau tolak bukti pembayaran. */
    public function verify(Request $request, Order $order)
    {
        $data = $request->validate([
            'decision' => ['required', 'in:terima,tolak'],
            'reason'   => ['required_if:decision,tolak', 'nullable', 'string', 'max:255'],
        ], ['reason.required_if' => 'Isi alasan penolakan supaya pelanggan tahu apa yang harus diperbaiki.']);

        if ($order->payment_status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Tidak ada bukti pembayaran yang perlu diverifikasi.');
        }

        if ($data['decision'] === 'terima') {
            try {
                DB::transaction(function () use ($order) {
                    $order->update([
                        'payment_status' => 'lunas',
                        'order_status'   => 'diproses',
                        'paid_at'        => now(),
                        'payment_note'   => null,
                    ]);

                    // Stok baru berkurang di sini, saat pembayaran diterima
                    $this->stock->deduct($order);
                });
            } catch (RuntimeException $e) {
                return back()->with('error', $e->getMessage().' Hubungi pelanggan atau tambah stok dulu, lalu terima lagi.');
            }

            return back()->with('success', 'Pembayaran diterima, stok dikurangi. Pesanan masuk status Diproses.');
        }

        $order->update([
            'payment_status' => 'ditolak',
            'payment_note'   => $data['reason'],
            'pay_deadline'   => now()->addHours(Order::PAY_HOURS),
        ]);

        return back()->with('success', 'Bukti ditolak. Pelanggan bisa mengupload ulang.');
    }

    /** Tampilkan file bukti (disimpan privat, hanya admin yang bisa membuka). */
    public function proof(Order $order)
    {
        abort_unless($order->payment_proof && Storage::disk('local')->exists($order->payment_proof), 404);

        return Storage::disk('local')->response($order->payment_proof);
    }

    /** Ubah status pesanan / pembayaran secara manual + catatan. */
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'order_status'   => ['required', 'in:'.implode(',', Order::STATUSES)],
            'payment_status' => ['required', 'in:'.implode(',', Order::PAYMENTS)],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['payment_status'] === 'lunas' && ! $order->paid_at) {
            $data['paid_at'] = now();
        }

        try {
            DB::transaction(function () use ($order, $data) {
                $order->update($data);

                // Jaga stok tetap sesuai status: lunas -> kurangi, batal / tidak lunas -> kembalikan
                if ($order->stock_deducted && ($order->order_status === 'batal' || $order->payment_status !== 'lunas')) {
                    $this->stock->restore($order, 'pesanan_batal');
                } elseif (! $order->stock_deducted && $order->payment_status === 'lunas' && $order->order_status !== 'batal') {
                    $this->stock->deduct($order);
                }
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function destroy(Order $order)
    {
        $this->stock->restore($order, 'pesanan_hapus');

        if ($order->payment_proof) {
            Storage::disk('local')->delete($order->payment_proof);
        }
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan dihapus.');
    }
}
