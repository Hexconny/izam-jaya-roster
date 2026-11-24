<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart)
    {
    }

    public function index()
    {
        return view('public.cart', [
            'items' => $this->cart->items(),
            'total' => $this->cart->total(),
            'policy' => \App\Models\BusinessProfile::current()->shipping_policy,
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'exists:product_variants,id'],
            'qty'        => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($data['variant_id']);

        if (! $variant->isOrderable() || ! $variant->product->isAvailable()) {
            return back()->with('error', 'Maaf, varian ini sedang habis atau tidak tersedia.');
        }

        $inCart = $this->cart->raw()[$variant->id] ?? 0;

        if ($inCart + (int) $data['qty'] > $variant->stock) {
            return back()->with('error', "Stok {$variant->product->product_name} ({$variant->label()}) tinggal {$variant->stock}"
                .($inCart ? ", dan di keranjangmu sudah ada {$inCart}" : '').'.');
        }

        $this->cart->add($variant->id, (int) $data['qty']);

        return back()->with('success', $variant->product->product_name.' masuk ke keranjang.');
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $qty = (int) $request->validate(['qty' => ['required', 'integer', 'min:0', 'max:9999']])['qty'];
        if ($qty > $variant->stock) {
            $this->cart->set($variant->id, $variant->stock);

            return redirect()->route('cart.index')
                ->with('error', "Stok tinggal {$variant->stock}, jumlah di keranjang disesuaikan.");
        }

        $this->cart->set($variant->id, $qty);

        return redirect()->route('cart.index');
    }

    public function remove(ProductVariant $variant)
    {
        $this->cart->remove($variant->id);

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }
}
