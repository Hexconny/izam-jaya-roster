@extends('layouts.app')

@section('title', $product->product_name)
@section('description', \Illuminate\Support\Str::limit(strip_tags($product->description), 150))

@section('content')
<section class="section">
    <div class="wrap">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="{{ route('catalog.index') }}">Katalog</a> /
            <a href="{{ route('catalog.category', $product->category) }}">{{ $product->category->category_name }}</a> /
            <span>{{ $product->product_name }}</span>
        </nav>

        <div class="detail">
            <div class="gallery">
                <div class="gallery__main pat-{{ $product->category->slug }}">
                    @if ($product->images->isNotEmpty())
                        <img src="{{ asset('storage/'.$product->images->first()->image) }}" alt="{{ $product->product_name }}" data-gallery-main>
                    @endif
                </div>
                @if ($product->images->count() > 1)
                    <div class="gallery__thumbs">
                        @foreach ($product->images as $img)
                            <button type="button" data-gallery-thumb="{{ asset('storage/'.$img->image) }}" aria-label="Lihat foto {{ $loop->iteration }}">
                                <img src="{{ asset('storage/'.$img->image) }}" alt="">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="detail__info">
                <p class="eyebrow">{{ $product->category->category_name }}</p>
                <h1>{{ $product->product_name }}</h1>

                <p class="detail__price" data-price>Rp {{ number_format($product->price, 0, ',', '.') }}</p>

                @php($firstOrderable = optional($product->variants->first(fn ($v) => $v->isOrderable()))->id)
                <p>
                    @if ($product->inStock())
                        <span class="tag tag--on">Tersedia</span>
                    @else
                        <span class="tag tag--off">Stok habis</span>
                    @endif
                </p>

                @if ($product->description)
                    <div class="prose">{!! nl2br(e($product->description)) !!}</div>
                @endif

                @if ($product->inStock())
                    <form method="post" action="{{ route('cart.add') }}" class="buy">
                        @csrf
                        <fieldset>
                            <legend>Pilih ukuran{{ $product->variants->whereNotNull('motif')->isNotEmpty() ? ' / motif' : '' }}</legend>
                            <div class="variants">
                                @foreach ($product->variants as $variant)
                                    <label @class(['variant', 'is-off' => ! $variant->isOrderable()])>
                                        <input type="radio" name="variant_id" value="{{ $variant->id }}"
                                            data-price="{{ (int) $variant->price }}" data-stock="{{ $variant->stock }}"
                                            @disabled(! $variant->isOrderable())
                                            @checked($variant->id === $firstOrderable) required>
                                        <span class="variant__name">{{ $variant->label() ?: 'Standar' }}</span>
                                        <span class="variant__price">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                        @if (! $variant->isOrderable())<em>habis</em>
                                        @elseif ($variant->isLowStock())<em class="variant__low">sisa {{ $variant->stock }}</em>@endif
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="buy__row">
                            <div class="qty" data-qty>
                                <button type="button" data-qty-minus aria-label="Kurangi">&minus;</button>
                                <input type="number" name="qty" value="1" min="1" max="9999" aria-label="Jumlah">
                                <button type="button" data-qty-plus aria-label="Tambah">+</button>
                            </div>
                            <button class="btn btn--primary" type="submit">Tambah ke keranjang</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>

@if ($related->isNotEmpty())
<section class="section section--tint">
    <div class="wrap">
        <h2>Produk sekategori</h2>
        <div class="grid">
            @foreach ($related as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
