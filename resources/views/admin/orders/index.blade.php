@extends('layouts.admin')
@section('title', 'Pesanan')
@section('heading', 'Pesanan')

@section('content')
<div class="panel">
    <div class="panel__head">
        <form method="get" class="inline">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / kode" aria-label="Cari pesanan">
            <select name="status" onchange="this.form.submit()" aria-label="Filter status">
                <option value="">Semua status</option>
                @foreach (\App\Models\Order::STATUS_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="bayar" onchange="this.form.submit()" aria-label="Filter pembayaran">
                <option value="">Semua pembayaran</option>
                @foreach (\App\Models\Order::PAYMENT_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('bayar') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <a class="btn btn--primary btn--small" href="{{ route('admin.orders.create') }}">+ Input pesanan</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Tanggal</th><th>Pelanggan</th><th>Total</th><th>Pembayaran</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($orders as $o)
                <tr>
                    <td>{{ $o->order_code }}</td>
                    <td>{{ $o->order_date->format('d M Y H:i') }}</td>
                    <td>{{ $o->customer_name }}</td>
                    <td>Rp {{ number_format($o->grandTotal(), 0, ',', '.') }}@if ($o->shipping_cost === null) <small class="muted">+ ongkir</small>@endif</td>
                    <td><span class="pill pill--pay-{{ $o->payment_status }}">{{ $o->paymentLabel() }}</span></td>
                    <td><span class="pill pill--{{ $o->order_status }}">{{ $o->statusLabel() }}</span></td>
                    <td><a href="{{ route('admin.orders.show', $o) }}">Buka</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $orders->links('pagination::bootstrap-3') }}
</div>
@endsection
