<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Order;

/**
 * WhatsApp hanya dipakai sebagai kontak bantuan (bukan untuk konfirmasi pembayaran).
 * Teks pesan diubah di sini.
 */
class WhatsAppMessage
{
    /** Tombol "Tanya admin" di halaman pesanan. */
    public function helpUrl(Order $order): string
    {
        return $this->link("Halo Izam Jaya Roster, saya ingin bertanya tentang pesanan {$order->order_code}.");
    }

    /** Link wa.me ke admin dengan teks apa saja (dipakai juga untuk request roster khusus). */
    public function link(string $text): string
    {
        return 'https://wa.me/'.Contact::whatsapp().'?text='.rawurlencode($text);
    }
}
