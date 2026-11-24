<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public const REASONS = [
        'stok_awal'      => 'Stok awal',
        'penyesuaian'    => 'Penyesuaian admin',
        'edit_produk'    => 'Edit produk',
        'pesanan_lunas'  => 'Pesanan lunas',
        'pesanan_batal'  => 'Pesanan dibatalkan',
        'pesanan_hapus'  => 'Pesanan dihapus',
    ];

    protected $fillable = [
        'variant_id', 'order_id', 'user_id', 'product_name', 'variant_label',
        'change', 'stock_after', 'reason', 'note',
    ];

    public function reasonLabel(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }
}
