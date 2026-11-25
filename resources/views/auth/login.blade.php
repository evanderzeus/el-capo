@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="auth card">
    <h1>TukerYuk!</h1>
    <p class="sub" style="text-align:center">Tukar barang antar mahasiswa Unpad Jatinangor</p>
    <form id="f">
        <label for="email">Email / Username</label>
        <input id="email" name="email" required autofocus autocomplete="username">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password">
        <label class="check"><input type="checkbox" id="remember"> Ingat saya (7 hari)</label>
        <button class="btn" style="width:100%;margin-top:18px">Masuk</button>
    </form>
    <p class="sub" style="text-align:center;margin:16px 0 0">Belum punya akun? <a href="/register"><b>Daftar</b></a></p>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('f').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = e.target.querySelector('button'); btn.disabled = true;
    try {
        await api('/login', { method: 'POST', body: {
            email: email.value.trim(), password: password.value, remember: remember.checked,
        }});
        location.href = '/';
    } catch (err) { toast(err.message, true); btn.disabled = false; }
});
</script>
@endpush
