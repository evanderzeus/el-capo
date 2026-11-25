@extends('layouts.app')
@section('title', 'Detail Barang')
@section('content')
<p><a href="/" class="muted">← Kembali</a></p>
<div id="detail"><div class="empty">Memuat…</div></div>

<dialog id="dlg">
    <h2>Ajukan Penawaran</h2>
    <p class="sub" id="dlgTarget"></p>
    <label for="mine">Barang yang kamu tawarkan</label>
    <select id="mine"></select>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:20px">
        <button class="btn ghost" onclick="dlg.close()">Batal</button>
        <button class="btn" id="send">Kirim Penawaran</button>
    </div>
</dialog>
@endsection
@push('scripts')
<script>
const ID = {{ $id }};
let item;

async function load() {
    try {
        ({ data: item } = await api('/items?id=' + ID));
    } catch (e) { detail.innerHTML = `<div class="empty">${esc(e.message)}</div>`; return; }

    let aksi = '';
    if (item.is_mine) {
        aksi = `<a class="btn" href="/my-items/${item.id}/edit">Edit</a>
                <button class="btn danger" onclick="hapus()">Hapus</button>`;
    } else if (item.status === 'tersedia') {
        aksi = `<button class="btn" onclick="openOffer()">Ajukan Penawaran</button>`;
    } else {
        aksi = `<span class="muted">Barang ini sudah ditukar.</span>`;
    }
    detail.innerHTML = `<div class="detail">
        ${thumb(item)}
        <div>
            <h1>${esc(item.nama_barang)}</h1>
            <p><span class="badge">${esc(item.kondisi)}</span> <span class="badge ${esc(item.status)}">${item.status === 'tersedia' ? 'Tersedia' : 'Sudah ditukar'}</span></p>
            <p class="price">Kisaran harga: <b>${kisaran(item)}</b></p>
            <p style="white-space:pre-line">${esc(item.deskripsi)}</p>
            <p class="muted">Pemilik: @${esc(item.pemilik?.username)}</p>
            <div class="actions" style="padding:0">${aksi}</div>
        </div></div>`;
}

async function openOffer() {
    try {
        const { data } = await api('/items?mine=1&status=tersedia');
        if (!data.length) { toast('Kamu belum punya barang yang tersedia. Tambahkan dulu.', true); return; }
        mine.innerHTML = data.map(i => `<option value="${i.id}">${esc(i.nama_barang)} (${kisaran(i)})</option>`).join('');
        dlgTarget.textContent = 'Untuk: ' + item.nama_barang;
        dlg.showModal();
    } catch (e) { toast(e.message, true); }
}

send.addEventListener('click', async () => {
    send.disabled = true;
    try {
        await api('/offers', { method: 'POST', body: { barang_tujuan_id: item.id, barang_penawaran_id: +mine.value } });
        dlg.close(); toast('Penawaran berhasil diajukan');
        setTimeout(() => location.href = '/offers', 900);
    } catch (e) { toast(e.message, true); } finally { send.disabled = false; }
});

async function hapus() {
    if (!confirm('Hapus barang ini? Penawaran terkait juga ikut terhapus.')) return;
    try { await api('/items?id=' + ID, { method: 'DELETE' }); location.href = '/my-items'; }
    catch (e) { toast(e.message, true); }
}
load();
</script>
@endpush
