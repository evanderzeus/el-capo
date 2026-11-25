@extends('layouts.app')
@section('title', 'Barang Saya')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap">
    <div><h1>Barang Saya</h1><p class="sub">Barang yang kamu tawarkan untuk ditukar.</p></div>
    <a href="/my-items/create" class="btn">+ Tambah Barang</a>
</div>
<div id="list" class="grid"><div class="empty">Memuat…</div></div>
@endsection
@push('scripts')
<script>
async function load() {
    try {
        const { data } = await api('/items?mine=1');
        list.innerHTML = data.length ? data.map(i => itemCard(i, `<div class="actions">
            <a class="btn ghost sm" href="/my-items/${i.id}/edit">Edit</a>
            <button class="btn danger sm" onclick="hapus(${i.id})">Hapus</button></div>`)).join('')
            : '<div class="empty">Kamu belum menambahkan barang.</div>';
    } catch (e) { list.innerHTML = `<div class="empty">${esc(e.message)}</div>`; }
}
async function hapus(id) {
    if (!confirm('Hapus barang ini? Penawaran terkait juga ikut terhapus.')) return;
    try { await api('/items?id=' + id, { method: 'DELETE' }); toast('Barang dihapus'); load(); }
    catch (e) { toast(e.message, true); }
}
load();
</script>
@endpush
