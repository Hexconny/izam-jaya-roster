<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

/** Stok Barang: lihat semua varian beserta stoknya, ubah banyak sekaligus, dan lihat riwayat. */
class StockController extends Controller
{
    public function __construct(private StockService $stock)
    {
    }

    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $q = trim((string) $request->query('q'));

        $variants = ProductVariant::with('product.category')
            ->whereHas('product', fn ($p) => $p->when($q, fn ($w) => $w->where('product_name', 'like', "%{$q}%")))
            ->when($filter === 'menipis', fn ($w) => $w->where('stock', '>', 0)->where('stock', '<=', ProductVariant::LOW_STOCK))
            ->when($filter === 'habis', fn ($w) => $w->where('stock', 0))
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->orderBy('products.product_name')->orderBy('product_variants.price')
            ->select('product_variants.*')
            ->paginate(30)->withQueryString();

        return view('admin.stock.index', [
            'variants' => $variants,
            'filter'   => $filter,
            'q'        => $q,
            'lowCount' => ProductVariant::where('stock', '>', 0)->where('stock', '<=', ProductVariant::LOW_STOCK)->count(),
            'outCount' => ProductVariant::where('stock', 0)->count(),
        ]);
    }

    /** Simpan perubahan stok. Hanya baris yang angkanya berubah yang dicatat. */
    public function update(Request $request)
    {
        $data = $request->validate([
            'stock'   => ['required', 'array'],
            'stock.*' => ['required', 'integer', 'min:0', 'max:1000000'],
            'note'    => ['nullable', 'string', 'max:255'],
        ], [
            'stock.*.integer' => 'Stok harus berupa angka bulat.',
            'stock.*.min'     => 'Stok tidak boleh negatif.',
        ]);

        $changed = 0;

        foreach (ProductVariant::whereIn('id', array_keys($data['stock']))->get() as $variant) {
            $new = (int) $data['stock'][$variant->id];

            if ($new !== (int) $variant->stock) {
                $this->stock->setTo($variant, $new, 'penyesuaian', $data['note'] ?? null);
                $changed++;
            }
        }

        return back()->with('success', $changed ? "{$changed} stok diperbarui." : 'Tidak ada stok yang berubah.');
    }

    public function history(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $movements = StockMovement::query()
            ->when($q, fn ($w) => $w->where('product_name', 'like', "%{$q}%"))
            ->latest('id')->paginate(30)->withQueryString();

        return view('admin.stock.history', ['movements' => $movements, 'q' => $q]);
    }
}
