@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<section class="section">
    <div class="wrap about">
        <div>
            <p class="eyebrow">Tentang kami</p>
            <h1>{{ $profile->business_name }}</h1>
            <div class="prose">{!! nl2br(e($profile->description)) !!}</div>

            <dl class="facts">
                @if ($profile->address)
                    <div><dt>Alamat</dt><dd>{{ $profile->address }}</dd></div>
                @endif
                @if ($profile->open_hours)
                    <div><dt>Jam buka</dt><dd>{{ $profile->open_hours }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="about__art {{ $profile->image ? '' : 'pat-roster' }}">
            @if ($profile->image)
                <img src="{{ asset('storage/'.$profile->image) }}" alt="{{ $profile->business_name }}">
            @endif
        </div>
    </div>
</section>

@if ($profile->map_query)
<section class="section section--tint" id="lokasi">
    <div class="wrap">
        <h2>Lokasi toko</h2>
        <div class="map">
            {{-- Embed Google Maps tanpa API key. Titik diubah lewat admin: Edit Tentang. --}}
            <iframe title="Peta lokasi {{ $profile->business_name }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                src="https://maps.google.com/maps?q={{ urlencode($profile->map_query) }}&output=embed"></iframe>
        </div>
        <p><a class="link" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($profile->map_query) }}">Buka di Google Maps</a></p>
    </div>
</section>
@endif
@endsection
