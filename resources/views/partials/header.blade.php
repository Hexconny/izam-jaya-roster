@php($profile = \App\Models\BusinessProfile::current())
<header class="site-header">
    <div class="topbar">
        <div class="wrap">
            <span>{{ $profile->address ?: 'Toko roster, asbes, boeven' }}</span>
            <span class="topbar__right">
                @if ($profile->open_hours){{ $profile->open_hours }} &nbsp;|&nbsp; @endif
                @if ($waNumber = \App\Models\Contact::whatsapp())WA +{{ $waNumber }}@endif
                @if ($profile->instagram) &nbsp;|&nbsp; IG {{ '@'.ltrim($profile->instagram, '@') }}@endif
            </span>
        </div>
    </div>

    <div class="wrap nav">
        <a href="{{ route('home') }}" class="brand" aria-label="Izam Jaya Roster, ke beranda">
            <img class="brand__logo" src="{{ $profile->logoUrl() }}" alt="{{ $profile->business_name }}" width="148" height="56">
        </a>

        <button class="nav__toggle" type="button" aria-expanded="false" aria-controls="nav-menu" data-nav-toggle>Menu</button>

        <nav id="nav-menu" class="nav__menu" data-nav-menu>
            <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Beranda</a>
            <a href="{{ route('catalog.index') }}" @class(['is-active' => request()->routeIs('catalog.*', 'product.*')])>Katalog</a>
            <a href="{{ route('about') }}" @class(['is-active' => request()->routeIs('about')])>Tentang</a>
            <a href="{{ route('track.form') }}" @class(['is-active' => request()->routeIs('track.*')])>Lacak Pesanan</a>
            <a href="{{ route('cart.index') }}" @class(['nav__cart', 'is-active' => request()->routeIs('cart.*')])>
                Keranjang
                <span class="badge" data-cart-count>{{ $cart->count() }}</span>
            </a>
        </nav>
    </div>
</header>
