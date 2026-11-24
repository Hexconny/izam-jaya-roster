<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /** Halaman katalog: semua kategori + pencarian produk. */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $products = Product::with(['category', 'images', 'variants'])
            ->when($q, fn ($query) => $query->where('product_name', 'like', "%{$q}%"))
            ->orderBy('product_name')
            ->get();

        return view('public.catalog', [
            'categories' => Category::withCount('products')->get(),
            'active'     => null,
            'products'   => $products,
            'q'          => $q,
        ]);
    }

    /** Produk di dalam satu kategori. */
    public function category(Category $category)
    {
        return view('public.catalog', [
            'categories' => Category::withCount('products')->get(),
            'active'     => $category,
            'products'   => $category->products()->with(['category', 'images', 'variants'])->orderBy('product_name')->get(),
            'q'          => '',
        ]);
    }

    /** Detail produk + pilihan ukuran/motif. */
    public function show(Product $product)
    {
        $product->load(['category', 'images', 'variants']);

        return view('public.product', [
            'product' => $product,
            'related' => Product::with(['category', 'images', 'variants'])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->take(4)->get(),
        ]);
    }
}
