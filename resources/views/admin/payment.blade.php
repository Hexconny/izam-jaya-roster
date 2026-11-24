@extends('layouts.admin')
@section('title', 'Pembayaran & Ongkir')
@section('heading', 'Pembayaran & Ongkir')

@section('content')
<form method="post" action="{{ route('admin.payment.update') }}" enctype="multipart/form-data" class="panel form">
    @csrf @method('PUT')
    <p class="muted">Data ini tampil di halaman pesanan pelanggan saat mereka akan membayar.</p>

    <div class="row">
        <label>Nama bank
            <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" maxlength="60" placeholder="BCA">
        </label>
        <label>Nomor rekening
            <input type="text" name="bank_account" value="{{ old('bank_account', $profile->bank_account) }}" maxlength="40">
        </label>
    </div>
    <label>Atas nama
        <input type="text" name="bank_holder" value="{{ old('bank_holder', $profile->bank_holder) }}" maxlength="100">
    </label>

    <label>Gambar QRIS (opsional)
        <input type="file" name="qris_image" accept="image/*">
        @if ($profile->qris_image)
            <img class="thumb" src="{{ asset('storage/'.$profile->qris_image) }}" alt="QRIS saat ini">
            <span class="check"><input type="checkbox" name="remove_qris" value="1"> Hapus QRIS</span>
        @endif
    </label>

    <label>Kebijakan ongkir (tampil di keranjang dan halaman pesanan)
        <textarea name="shipping_policy" rows="5" maxlength="1000" placeholder="Contoh: Gratis ongkir radius 5 km. Di luar itu ongkir ditentukan admin sesuai jarak.">{{ old('shipping_policy', $profile->shipping_policy) }}</textarea>
    </label>

    <button class="btn btn--primary" type="submit">Simpan</button>
</form>
@endsection
