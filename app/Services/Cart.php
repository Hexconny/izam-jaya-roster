<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;

/**
 * Keranjang belanja yang disimpan di SESSION: [variant_id => jumlah].
 * Customer tidak perlu login (SKPL batasan no. 4).
 */
class Cart
{
    private const KEY = 'cart';

    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(int $variantId, int $qty = 1): void
    {
        $cart = $this->raw();
        $cart[$variantId] = min(($cart[$variantId] ?? 0) + $qty, 9999);
        session([self::KEY => $cart]);
    }

    public function set(int $variantId, int $qty): void
    {
        if ($qty < 1) {
            $this->remove($variantId);

            return;
        }

        $cart = $this->raw();
        $cart[$variantId] = min($qty, 9999);
        session([self::KEY => $cart]);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->raw();
        unset($cart[$variantId]);
        session([self::KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /** Daftar baris keranjang lengkap dengan data produk dan subtotal. */
    public function items(): Collection
    {
        $raw = $this->raw();

        if (! $raw) {
            return collect();
        }

        return ProductVariant::with('product.images', 'product.category')
            ->whereIn('id', array_keys($raw))
            ->get()
            ->map(fn ($variant) => (object) [
                'variant'  => $variant,
                'product'  => $variant->product,
                'qty'      => $raw[$variant->id],
                'subtotal' => $variant->price * $raw[$variant->id],
            ]);
    }

    public function total(): float
    {
        return (float) $this->items()->sum('subtotal');
    }
}
