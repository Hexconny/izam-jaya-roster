@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('heading', 'Edit Produk')

@section('content')
<form method="post" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="panel form">
    @csrf @method('PUT')
    @include('admin.products._form')
    <div class="form__actions">
        <button class="btn btn--primary" type="submit">Simpan perubahan</button>
        <a class="btn" href="{{ route('admin.products.index') }}">Batal</a>
    </div>
</form>
@endsection
