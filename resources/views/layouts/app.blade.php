<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TukerYuk!') · TukerYuk!</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
@auth
<nav class="nav">
    <div class="container">
        <a href="/" class="brand">TukerYuk!</a>
        <a class="link {{ request()->is('/') || request()->is('items/*') ? 'active' : '' }}" href="/">Jelajahi</a>
        <a class="link {{ request()->is('my-items*') ? 'active' : '' }}" href="/my-items">Barang Saya</a>
        <a class="link {{ request()->is('offers') ? 'active' : '' }}" href="/offers">Penawaran</a>
        <span class="spacer"></span>
        <a class="link {{ request()->is('profile') ? 'active' : '' }}" href="/profile">&#64;{{ auth()->user()->username }}</a>
        <button class="btn ghost sm" onclick="logout()">Keluar</button>
    </div>
</nav>
@endauth
<main class="container">
    @yield('content')
</main>
<script src="/js/app.js"></script>
@stack('scripts')
</body>
</html>
