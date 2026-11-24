@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('heading', 'Edit Kategori: '.$category->category_name)

@section('content')
<form method="post" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="panel form">
    @csrf @method('PUT')

    <label>Nama kategori
        <input type="text" name="category_name" value="{{ old('category_name', $category->category_name) }}" required maxlength="100">
    </label>
    <label>Deskripsi singkat
        <textarea name="description" rows="3" maxlength="500">{{ old('description', $category->description) }}</textarea>
    </label>
    <label>Gambar kategori
        <input type="file" name="image" accept="image/*">
        <small>Disarankan ukuran 4:3, sekitar 800 x 600 px, maksimal 3 MB.</small>
        @if ($category->image)
            <img class="thumb" src="{{ asset('storage/'.$category->image) }}" alt="Gambar saat ini">
            <span class="check"><input type="checkbox" name="remove_image" value="1"> Hapus gambar</span>
        @endif
    </label>

    <div class="form__actions">
        <button class="btn btn--primary" type="submit">Simpan</button>
        <a class="btn" href="{{ route('admin.categories.index') }}">Batal</a>
    </div>
</form>
@endsection
