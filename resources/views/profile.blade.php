@extends('layouts.app')
@section('title', 'Profil')
@section('content')
<h1>Profil Saya</h1>
<p class="sub">Ubah informasi akun dan password.</p>
<form id="f" class="card form">
    <label for="username">Username</label>
    <input id="username" required minlength="3" maxlength="30" pattern="[A-Za-z0-9_\-]+">
    <label for="fullname">Nama lengkap</label>
    <input id="fullname" maxlength="100">
    <label for="email">Email</label>
    <input id="email" type="email" required>
    <h2 style="margin-top:24px">Ganti password <span class="muted" style="font-weight:400">(kosongkan jika tidak diubah)</span></h2>
    <label for="current_password">Password saat ini</label>
    <input id="current_password" type="password" autocomplete="current-password">
    <label for="password">Password baru (min. 8 karakter)</label>
    <input id="password" type="password" minlength="8" autocomplete="new-password">
    <button class="btn" style="margin-top:22px">Simpan Perubahan</button>
</form>
@endsection
@push('scripts')
<script>
(async () => {
    try {
        const { data } = await api('/profile');
        username.value = data.username; fullname.value = data.name || ''; email.value = data.email;
    } catch (e) { toast(e.message, true); }
})();
f.addEventListener('submit', async e => {
    e.preventDefault();
    const body = { username: username.value.trim(), name: fullname.value.trim() || null, email: email.value.trim() };
    if (password.value) { body.password = password.value; body.current_password = current_password.value; }
    const btn = f.querySelector('button'); btn.disabled = true;
    try {
        await api('/profile', { method: 'PUT', body });
        toast('Profil berhasil diperbarui');
        current_password.value = password.value = '';
        setTimeout(() => location.reload(), 800);
    } catch (err) { toast(err.message, true); btn.disabled = false; }
});
</script>
@endpush
