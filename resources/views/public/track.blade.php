@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<section class="section">
    <div class="wrap track">
        <p class="eyebrow">Lacak pesanan</p>
        <h1>Cek status pesananmu</h1>
        <p class="lead">Masukkan kode pesanan dan nomor HP yang kamu pakai saat memesan. Tanpa login.</p>

        <form method="post" action="{{ route('track.lookup') }}" class="track__form">
            @csrf
            <label>Kode pesanan
                <input type="text" name="kode" value="{{ old('kode') }}" required maxlength="30" placeholder="IJR-20261115-4821" autocomplete="off">
            </label>
            <label>Nomor HP
                <input type="tel" name="hp" value="{{ old('hp') }}" required maxlength="20" placeholder="08xxxxxxxxxx" inputmode="tel">
            </label>
            <button class="btn btn--primary" type="submit">Lihat pesanan</button>
        </form>
        <p class="hint">Kode pesanan hilang? Hubungi admin lewat tombol WhatsApp di pojok kanan bawah.</p>
    </div>
</section>
@endsection
