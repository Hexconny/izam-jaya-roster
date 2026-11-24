@extends('layouts.app')

@section('title', $active ? $active->category_name : 'Katalog')

@section('content')
<section class="section">
    <div class="wrap">
        <p class="eyebrow">Katalog</p>
        <h1>{{ $active ? $active->category_name : 'Semua produk' }}</h1>
        @if ($active && $active->description)
            <p class="lead">{{ $active->description }}</p>
        @endif

        <div class="filters">
            <div class="chips">
                <a href="{{ route('catalog.index') }}" @class(['chip', 'is-active' => ! $active])>Semua</a>
                @foreach ($categories as $category)
                    <a href="{{ route('catalog.category', $category) }}" @class(['chip', 'is-active' => $active && $active->id === $category->id])>
                        {{ $category->category_name }}
                    </a>
                @endforeach
            </div>

            @unless ($active)
                <form class="search" method="get" action="{{ route('catalog.index') }}" role="search">
                    <input type="search" name="q" value="{{ $q }}" placeholder="Cari produk" aria-label="Cari produk">
                    <button class="btn btn--dark" type="submit">Cari</button>
                </form>
            @endunless
        </div>

        @if ($products->isEmpty())
            <p class="empty">Produk belum ditemukan. Coba kategori lain atau <a href="#request-roster">kirim request ke WhatsApp</a>.</p>
        @else
            <div class="grid">
                @foreach ($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </div>
</section>

@include('partials.request-roster')
@endsection
