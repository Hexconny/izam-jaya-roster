@extends('layouts.admin')
@section('title', 'Katalog Produk')
@section('heading', 'Katalog Produk')

@section('content')
<div class="panel">
    <div class="panel__head">
        <form method="get" class="inline">
            <select name="kategori" onchange="this.form.submit()" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('kategori') == $c->id)>{{ $c->category_name }}</option>
                @endforeach
            </select>
        </form>
        <a class="btn btn--primary btn--small" href="{{ route('admin.products.create') }}">+ Tambah produk</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Varian</th><th>Total stok</th><th>Harga mulai</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($products as $p)
                <tr>
                    <td>{{ $p->product_name }}</td>
                    <td>{{ $p->category->category_name }}</td>
                    <td>{{ $p->variants->count() }}</td>
                    <td>{{ $p->variants->sum('stock') }}</td>
                    <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                    <td><span class="pill pill--{{ $p->availability === 'tersedia' ? 'selesai' : 'batal' }}">{{ $p->availability }}</span></td>
                    <td class="actions">
                        <a href="{{ route('admin.products.edit', $p) }}">Edit</a>
                        <form method="post" action="{{ route('admin.products.destroy', $p) }}" onsubmit="return confirm('Hapus produk {{ addslashes($p->product_name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada produk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links('pagination::bootstrap-3') }}
</div>
@endsection
