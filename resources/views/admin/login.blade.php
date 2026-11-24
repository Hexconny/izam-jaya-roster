<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Login Admin | Izam Jaya Roster</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,800&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="login">
    <div class="login__art" aria-hidden="true"></div>
    <main class="login__box">
        <img class="login__logo" src="{{ \App\Models\BusinessProfile::current()->logoUrl() }}" alt="Izam Jaya Roster" width="220" height="83">
        <h1>Masuk admin</h1>

        @if (session('error'))<div class="flash flash--err" role="alert">{{ session('error') }}</div>@endif
        @if (session('success'))<div class="flash flash--ok" role="status">{{ session('success') }}</div>@endif
        @error('username')<div class="flash flash--err">{{ $message }}</div>@enderror
        @error('password')<div class="flash flash--err">{{ $message }}</div>@enderror

        <form method="post" action="{{ route('admin.login.submit') }}">
            @csrf
            <label>Username
                <input type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username">
            </label>
            <label>Password
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            <button class="btn btn--primary btn--block" type="submit">Login</button>
        </form>
        <a class="back" href="{{ route('home') }}">&larr; Kembali ke website</a>
    </main>
</body>
</html>
