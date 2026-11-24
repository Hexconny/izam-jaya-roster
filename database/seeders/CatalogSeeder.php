<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'asbes'        => ['Asbes', 'Atap asbes gelombang berbagai ukuran.'],
            'roster'       => ['Roster', 'Roster beton aneka motif dan ukuran.'],
            'boeven'       => ['Boeven', 'Boeven (ventilasi/lubang angin) berbagai ukuran.'],
            'batu-tempel'  => ['Batu Tempel', 'Batu tempel untuk dinding dan tampilan fasad.'],
        ];

        foreach ($categories as $slug => [$name, $desc]) {
            Category::updateOrCreate(['slug' => $slug], ['category_name' => $name, 'description' => $desc]);
        }

        // Produk contoh. Harga hanya contoh, silakan ganti lewat admin.
        $samples = [
            ['asbes', 'Asbes Gelombang', 'Atap asbes gelombang kecil, kuat dan tahan lama.', [
                ['size' => '180 x 105 cm', 'price' => 65000, 'stock' => 100],
                ['size' => '240 x 105 cm', 'price' => 86000, 'stock' => 100],
                ['size' => '300 x 105 cm', 'price' => 108000, 'stock' => 100],
            ]],
            ['roster', 'Roster Bunga', 'Roster beton motif bunga untuk sirkulasi udara dan hiasan dinding.', [
                ['motif' => 'Bunga', 'size' => '20 x 20 cm', 'price' => 7500, 'stock' => 100],
                ['motif' => 'Bunga', 'size' => '25 x 25 cm', 'price' => 11000, 'stock' => 100],
            ]],
            ['roster', 'Roster Kotak', 'Roster beton motif kotak modern.', [
                ['motif' => 'Kotak', 'size' => '20 x 20 cm', 'price' => 7000, 'stock' => 100],
                ['motif' => 'Kotak', 'size' => '30 x 30 cm', 'price' => 13500, 'stock' => 100],
            ]],
            ['boeven', 'Boeven Beton', 'Boeven beton untuk ventilasi dinding.', [
                ['size' => '40 x 20 cm', 'price' => 9000, 'stock' => 100],
                ['size' => '60 x 30 cm', 'price' => 16000, 'stock' => 100],
            ]],
            ['batu-tempel', 'Batu Tempel Alam', 'Batu tempel untuk dinding depan atau taman.', [
                ['size' => 'Per m2', 'price' => 85000, 'stock' => 100],
            ]],
        ];

        foreach ($samples as [$catSlug, $name, $desc, $variants]) {
            if (Product::where('product_name', $name)->exists()) {
                continue;
            }

            $product = Product::create([
                'category_id'  => Category::where('slug', $catSlug)->value('id'),
                'product_name' => $name,
                'slug'         => Product::makeSlug($name),
                'description'  => $desc,
                'price'        => collect($variants)->min('price'),
                'availability' => 'tersedia',
            ]);

            foreach ($variants as $v) {
                $product->variants()->create($v + ['is_available' => true]);
            }
        }
    }
}
