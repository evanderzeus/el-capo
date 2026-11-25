@extends('layouts.app')
@section('title', 'Jelajahi Barang')
@section('content')
<h1>Jelajahi Barang</h1>
<p class="sub">Temukan barang yang ingin kamu tukar.</p>
<div class="toolbar">
    <input id="q" type="search" placeholder="Cari barang…">
    <select id="kondisi">
        <option value="">Semua kondisi</option>
        @foreach (\App\Models\Item::KONDISI as $k)<option>{{ $k }}</option>@endforeach
    </select>
    <a href="/my-items/create" class="btn">+ Tambah Barang</a>
</div>
<div id="list" class="grid"><div class="empty">Memuat…</div></div>
@endsection
@push('scripts')
<script>
async function load() {
    const p = new URLSearchParams();
    if (q.value.trim()) p.set('q', q.value.trim());
    if (kondisi.value) p.set('kondisi', kondisi.value);
    try {
        const { data } = await api('/items?' + p);
        list.innerHTML = data.length ? data.map(i => itemCard(i)).join('')
            : '<div class="empty">Belum ada barang yang cocok.</div>';
    } catch (e) { list.innerHTML = `<div class="empty">${esc(e.message)}</div>`; }
}
q.addEventListener('input', debounce(load));
kondisi.addEventListener('change', load);
load();
</script>
@endpush
