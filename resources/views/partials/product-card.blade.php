{{-- Kartu produk. Kalau belum ada foto, dipakai pola sesuai kategori. --}}
<article class="card">
    <a href="{{ route('product.show', $product) }}" class="card__media pat-{{ $product->category->slug }}">
        @if ($product->coverUrl())
            <img src="{{ $product->coverUrl() }}" alt="{{ $product->product_name }}" loading="lazy">
        @endif
        @unless ($product->inStock())
            <span class="tag tag--off">Habis</span>
        @endunless
    </a>
    <div class="card__body">
        <p class="card__cat">{{ $product->category->category_name }}</p>
        <h3 class="card__title"><a href="{{ route('product.show', $product) }}">{{ $product->product_name }}</a></h3>
        <p class="card__price"><small>mulai</small> Rp {{ number_format($product->price, 0, ',', '.') }}</p>
    </div>
</article>
