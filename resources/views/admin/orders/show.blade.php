@extends('layouts.admin')
@section('title', 'Pesanan '.$order->order_code)
@section('heading', 'Pesanan '.$order->order_code)

@section('content')
<div class="two">
    <div class="panel">
        <h2>Pelanggan</h2>
        <dl class="facts">
            <div><dt>Nama</dt><dd>{{ $order->customer_name }}</dd></div>
            <div><dt>No. HP</dt><dd>{{ $order->customer_phone }}</dd></div>
            <div><dt>Alamat</dt><dd>{{ $order->customer_address }}</dd></div>
            <div><dt>Penerimaan</dt><dd>{{ $order->isPickup() ? 'Ambil di toko' : 'Dikirim' }}</dd></div>
            <div><dt>Tanggal</dt><dd>{{ $order->order_date->format('d M Y H:i') }}</dd></div>
            <div><dt>Sumber</dt><dd>{{ $order->source === 'web' ? 'Website' : 'Input admin' }}</dd></div>
        </dl>
        <a class="btn btn--small" href="{{ $waLink }}" target="_blank" rel="noopener">Chat pelanggan di WhatsApp</a>
    </div>

    <div class="panel">
        <h2>Status</h2>
        <p>
            <span class="pill pill--{{ $order->order_status }}">{{ $order->statusLabel() }}</span>
            <span class="pill pill--pay-{{ $order->payment_status }}">{{ $order->paymentLabel() }}</span>
        </p>
        @if ($order->pay_deadline && $order->canPay())
            <p class="muted">Batas bayar: {{ $order->pay_deadline->format('d M Y H:i') }}</p>
        @endif
        @if ($order->paid_at)
            <p class="muted">Dibayar: {{ $order->paid_at->format('d M Y H:i') }}</p>
        @endif
        <p class="muted">Stok: {{ $order->stock_deducted ? 'sudah dikurangi' : 'belum dikurangi (berkurang saat pembayaran diterima)' }}</p>

        <form method="post" action="{{ route('admin.orders.update', $order) }}" class="form">
            @csrf @method('PUT')
            <label>Status pesanan
                <select name="order_status">
                    @foreach (\App\Models\Order::STATUS_LABELS as $key => $label)
                        <option value="{{ $key }}" @selected($order->order_status === $key)>{{ $key === 'dikirim' ? 'Dikirim / Siap diambil' : $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Status pembayaran
                <select name="payment_status">
                    @foreach (\App\Models\Order::PAYMENT_LABELS as $key => $label)
                        <option value="{{ $key }}" @selected($order->payment_status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Catatan internal
                <textarea name="notes" rows="2" maxlength="500">{{ old('notes', $order->notes) }}</textarea>
            </label>
            <button class="btn btn--primary btn--small" type="submit">Simpan status</button>
        </form>
    </div>
</div>

<div class="two">
    <div class="panel">
        <h2>Ongkir</h2>
        @if ($order->isPickup())
            <p class="muted">Ambil di toko, tanpa ongkir.</p>
        @else
            @if ($order->shipping_cost === null)
                <div class="flash flash--err">Ongkir belum ditentukan. Pelanggan belum bisa membayar.</div>
            @endif
            <form method="post" action="{{ route('admin.orders.shipping', $order) }}" class="form">
                @csrf @method('PUT')
                <label>Ongkos kirim (Rp, isi 0 jika gratis)
                    <input type="number" name="shipping_cost" min="0" step="1000" required value="{{ old('shipping_cost', $order->shipping_cost !== null ? (int) $order->shipping_cost : '') }}">
                </label>
                <label>Catatan ongkir (tampil ke pelanggan)
                    <input type="text" name="shipping_note" maxlength="255" value="{{ old('shipping_note', $order->shipping_note) }}" placeholder="Contoh: jarak 12 km, mobil bak">
                </label>
                <button class="btn btn--primary btn--small" type="submit">Simpan ongkir</button>
            </form>
        @endif
    </div>

    <div class="panel">
        <h2>Bukti pembayaran</h2>
        @if ($order->payment_proof)
            <p><a href="{{ route('admin.orders.proof', $order) }}" target="_blank" rel="noopener">Buka bukti pembayaran</a></p>
        @else
            <p class="muted">Belum ada bukti yang diupload.</p>
        @endif

        @if ($order->payment_note)
            <p class="muted">Alasan penolakan sebelumnya: {{ $order->payment_note }}</p>
        @endif

        @if ($order->payment_status === 'menunggu_verifikasi')
            <p>Total yang harus masuk: <strong>Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}</strong>. Cocokkan dengan mutasi rekening.</p>
            <form method="post" action="{{ route('admin.orders.verify', $order) }}" class="form">
                @csrf @method('PUT')
                <label>Alasan penolakan (wajib kalau menolak)
                    <input type="text" name="reason" maxlength="255" placeholder="Contoh: nominal kurang, bukti tidak terbaca">
                </label>
                <div class="form__actions">
                    <button class="btn btn--primary btn--small" type="submit" name="decision" value="terima">Terima pembayaran</button>
                    <button class="danger" type="submit" name="decision" value="tolak">Tolak bukti</button>
                </div>
            </form>
        @endif
    </div>
</div>

<div class="panel">
    <h2>Rincian pesanan</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Produk</th><th>Varian</th><th>Harga</th><th>Jml</th><th>Subtotal</th></tr></thead>
            <tbody>
            @foreach ($order->details as $d)
                <tr>
                    <td>{{ $d->product_name }}</td>
                    <td>{{ $d->variant_label }}</td>
                    <td>Rp {{ number_format($d->price, 0, ',', '.') }}</td>
                    <td>{{ $d->quantity }}</td>
                    <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="4">Total barang</th><th>Rp {{ number_format($order->total_price, 0, ',', '.') }}</th></tr>
                <tr><th colspan="4">Ongkir</th><th>{{ $order->shipping_cost === null ? 'belum ditentukan' : 'Rp '.number_format($order->shipping_cost, 0, ',', '.') }}</th></tr>
                <tr><th colspan="4">Total bayar</th><th>Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}</th></tr>
            </tfoot>
        </table>
    </div>

    <form method="post" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Hapus pesanan ini?')" class="form__actions">
        @csrf @method('DELETE')
        <button type="submit" class="danger">Hapus pesanan</button>
    </form>
</div>
@endsection
