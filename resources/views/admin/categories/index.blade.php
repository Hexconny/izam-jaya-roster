@extends('layouts.admin')
@section('title', 'Kategori')
@section('heading', 'Kategori')

@section('content')
<div class="panel">
    <p class="muted">Gambar kategori tampil di kartu "Produk Kami" pada beranda.</p>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Gambar</th><th>Kategori</th><th>Produk</th><th></th></tr></thead>
            <tbody>
            @foreach ($categories as $c)
                <tr>
                    <td>
                        @if ($c->image)
                            <img src="{{ asset('storage/'.$c->image) }}" alt="" width="64" height="48" style="object-fit:cover;border-radius:4px">
                        @else
                            <span class="muted">belum ada</span>
                        @endif
                    </td>
                    <td>{{ $c->category_name }}</td>
                    <td>{{ $c->products_count }}</td>
                    <td><a href="{{ route('admin.categories.edit', $c) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
