@php($profile = \App\Models\BusinessProfile::current())
@php($wa = \App\Models\Contact::whatsapp())
<footer class="site-footer">
    <div class="wrap footer__grid">
        <div>
            <a href="{{ route('home') }}" class="footer__logo" aria-label="{{ $profile->business_name }}, ke beranda">
                <img src="{{ $profile->logoUrl() }}" alt="{{ $profile->business_name }}" width="148" height="56" loading="lazy">
            </a>
            <p>{{ $profile->address }}</p>
            <p>{{ $profile->open_hours }}</p>
        </div>
        <div>
            <p class="footer__title">Halaman</p>
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('catalog.index') }}">Katalog</a>
            <a href="{{ route('about') }}">Tentang</a>
            <a href="{{ route('cart.index') }}">Keranjang</a>
            <a href="{{ route('track.form') }}">Lacak Pesanan</a>
        </div>
        <div>
            <p class="footer__title">Hubungi kami</p>
            @if ($wa)
                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener">WhatsApp +{{ $wa }}</a>
            @endif
            @if ($profile->instagram)
                <a href="https://instagram.com/{{ ltrim($profile->instagram, '@') }}" target="_blank" rel="noopener">Instagram {{ '@'.ltrim($profile->instagram, '@') }}</a>
            @endif
            <a href="{{ route('admin.login') }}" class="footer__admin">Login admin</a>
        </div>
    </div>
    <div class="wrap footer__copy">&copy; {{ date('Y') }} {{ $profile->business_name }}</div>
</footer>

@if ($wa)
    <a class="wa-float" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Halo Izam Jaya Roster, saya mau tanya produk.') }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
        <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5z"/></svg>
    </a>
@endif
