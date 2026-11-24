<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = ['menunggu_ongkir', 'menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'batal'];
    public const PAYMENTS = ['belum_bayar', 'menunggu_verifikasi', 'lunas', 'ditolak'];

    public const STATUS_LABELS = [
        'menunggu_ongkir'     => 'Menunggu ongkir',
        'menunggu_pembayaran' => 'Menunggu pembayaran',
        'diproses'            => 'Diproses',
        'dikirim'             => 'Dikirim',
        'selesai'             => 'Selesai',
        'batal'               => 'Batal',
    ];

    public const PAYMENT_LABELS = [
        'belum_bayar'         => 'Belum bayar',
        'menunggu_verifikasi' => 'Menunggu verifikasi',
        'lunas'               => 'Lunas',
        'ditolak'             => 'Bukti ditolak',
    ];

    /** Batas waktu pembayaran sejak total final (jam). */
    public const PAY_HOURS = 24;

    protected $fillable = [
        'order_code', 'customer_name', 'customer_phone', 'customer_address',
        'order_date', 'total_price', 'payment_status', 'order_status', 'source', 'notes',
        'delivery_method', 'shipping_cost', 'shipping_note',
        'payment_proof', 'payment_note', 'pay_deadline', 'paid_at', 'stock_deducted',
    ];

    protected function casts(): array
    {
        return [
            'order_date'   => 'datetime',
            'pay_deadline' => 'datetime',
            'paid_at'      => 'datetime',
            'stock_deducted' => 'boolean',
        ];
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    // ---------- label & status ----------

    public function statusLabel(): string
    {
        if ($this->order_status === 'dikirim' && $this->isPickup()) {
            return 'Siap diambil';
        }

        return self::STATUS_LABELS[$this->order_status] ?? $this->order_status;
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_LABELS[$this->payment_status] ?? $this->payment_status;
    }

    public function isPickup(): bool
    {
        return $this->delivery_method === 'ambil';
    }

    /** Total yang harus dibayar = barang + ongkir (ongkir belum ada dihitung 0). */
    public function grandTotal(): float
    {
        return (float) $this->total_price + (float) ($this->shipping_cost ?? 0);
    }

    /** Pelanggan boleh upload bukti hanya saat menunggu pembayaran. */
    public function canPay(): bool
    {
        return $this->order_status === 'menunggu_pembayaran'
            && in_array($this->payment_status, ['belum_bayar', 'ditolak'], true);
    }

    /** Ongkir sudah final -> buka pembayaran dan mulai hitung mundur. */
    public function openForPayment(): void
    {
        $this->order_status = 'menunggu_pembayaran';
        $this->pay_deadline = now()->addHours(self::PAY_HOURS);
    }

    /** Pesanan yang melewati batas bayar otomatis dibatalkan (dicek saat halaman dibuka). */
    public static function expireOverdue(): void
    {
        static::where('order_status', 'menunggu_pembayaran')
            ->whereIn('payment_status', ['belum_bayar', 'ditolak'])
            ->whereNotNull('pay_deadline')
            ->where('pay_deadline', '<', now())
            ->update(['order_status' => 'batal']);
    }

    // ---------- util ----------

    /** Kode pesanan, contoh: IJR-20261109-4821 */
    public static function makeCode(): string
    {
        do {
            $code = 'IJR-'.now()->format('Ymd').'-'.random_int(1000, 9999);
        } while (static::where('order_code', $code)->exists());

        return $code;
    }

    /** 0812... / +62812... / 62812... -> 62812... supaya bisa dibandingkan dan dipakai wa.me */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        return str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits;
    }
}
