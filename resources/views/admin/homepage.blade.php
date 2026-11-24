@extends('layouts.admin')
@section('title', 'Edit Beranda')
@section('heading', 'Edit Beranda')

@section('content')
<form method="post" action="{{ route('admin.homepage.update') }}" enctype="multipart/form-data" class="panel form">
    @csrf @method('PUT')

    <label>Judul besar di beranda
        <input type="text" name="hero_title" value="{{ old('hero_title', $profile->hero_title) }}" required maxlength="150">
    </label>
    <label>Kalimat pengantar
        <textarea name="hero_subtitle" rows="3" maxlength="300">{{ old('hero_subtitle', $profile->hero_subtitle) }}</textarea>
    </label>

    <label>Gambar banner beranda
        <input type="file" name="hero_image" accept="image/*">
        <small>Disarankan landscape, sekitar 1600 x 700 px, maksimal 3 MB. Kosongkan kalau tidak ingin mengganti.</small>
        @if ($profile->hero_image)
            <img class="thumb" src="{{ asset('storage/'.$profile->hero_image) }}" alt="Banner saat ini">
            <span class="check"><input type="checkbox" name="remove_hero_image" value="1"> Hapus banner (kembali ke pola)</span>
        @endif
    </label>

    <label>Nomor WhatsApp admin
        <input type="tel" name="whatsapp" value="{{ old('whatsapp', $whatsapp) }}" required placeholder="081234567890">
        <small>Semua tombol WhatsApp di website memakai nomor ini.</small>
    </label>

    <button class="btn btn--primary" type="submit">Simpan perubahan</button>
</form>
@endsection
