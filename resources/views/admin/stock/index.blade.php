@extends('layouts.admin')
@section('title', 'Stok Barang')
@section('heading', 'Stok Barang')

@section('content')
<div class="panel">
    <div class="panel__head">
        <form method="get" class="inline">
            <input type="search" name="q" value="{{ $q }}" placeholder="Cari produk" aria-label="Cari produk">
            <select name="filter" onchange="this.form.submit()" aria-label="Filter stok">
                <option value="">Semua stok</option>
                <option value="menipis" @selected($filter === 'menipis')>Menipis ({{ $lowCount }})</option>
                <option value="habis" @selected($filter === 'habis')>Habis ({{ $outCount }})</option>
            </select>
        </form>
        <a class="btn btn--small" href="{{ route('admin.stock.history') }}">Riwayat stok</a>
    </div>

    <p class="muted">Stok berkurang otomatis saat pembayaran diterima dan kembali kalau pesanan lunas dibatalkan. Ubah angka di bawah untuk koreksi (misalnya barang baru datang atau hasil hitung ulang). Stok menipis: {{ \App\Models\ProductVariant::LOW_STOCK }} atau kurang.</p>

    <form method="post" action="{{ route('admin.stock.update') }}">
        @csrf @method('PUT')

        <div class="table-wrap">
            <table>
                <thead><tr><th>Produk</th><th>Kategori</th><th>Varian</th><th>Harga</th><th>Stok</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($variants as $v)
                    <tr>
                        <td><a href="{{ route('admin.products.edit', $v->product) }}">{{ $v->product->product_name }}</a></td>
                        <td>{{ $v->product->category->category_name }}</td>
                        <td>{{ $v->label() ?: '-' }}</td>
                        <td>Rp {{ number_format($v->price, 0, ',', '.') }}</td>
                        <td><input class="stock-input" type="number" name="stock[{{ $v->id }}]" value="{{ old('stock.'.$v->id, $v->stock) }}" min="0" step="1" aria-label="Stok {{ $v->product->product_name }} {{ $v->label() }}"></td>
                        <td>
                            @if ($v->stock === 0)<span class="pill pill--batal">Habis</span>
                            @elseif ($v->isLowStock())<span class="pill pill--menunggu_ongkir">Menipis</span>
                            @else<span class="pill pill--selesai">Aman</span>@endif
                            @unless ($v->is_available)<span class="pill">dimatikan</span>@endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted">Tidak ada varian yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($variants->isNotEmpty())
            <div class="form form__actions" style="max-width:none">
                <input type="text" name="note" maxlength="255" placeholder="Catatan (opsional), contoh: barang baru datang" style="max-width:360px;margin:0">
                <button class="btn btn--primary" type="submit">Simpan perubahan stok</button>
            </div>
        @endif
    </form>

    {{ $variants->links('pagination::bootstrap-3') }}
</div>
@endsection
