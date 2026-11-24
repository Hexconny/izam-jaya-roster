@extends('layouts.admin')
@section('title', 'Riwayat Stok')
@section('heading', 'Riwayat Stok')

@section('content')
<div class="panel">
    <div class="panel__head">
        <form method="get" class="inline">
            <input type="search" name="q" value="{{ $q }}" placeholder="Cari produk" aria-label="Cari produk">
            <button class="btn btn--small" type="submit">Cari</button>
        </form>
        <a class="btn btn--small" href="{{ route('admin.stock.index') }}">&larr; Stok Barang</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Produk</th><th>Varian</th><th>Perubahan</th><th>Stok akhir</th><th>Alasan</th><th>Catatan</th></tr></thead>
            <tbody>
            @forelse ($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $m->product_name }}</td>
                    <td>{{ $m->variant_label ?: '-' }}</td>
                    <td><b class="{{ $m->change < 0 ? 'neg' : 'pos' }}">{{ $m->change > 0 ? '+' : '' }}{{ $m->change }}</b></td>
                    <td>{{ $m->stock_after }}</td>
                    <td>{{ $m->reasonLabel() }}</td>
                    <td>{{ $m->note }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada riwayat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $movements->links('pagination::bootstrap-3') }}
</div>
@endsection
