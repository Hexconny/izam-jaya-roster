@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<section class="section">
    <div class="wrap">
        <p class="eyebrow">Keranjang</p>
        <h1>Cek pesananmu</h1>

        @if ($items->isEmpty())
            <p class="empty">Keranjang masih kosong. <a href="{{ route('catalog.index') }}">Lihat katalog</a></p>
        @else
            <div class="cart">
                <div class="cart__items">
                    @foreach ($items as $row)
                        <article class="line">
                            <div class="line__media pat-{{ $row->product->category->slug ?? 'roster' }}">
                                @if ($row->product->coverUrl())
                                    <img src="{{ $row->product->coverUrl() }}" alt="">
                                @endif
                            </div>
                            <div class="line__info">
                                <a href="{{ route('product.show', $row->product) }}" class="line__name">{{ $row->product->product_name }}</a>
                                <p class="line__variant">{{ $row->variant->label() }}</p>
                                <p class="line__price">Rp {{ number_format($row->variant->price, 0, ',', '.') }}</p>
                                @if ($row->qty > $row->variant->stock)
                                    <p class="line__warn">Stok tinggal {{ $row->variant->stock }}, kurangi jumlahnya.</p>
                                @endif

                                <div class="line__actions">
                                    <form method="post" action="{{ route('cart.update', $row->variant) }}" class="qty" data-qty>
                                        @csrf @method('PATCH')
                                        <button type="button" data-qty-minus aria-label="Kurangi">&minus;</button>
                                        <input type="number" name="qty" value="{{ $row->qty }}" min="1" max="9999" aria-label="Jumlah" data-autosubmit>
                                        <button type="button" data-qty-plus aria-label="Tambah">+</button>
                                    </form>
                                    <form method="post" action="{{ route('cart.remove', $row->variant) }}">
                                        @csrf @method('DELETE')
                                        <button class="link link--danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <p class="line__subtotal">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</p>
                        </article>
                    @endforeach
                </div>

                <aside class="summary">
                    <h2>Data pemesan</h2>
                    <form method="post" action="{{ route('checkout.store') }}">
                        @csrf
                        <label>Nama lengkap
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required maxlength="100" autocomplete="name">
                        </label>
                        <label>Nomor HP / WhatsApp
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="20" inputmode="tel" autocomplete="tel" placeholder="08xxxxxxxxxx">
                        </label>
                        <label>Alamat (untuk pengiriman atau catatan pengambilan)
                            <textarea name="customer_address" rows="3" required maxlength="500" autocomplete="street-address">{{ old('customer_address') }}</textarea>
                        </label>

                        <fieldset class="delivery">
                            <legend>Cara menerima barang</legend>
                            <label class="delivery__opt"><input type="radio" name="delivery_method" value="kirim" @checked(old('delivery_method', 'kirim') === 'kirim')> Dikirim ke alamat <small>ongkir ditentukan admin</small></label>
                            <label class="delivery__opt"><input type="radio" name="delivery_method" value="ambil" @checked(old('delivery_method') === 'ambil')> Ambil di toko <small>tanpa ongkir</small></label>
                        </fieldset>
                        @if ($policy)<p class="hint">{!! nl2br(e($policy)) !!}</p>@endif

                        <dl class="totals">
                            <div><dt>Total barang</dt><dd>Rp {{ number_format($total, 0, ',', '.') }}</dd></div>
                            <div class="totals__note"><dt>Ongkos kirim</dt><dd>Ditentukan admin</dd></div>
                        </dl>

                        <button class="btn btn--primary btn--block" type="submit">Buat pesanan</button>
                        <p class="hint">Setelah pesanan dibuat, kamu mendapat kode pesanan dan bisa membayar serta memantau statusnya di website.</p>
                    </form>
                </aside>
            </div>
        @endif
    </div>
</section>
@endsection
