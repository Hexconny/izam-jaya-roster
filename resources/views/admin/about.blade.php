@extends('layouts.admin')
@section('title', 'Edit Tentang')
@section('heading', 'Edit Tentang')

@section('content')
<form method="post" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" class="panel form">
    @csrf @method('PUT')

    <label>Nama usaha
        <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name) }}" required maxlength="100">
    </label>
    <label>Deskripsi usaha
        <textarea name="description" rows="6" maxlength="3000">{{ old('description', $profile->description) }}</textarea>
    </label>
    <label>Alamat toko
        <textarea name="address" rows="2" maxlength="500">{{ old('address', $profile->address) }}</textarea>
    </label>

    <div class="row">
        <label>Jam buka
            <input type="text" name="open_hours" value="{{ old('open_hours', $profile->open_hours) }}" placeholder="Senin - Sabtu, 08.00 - 17.00">
        </label>
        <label>Instagram (tanpa @)
            <input type="text" name="instagram" value="{{ old('instagram', $profile->instagram) }}">
        </label>
    </div>

    <label>Lokasi untuk peta
        <input type="text" name="map_query" value="{{ old('map_query', $profile->map_query) }}" placeholder="Nama tempat atau alamat lengkap, seperti yang kamu cari di Google Maps">
        <small>Peta di halaman Tentang akan menampilkan titik ini.</small>
    </label>

    <label>Logo toko
        <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml">
        <small>Tampil di header, footer, dan halaman login admin. PNG latar transparan, memanjang sekitar 450 x 170 px, maksimal 2 MB.</small>
        <img class="thumb thumb--logo" src="{{ $profile->logoUrl() }}" alt="Logo saat ini">
        @if ($profile->logo)
            <span class="check"><input type="checkbox" name="remove_logo" value="1"> Hapus logo upload (kembali ke logo bawaan)</span>
        @endif
    </label>

    <label>Foto usaha
        <input type="file" name="image" accept="image/*">
        @if ($profile->image)
            <img class="thumb" src="{{ asset('storage/'.$profile->image) }}" alt="Foto usaha saat ini">
        @endif
    </label>

    <button class="btn btn--primary" type="submit">Simpan perubahan</button>
</form>
@endsection
