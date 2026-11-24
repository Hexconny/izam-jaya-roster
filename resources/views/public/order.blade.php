@extends('layouts.app')

@section('title', 'Pesanan '.$order->order_code)

@section('content')
@php
    $steps = [
        ['Pesanan dibuat', true],
        [$order->isPickup() ? 'Ambil di toko (tanpa ongkir)' : 'Ongkir ditentukan', $order->shipping_cost !== null],
        ['Pembayaran lunas', $order->payment_status === 'lunas'],
        ['Diproses', in_array($order->order_status, ['diproses', 'dikirim', 'selesai'])],
        [$order->isPickup() ? 'Siap diambil' : 'Dikirim', in_array($order->order_status, ['dikirim', 'selesai'])],
        ['Selesai', $order->order_status === 'selesai'],
    ];
@endphp

<section class="section">
    <div class="wrap order">
        <div class="order__head">
            <div>
                <p class="eyebrow">Pesanan</p>
                <h1>{{ $order->order_code }}</h1>
                <p class="muted">Dibuat {{ $order->order_date->format('d M Y, H:i') }} &middot; {{ $order->customer_name }}</p>
            </div>
            <div class="order__pills">
                <span class="pill pill--{{ $order->order_status }}">{{ $order->statusLabel() }}</span>
                <span class="pill pill--pay-{{ $order->payment_status }}">{{ $order->paymentLabel() }}</span>
            </div>
        </div>

        @if ($order->order_status === 'batal')
            <div class="flash flash--err">Pesanan ini dibatalkan. Kalau kamu masih membutuhkannya, buat pesanan baru atau hubungi admin.</div>
        @else
            <ol class="timeline">
                @foreach ($steps as [$label, $done])
                    <li @class(['is-done' => $done])>{{ $label }}</li>
                @endforeach
            </ol>
        @endif

        <div class="order__grid">
            <div>
                <h2>Rincian pesanan</h2>
                <ul class="recap">
                    @foreach ($order->details as $d)
                        <li><span>{{ $d->product_name }} <small>{{ $d->variant_label }}</small> &times; {{ $d->quantity }}</span><b>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</b></li>
                    @endforeach
                    <li><span>Total barang</span><b>Rp {{ number_format($order->total_price, 0, ',', '.') }}</b></li>
                    <li>
                        <span>Ongkos kirim @if ($order->shipping_note)<small>{{ $order->shipping_note }}</small>@endif</span>
                        <b>
                            @if ($order->shipping_cost === null) Menunggu admin
                            @elseif ((float) $order->shipping_cost == 0) Gratis
                            @else Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                            @endif
                        </b>
                    </li>
                    <li class="recap__total"><span>Total bayar</span><b>
                        Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}@if ($order->shipping_cost === null) <small>(belum termasuk ongkir)</small>@endif
                    </b></li>
                </ul>

                <h2>{{ $order->isPickup() ? 'Pengambilan' : 'Alamat pengiriman' }}</h2>
                <p>{{ $order->isPickup() ? 'Diambil di toko.' : $order->customer_address }}</p>
            </div>

            <aside class="payment">
                @if ($order->order_status === 'menunggu_ongkir')
                    <h2>Menunggu ongkir</h2>
                    <p>Admin sedang menghitung ongkir sesuai alamat dan jumlah barang. Buka lagi halaman ini nanti untuk membayar.</p>
                    @if ($profile->shipping_policy)<p class="hint">{!! nl2br(e($profile->shipping_policy)) !!}</p>@endif

                @elseif ($order->canPay())
                    <h2>Pembayaran</h2>
                    <p class="payment__amount">Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}</p>

                    @if ($order->payment_status === 'ditolak')
                        <div class="flash flash--err"><strong>Bukti sebelumnya ditolak:</strong> {{ $order->payment_note }}. Upload bukti yang baru.</div>
                    @endif

                    <dl class="facts">
                        @if ($profile->bank_account)
                            <div><dt>Bank</dt><dd>{{ $profile->bank_name }}</dd></div>
                            <div><dt>No. rekening</dt><dd><b>{{ $profile->bank_account }}</b></dd></div>
                            <div><dt>Atas nama</dt><dd>{{ $profile->bank_holder }}</dd></div>
                        @else
                            <div><dt>Rekening</dt><dd>Belum diatur admin. Hubungi admin lewat WhatsApp.</dd></div>
                        @endif
                    </dl>
                    @if ($profile->qris_image)
                        <img class="payment__qris" src="{{ asset('storage/'.$profile->qris_image) }}" alt="QRIS pembayaran">
                    @endif

                    @if ($order->pay_deadline)
                        <p class="hint">Bayar sebelum <strong>{{ $order->pay_deadline->format('d M Y, H:i') }}</strong>, kalau lewat pesanan otomatis dibatalkan.</p>
                    @endif

                    <form method="post" action="{{ route('track.proof', $order->order_code) }}" enctype="multipart/form-data">
                        @csrf
                        <label>Upload bukti transfer (JPG, PNG, atau PDF, maks 3 MB)
                            <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required>
                        </label>
                        <button class="btn btn--primary btn--block" type="submit">Kirim bukti pembayaran</button>
                    </form>

                @elseif ($order->payment_status === 'menunggu_verifikasi')
                    <h2>Bukti diterima</h2>
                    <p>Admin sedang memeriksa pembayaranmu. Status akan berubah di halaman ini setelah diverifikasi.</p>

                @elseif ($order->payment_status === 'lunas')
                    <h2>Pembayaran diterima</h2>
                    <p>Terima kasih. Dibayar {{ optional($order->paid_at)->format('d M Y, H:i') }}.</p>
                @endif

                <a class="btn btn--ghost btn--block" href="{{ $waLink }}" target="_blank" rel="noopener">Tanya admin lewat WhatsApp</a>
            </aside>
        </div>
    </div>
</section>
@endsection
