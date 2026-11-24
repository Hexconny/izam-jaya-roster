@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
{{-- 1. Banner utama. Gambar diganti lewat Admin > Edit Beranda --}}
<section class="banner {{ $profile->hero_image ? '' : 'pat-roster' }}"
    @if ($profile->hero_image) style="background-image:url('{{ asset('storage/'.$profile->hero_image) }}')" @endif>
    <div class="banner__shade">
        <div class="wrap banner__inner">
            <p class="eyebrow">{{ $profile->business_name }}</p>
            <h1>{{ $profile->hero_title }}</h1>
            <p class="banner__sub">{{ $profile->hero_subtitle }}</p>
            <div class="hero__cta">
                <a href="{{ route('catalog.index') }}" class="btn btn--primary">Lihat katalog</a>
                <a href="#request-roster" class="btn btn--light">Request roster khusus</a>
            </div>
        </div>
    </div>
</section>

{{-- 2. Kartu kategori. Gambar diganti lewat Admin > Kategori --}}
<section class="section">
    <div class="wrap">
        <div class="section__head">
            <h2>Produk kami</h2>
            <a href="{{ route('catalog.index') }}" class="link">Semua produk</a>
        </div>
        <div class="tiles">
            @foreach ($categories as $category)
                <a class="tile" href="{{ route('catalog.category', $category) }}">
                    <span class="tile__art pat-{{ $category->slug }}">
                        @if ($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->category_name }}" loading="lazy">
                        @endif
                    </span>
                    <span class="tile__name">{{ $category->category_name }}</span>
                    <span class="tile__desc">{{ $category->description }}</span>
                    <span class="tile__count">{{ $category->products_count }} produk &rarr;</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- 3. Tentang singkat --}}
<section class="section section--tint">
    <div class="wrap intro">
        <div>
            <p class="eyebrow">Tentang kami</p>
            <h2>{{ $profile->business_name }}</h2>
        </div>
        <div>
            <p>{{ \Illuminate\Support\Str::limit(strip_tags($profile->description), 320) }}</p>
            <a href="{{ route('about') }}" class="link">Selengkapnya</a>
        </div>
    </div>
</section>

{{-- 4. Produk terbaru --}}
@if ($featured->isNotEmpty())
<section class="section">
    <div class="wrap">
        <div class="section__head">
            <h2>Produk terbaru</h2>
            <a href="{{ route('catalog.index') }}" class="link">Lihat semua</a>
        </div>
        <div class="grid">
            @foreach ($featured as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 5. Cara pesan --}}
<section class="section section--tint">
    <div class="wrap steps">
        <h2>Cara pesan</h2>
        <ol>
            <li><b>Pilih produk</b> dan tentukan ukuran atau motif.</li>
            <li><b>Masukkan ke keranjang</b>, cek lagi jumlah dan harganya.</li>
            <li><b>Isi data dan buat pesanan.</b> Pilih dikirim atau ambil di toko, admin menentukan ongkirnya.</li>
            <li><b>Bayar dan upload bukti</b> di halaman pesananmu, lalu pantau statusnya lewat Lacak Pesanan.</li>
        </ol>
    </div>
</section>

{{-- 6. Request roster khusus --}}
@include('partials.request-roster')

{{-- 7. Kontak --}}
<section class="section">
    <div class="wrap contact">
        <div>
            <p class="eyebrow">Kontak</p>
            <h2>Datang atau hubungi kami</h2>
        </div>
        <dl class="facts">
            @if ($profile->address)<div><dt>Alamat</dt><dd>{{ $profile->address }}</dd></div>@endif
            @if ($profile->open_hours)<div><dt>Jam buka</dt><dd>{{ $profile->open_hours }}</dd></div>@endif
            @if ($wa = \App\Models\Contact::whatsapp())
                <div><dt>WhatsApp</dt><dd><a class="link" href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener">+{{ $wa }}</a></dd></div>
            @endif
            <div><dt>Peta</dt><dd><a class="link" href="{{ route('about') }}#lokasi">Lihat lokasi toko</a></dd></div>
        </dl>
    </div>
</section>
@endsection
