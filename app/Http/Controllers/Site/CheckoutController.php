<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart)
    {
    }

    /** Simpan pesanan ke database, lalu buka halaman pesanan (untuk bayar & pantau status). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'    => ['required', 'string', 'max:100'],
            'customer_phone'   => ['required', 'string', 'max:20', 'regex:/^[0-9+\s-]{8,20}$/'],
            'customer_address' => ['required', 'string', 'max:500'],
            'delivery_method'  => ['required', 'in:kirim,ambil'],
        ], [
            'customer_phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'delivery_method.required' => 'Pilih cara penerimaan barang.',
        ]);

        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        foreach ($items as $row) {
            if (! $row->variant->isOrderable() || $row->qty > $row->variant->stock) {
                return redirect()->route('cart.index')->with('error',
                    "Stok {$row->product->product_name} ({$row->variant->label()}) tinggal {$row->variant->stock}. Ubah jumlahnya di keranjang.");
            }
        }

        $order = DB::transaction(function () use ($data, $items) {
            $order = new Order($data + [
                'order_code'     => Order::makeCode(),
                'order_date'     => now(),
                'total_price'    => $items->sum('subtotal'),
                'payment_status' => 'belum_bayar',
                'source'         => 'web',
            ]);

            if ($data['delivery_method'] === 'ambil') {
                // Ambil di toko: tidak ada ongkir, langsung bisa dibayar
                $order->shipping_cost = 0;
                $order->openForPayment();
            } else {
                // Dikirim: tunggu admin menentukan ongkir
                $order->order_status = 'menunggu_ongkir';
            }

            $order->save();

            foreach ($items as $row) {
                $order->details()->create([
                    'product_id'    => $row->product->id,
                    'variant_id'    => $row->variant->id,
                    'product_name'  => $row->product->product_name,
                    'variant_label' => $row->variant->label(),
                    'quantity'      => $row->qty,
                    'price'         => $row->variant->price,
                    'subtotal'      => $row->subtotal,
                ]);
            }

            return $order;
        });

        $this->cart->clear();
        TrackController::remember($order);

        return redirect()->route('track.show', $order->order_code)
            ->with('success', 'Pesanan dibuat. Simpan kode pesanan '.$order->order_code.' untuk melihat statusnya.');
    }
}
