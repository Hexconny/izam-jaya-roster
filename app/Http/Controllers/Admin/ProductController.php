<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** Kelola katalog: tambah, ubah, hapus produk beserta varian dan fotonya. */
class ProductController extends Controller
{
    public function __construct(private StockService $stock)
    {
    }

    public function index(Request $request)
    {
        $products = Product::with(['category', 'variants'])
            ->when($request->query('kategori'), fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy('product_name')
            ->paginate(15)->withQueryString();

        return view('admin.products.index', [
            'products'   => $products,
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'product'    => new Product(['availability' => 'tersedia']),
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $product = Product::create([
                'category_id'  => $data['category_id'],
                'product_name' => $data['product_name'],
                'slug'         => Product::makeSlug($data['product_name']),
                'description'  => $data['description'] ?? null,
                'availability' => $data['availability'],
            ]);

            $this->syncVariants($product, $data['variants']);
            $this->storeImages($product, $request);
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product'    => $product->load(['variants', 'images']),
            'categories' => Category::orderBy('category_name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data, $product) {
            $product->update([
                'category_id'  => $data['category_id'],
                'product_name' => $data['product_name'],
                'slug'         => Product::makeSlug($data['product_name'], $product->id),
                'description'  => $data['description'] ?? null,
                'availability' => $data['availability'],
            ]);

            $this->syncVariants($product, $data['variants']);

            // Hapus foto yang dicentang
            $remove = ProductImage::where('products_id', $product->id)
                ->whereIn('id', $request->input('remove_images', []))->get();
            foreach ($remove as $img) {
                Storage::disk('public')->delete($img->image);
                $img->delete();
            }

            $this->storeImages($product, $request);
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    // ------------------------------------------------------------------

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id'           => ['required', 'exists:categories,id'],
            'product_name'          => ['required', 'string', 'max:100'],
            'description'           => ['nullable', 'string', 'max:3000'],
            'availability'          => ['required', 'in:tersedia,habis'],
            'variants'              => ['required', 'array', 'min:1'],
            'variants.*.id'         => ['nullable', 'integer'],
            'variants.*.size'       => ['nullable', 'string', 'max:60'],
            'variants.*.motif'      => ['nullable', 'string', 'max:60'],
            'variants.*.price'      => ['required', 'numeric', 'min:0'],
            'variants.*.stock'      => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'variants.*.is_available' => ['nullable', 'boolean'],
            'images.*'              => ['nullable', 'image', 'max:2048'],
        ], [
            'variants.required'         => 'Tambahkan minimal satu varian (ukuran/harga).',
            'variants.*.price.required' => 'Harga varian wajib diisi.',
        ]);
    }

    /** Samakan varian di database dengan isi form (tambah, ubah, hapus yang dibuang). */
    private function syncVariants(Product $product, array $rows): void
    {
        $keepIds = [];

        foreach ($rows as $row) {
            $attrs = [
                'size'         => $row['size'] ?? null,
                'motif'        => $row['motif'] ?? null,
                'price'        => $row['price'],
                'is_available' => ! empty($row['is_available']),
            ];

            $newStock = (int) ($row['stock'] ?? 0);

            $variant = ! empty($row['id'])
                ? $product->variants()->whereKey($row['id'])->first()
                : null;

            if ($variant) {
                $variant->update($attrs);
                $this->stock->setTo($variant, $newStock, 'edit_produk');
            } else {
                $variant = $product->variants()->create($attrs);
                $this->stock->setTo($variant, $newStock, 'stok_awal');
            }

            $keepIds[] = $variant->id;
        }

        $product->variants()->reorder()->whereNotIn('id', $keepIds)->delete();

        // Harga "mulai dari" di tabel products
        $product->update(['price' => $product->variants()->reorder()->min('price') ?? 0]);
    }

    private function storeImages(Product $product, Request $request): void
    {
        foreach ($request->file('images', []) as $file) {
            $product->images()->create(['image' => $file->store('products', 'public')]);
        }
    }
}
