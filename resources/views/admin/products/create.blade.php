@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('heading', 'Tambah Produk')

@section('content')
<form method="post" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="panel form">
    @csrf
    @include('admin.products._form')
    <div class="form__actions">
        <button class="btn btn--primary" type="submit">Simpan produk</button>
        <a class="btn" href="{{ route('admin.products.index') }}">Batal</a>
    </div>
</form>
@endsection
