<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        // Data contoh. Semuanya bisa diubah lewat dashboard admin.
        BusinessProfile::firstOrCreate([], [
            'business_name' => 'Izam Jaya Roster',
            'hero_title'    => 'Roster, asbes, dan boeven untuk rumah yang lebih adem.',
            'hero_subtitle' => 'Pilih ukuran dan motif, masukkan ke keranjang, lalu selesaikan pesanan lewat WhatsApp.',
            'description'   => "Izam Jaya Roster menyediakan roster, asbes, boeven, dan batu tempel untuk kebutuhan membangun dan merenovasi rumah.\n\nKami juga melayani pembuatan roster dengan motif atau ukuran khusus sesuai permintaan.",
            'address'       => 'Isi alamat toko di menu Edit Tentang',
            'open_hours'    => 'Senin - Sabtu, 08.00 - 16.00',
            'instagram'     => '',
            'map_query'     => 'Tulungagung, Jawa Timur',
            'bank_name'     => 'BCA',
            'bank_account'  => '1234567890',
            'bank_holder'   => 'Izam Jaya Roster',
            'shipping_policy' => "Ambil di toko: tanpa ongkir.\nPengiriman: ongkir ditentukan admin setelah pesanan masuk, sesuai jarak dan jumlah barang. Pesanan dengan jarak dekat bisa gratis ongkir.",
        ]);

        Contact::firstOrCreate([], ['whatsapp' => '6281234567890']);
    }
}
