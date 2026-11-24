<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ['category_id', 'product_name', 'slug', 'description', 'price', 'availability'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('price');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'products_id');
    }

    public function isAvailable(): bool
    {
        return $this->availability === 'tersedia';
    }

    /** Produk dianggap ada stok kalau tidak dimatikan dan minimal satu varian bisa dipesan. */
    public function inStock(): bool
    {
        return $this->isAvailable() && $this->variants->contains(fn ($v) => $v->isOrderable());
    }

    /** Foto pertama produk, atau null kalau belum ada. */
    public function coverUrl(): ?string
    {
        $first = $this->images->first();

        return $first ? asset('storage/'.$first->image) : null;
    }

    /** Slug unik dari nama produk. */
    public static function makeSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
