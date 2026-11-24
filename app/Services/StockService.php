<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Semua perubahan stok lewat sini supaya selalu tercatat di riwayat.
 *
 * Aturan:
 * - Stok berkurang saat pembayaran DITERIMA (bukan saat pesanan dibuat).
 * - Stok kembali kalau pesanan yang sudah lunas dibatalkan atau dihapus.
 * - Penanda orders.stock_deducted mencegah stok dikurangi/dikembalikan dua kali.
 */
class StockService
{
    /** Ubah stok sebuah varian sebesar $delta (boleh negatif) dan catat riwayatnya. */
    public function adjust(ProductVariant $variant, int $delta, string $reason, ?Order $order = null, ?string $note = null): ProductVariant
    {
        return DB::transaction(function () use ($variant, $delta, $reason, $order, $note) {
            $fresh = ProductVariant::with('product')->lockForUpdate()->findOrFail($variant->id);
            $after = $fresh->stock + $delta;

            if ($after < 0) {
                throw new RuntimeException("Stok {$fresh->product->product_name} ({$fresh->label()}) hanya {$fresh->stock}.");
            }

            $fresh->update(['stock' => $after]);

            StockMovement::create([
                'variant_id'    => $fresh->id,
                'order_id'      => $order?->id,
                'user_id'       => Auth::id(),
                'product_name'  => $fresh->product->product_name,
                'variant_label' => $fresh->label(),
                'change'        => $delta,
                'stock_after'   => $after,
                'reason'        => $reason,
                'note'          => $note,
            ]);

            return $fresh;
        });
    }

    /** Set stok ke angka tertentu (dipakai halaman Stok Barang dan form produk). */
    public function setTo(ProductVariant $variant, int $new, string $reason, ?string $note = null): void
    {
        $delta = $new - (int) $variant->stock;

        if ($delta !== 0) {
            $this->adjust($variant, $delta, $reason, null, $note);
        }
    }

    /**
     * Cek apakah stok cukup untuk seluruh isi pesanan. Mengembalikan daftar pesan kekurangan
     * (kosong = cukup).
     */
    public function shortages(Order $order): array
    {
        $problems = [];

        foreach ($order->details as $d) {
            if (! $d->variant_id) {
                continue; // varian sudah dihapus admin, tidak bisa dicek
            }

            $variant = ProductVariant::find($d->variant_id);

            if ($variant && $variant->stock < $d->quantity) {
                $problems[] = "{$d->product_name} ({$d->variant_label}): diminta {$d->quantity}, stok {$variant->stock}";
            }
        }

        return $problems;
    }

    /** Kurangi stok untuk pesanan yang sudah lunas. Aman dipanggil berulang. */
    public function deduct(Order $order): void
    {
        if ($order->stock_deducted) {
            return;
        }

        $order->loadMissing('details');

        if ($problems = $this->shortages($order)) {
            throw new RuntimeException('Stok tidak cukup: '.implode('; ', $problems).'.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->details as $d) {
                if ($d->variant_id && ($variant = ProductVariant::find($d->variant_id))) {
                    $this->adjust($variant, -$d->quantity, 'pesanan_lunas', $order, $order->order_code);
                }
            }

            $order->forceFill(['stock_deducted' => true])->save();
        });
    }

    /** Kembalikan stok pesanan yang pernah dikurangi. Aman dipanggil berulang. */
    public function restore(Order $order, string $reason = 'pesanan_batal'): void
    {
        if (! $order->stock_deducted) {
            return;
        }

        $order->loadMissing('details');

        DB::transaction(function () use ($order, $reason) {
            foreach ($order->details as $d) {
                if ($d->variant_id && ($variant = ProductVariant::find($d->variant_id))) {
                    $this->adjust($variant, $d->quantity, $reason, $order, $order->order_code);
                }
            }

            $order->forceFill(['stock_deducted' => false])->save();
        });
    }
}
