@extends('layouts.admin')
@section('title', 'Input Pesanan')
@section('heading', 'Input Pesanan')

@section('content')
@php
    $oldItems = old('items', [['variant_id' => '', 'qty' => 1]]);
    $options = $variants->map(fn ($v) => [
        'id' => $v->id,
        'label' => $v->product->product_name.' | '.$v->label().' | Rp '.number_format($v->price, 0, ',', '.'),
    ]);
@endphp

<form method="post" action="{{ route('admin.orders.store') }}" class="panel form" data-order-form>
    @csrf
    <p class="muted">Untuk mencatat pesanan yang masuk lewat WhatsApp, telepon, atau datang langsung ke toko.</p>

    <div class="row">
        <label>Nama pelanggan
            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required maxlength="100">
        </label>
        <label>Nomor HP
            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="20">
        </label>
    </div>
    <label>Alamat
        <textarea name="customer_address" rows="2" required maxlength="500">{{ old('customer_address') }}</textarea>
    </label>

    <div class="row">
        <label>Cara menerima barang
            <select name="delivery_method">
                <option value="kirim" @selected(old('delivery_method', 'kirim') === 'kirim')>Dikirim</option>
                <option value="ambil" @selected(old('delivery_method') === 'ambil')>Ambil di toko</option>
            </select>
        </label>
        <label>Ongkir (Rp, kosongkan kalau belum ditentukan)
            <input type="number" name="shipping_cost" min="0" step="1000" value="{{ old('shipping_cost') }}">
        </label>
    </div>
    <label>Catatan ongkir
        <input type="text" name="shipping_note" maxlength="255" value="{{ old('shipping_note') }}">
    </label>
    <label class="check"><input type="checkbox" name="paid" value="1" @checked(old('paid'))> Sudah dibayar lunas (bayar langsung / tunai)</label>

    <fieldset>
        <legend>Produk yang dipesan</legend>
        <div data-items>
            @foreach ($oldItems as $i => $item)
                <div class="vrow vrow--order" data-item-row>
                    <label>Produk
                        <select name="items[{{ $i }}][variant_id]" required>
                            <option value="">Pilih produk</option>
                            @foreach ($options as $opt)
                                <option value="{{ $opt['id'] }}" @selected(($item['variant_id'] ?? '') == $opt['id'])>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Jumlah
                        <input type="number" name="items[{{ $i }}][qty]" value="{{ $item['qty'] ?? 1 }}" min="1" required>
                    </label>
                    <button type="button" class="danger" data-remove-item>Hapus</button>
                </div>
            @endforeach
        </div>

        <template data-item-template>
            <div class="vrow vrow--order" data-item-row>
                <label>Produk
                    <select name="items[__INDEX__][variant_id]" required>
                        <option value="">Pilih produk</option>
                        @foreach ($options as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Jumlah
                    <input type="number" name="items[__INDEX__][qty]" value="1" min="1" required>
                </label>
                <button type="button" class="danger" data-remove-item>Hapus</button>
            </div>
        </template>

        <button type="button" class="btn btn--small" data-add-item>+ Tambah produk</button>
    </fieldset>

    <label>Catatan (ongkir, kesepakatan, dll)
        <textarea name="notes" rows="2" maxlength="500">{{ old('notes') }}</textarea>
    </label>

    <div class="form__actions">
        <button class="btn btn--primary" type="submit">Simpan pesanan</button>
        <a class="btn" href="{{ route('admin.orders.index') }}">Batal</a>
    </div>
</form>
@endsection
