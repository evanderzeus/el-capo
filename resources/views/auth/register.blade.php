@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="auth card">
    <h1>Buat Akun</h1>
    <p class="sub" style="text-align:center">Khusus mahasiswa Unpad</p>
    <form id="f">
        <label for="username">Username</label>
        <input id="username" required minlength="3" maxlength="30" pattern="[A-Za-z0-9_\-]+" title="Huruf, angka, - dan _">
        <label for="fullname">Nama lengkap (opsional)</label>
        <input id="fullname" maxlength="100">
        <label for="email">Email</label>
        <input id="email" type="email" required>
        <label for="password">Password (min. 8 karakter)</label>
        <input id="password" type="password" required minlength="8">
        <button class="btn" style="width:100%;margin-top:18px">Daftar</button>
    </form>
    <p class="sub" style="text-align:center;margin:16px 0 0">Sudah punya akun? <a href="/login"><b>Masuk</b></a></p>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('f').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = e.target.querySelector('button'); btn.disabled = true;
    try {
        await api('/register', { method: 'POST', body: {
            username: username.value.trim(), name: fullname.value.trim() || null,
            email: email.value.trim(), password: password.value,
        }});
        toast('Registrasi berhasil, silakan masuk');
        setTimeout(() => location.href = '/login', 900);
    } catch (err) { toast(err.message, true); btn.disabled = false; }
});
</script>
@endpush
