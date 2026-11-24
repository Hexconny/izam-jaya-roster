<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | Izam Jaya Roster</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin">
    <aside class="side">
        <a href="{{ route('admin.dashboard') }}" class="side__brand" aria-label="Dashboard admin">
            <img src="{{ \App\Models\BusinessProfile::current()->logoUrl() }}" alt="Izam Jaya Roster" width="170" height="64">
            <small>Admin</small>
        </a>
        <nav>
            <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
            <a href="{{ route('admin.homepage.edit') }}" @class(['is-active' => request()->routeIs('admin.homepage.*')])>Edit Beranda</a>
            <a href="{{ route('admin.about.edit') }}" @class(['is-active' => request()->routeIs('admin.about.*')])>Edit Tentang</a>
            <a href="{{ route('admin.categories.index') }}" @class(['is-active' => request()->routeIs('admin.categories.*')])>Kategori</a>
            <a href="{{ route('admin.products.index') }}" @class(['is-active' => request()->routeIs('admin.products.*')])>Katalog Produk</a>
            <a href="{{ route('admin.stock.index') }}" @class(['is-active' => request()->routeIs('admin.stock.*')])>Stok Barang</a>
            <a href="{{ route('admin.payment.edit') }}" @class(['is-active' => request()->routeIs('admin.payment.*')])>Pembayaran &amp; Ongkir</a>
            <a href="{{ route('admin.orders.index') }}" @class(['is-active' => request()->routeIs('admin.orders.*')])>Pesanan</a>
        </nav>
        <div class="side__foot">
            <a href="{{ route('home') }}" target="_blank" rel="noopener">Lihat website</a>
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <div class="content">
        <header class="content__head">
            <h1>@yield('heading', 'Admin')</h1>
            <span>{{ auth()->user()->name }}</span>
        </header>

        @if (session('success'))<div class="flash flash--ok" role="status">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="flash flash--err" role="alert">{{ session('error') }}</div>@endif
        @if ($errors->any())
            <div class="flash flash--err" role="alert">
                <strong>Ada isian yang perlu diperbaiki:</strong>
                <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
