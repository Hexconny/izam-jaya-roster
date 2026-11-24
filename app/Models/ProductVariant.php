<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Varian produk: kombinasi ukuran dan/atau motif beserta harga dan stok.
 */
class ProductVariant extends Model
{
    /** Stok di bawah atau sama dengan angka ini dianggap menipis. */
    public const LOW_STOCK = 10;

    protected $fillable = ['product_id', 'size', 'motif', 'price', 'is_available', 'stock'];

    protected function casts(): array
    {
        return ['is_available' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Contoh: "Motif Bunga · 20 x 20 cm" */
    public function label(): string
    {
        return collect([$this->motif, $this->size])->filter()->implode(' · ');
    }

    /** Bisa dipesan: tidak dimatikan admin dan stok masih ada. */
    public function isOrderable(): bool
    {
        return $this->is_available && $this->stock > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= self::LOW_STOCK;
    }
}
