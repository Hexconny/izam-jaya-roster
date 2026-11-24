@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="stats">
    <a class="stat stat--link" href="{{ route('admin.orders.index', ['status' => 'menunggu_ongkir']) }}"><span>Perlu ditentukan ongkirnya</span><b>{{ $needShipping }}</b></a>
    <a class="stat stat--link" href="{{ route('admin.orders.index', ['bayar' => 'menunggu_verifikasi']) }}"><span>Bukti bayar perlu diverifikasi</span><b>{{ $needVerify }}</b></a>
    <a class="stat stat--link" href="{{ route('admin.stock.index', ['filter' => 'menipis']) }}"><span>Stok menipis (varian)</span><b>{{ $lowStock }}</b></a>
    <a class="stat stat--link" href="{{ route('admin.stock.index', ['filter' => 'habis']) }}"><span>Stok habis (varian)</span><b>{{ $outStock }}</b></a>
    <div class="stat"><span>Pesanan berjalan</span><b>{{ $inProgress }}</b></div>
    <div class="stat"><span>Produk di katalog</span><b>{{ $productCount }}</b></div>
    <div class="stat"><span>Pembayaran lunas bulan ini</span><b>Rp {{ number_format($monthRevenue, 0, ',', '.') }}</b></div>
</div>

<div class="panel">
    <div class="panel__head">
        <h2>Pesanan terbaru</h2>
        <a class="btn btn--small" href="{{ route('admin.orders.create') }}">+ Input pesanan</a>
    </div>

    @if ($latestOrders->isEmpty())
        <p class="muted">Belum ada pesanan.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Pembayaran</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($latestOrders as $o)
                    <tr>
                        <td>{{ $o->order_code }}</td>
                        <td>{{ $o->customer_name }}</td>
                        <td>Rp {{ number_format($o->grandTotal(), 0, ',', '.') }}</td>
                        <td><span class="pill pill--pay-{{ $o->payment_status }}">{{ $o->paymentLabel() }}</span></td>
                        <td><span class="pill pill--{{ $o->order_status }}">{{ $o->statusLabel() }}</span></td>
                        <td><a href="{{ route('admin.orders.show', $o) }}">Buka</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
