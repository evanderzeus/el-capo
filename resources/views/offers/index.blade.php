@extends('layouts.app')
@section('title', 'Penawaran')
@section('content')
<h1>Penawaran</h1>
<p class="sub">Kelola penawaran tukar yang masuk dan yang kamu ajukan.</p>
<div class="tabs">
    <button class="tab active" data-t="masuk">Masuk <span id="cm"></span></button>
    <button class="tab" data-t="dikirim">Diajukan <span id="cd"></span></button>
</div>
<div id="list"></div>
@endsection
@push('scripts')
<script>
let data = { masuk: [], dikirim: [] }, tab = 'masuk';

const mini = i => `<a class="mini" href="/items/${i.id}">${thumb(i)}<div><b>${esc(i.nama_barang)}</b><div class="price">${kisaran(i)}</div></div></a>`;

function render() {
    const rows = data[tab];
    cm.textContent = `(${data.masuk.length})`; cd.textContent = `(${data.dikirim.length})`;
    if (!rows.length) { list.innerHTML = '<div class="empty">Belum ada penawaran.</div>'; return; }
    list.innerHTML = rows.map(o => {
        const siapa = tab === 'masuk' ? `<b>@${esc(o.pengaju.username)}</b> menawarkan` : `Kamu menawarkan ke <b>@${esc(o.pemilik.username)}</b>`;
        let aksi = '';
        if (o.can_respond) aksi = `<button class="btn ok sm" onclick="respon(${o.id},'Diterima')">Terima</button>
                                   <button class="btn danger sm" onclick="respon(${o.id},'Ditolak')">Tolak</button>`;
        if (o.can_cancel) aksi = `<button class="btn ghost sm" onclick="batal(${o.id})">Batalkan</button>`;
        const kontak = o.kontak ? `<div class="contact">Hubungi @${esc(o.kontak.username)} di <b>${esc(o.kontak.email)}</b> untuk janjian tukar barang.</div>` : '';
        const ket = o.status_penawaran === 'Ditolak' && tab === 'dikirim'
            ? ` <a class="btn ghost sm" href="/items/${o.barang_tujuan.id}">Ajukan lagi</a>` : '';
        return `<div class="offer">
            <div style="display:flex;justify-content:space-between;gap:8px"><span>${siapa}</span><span class="badge ${esc(o.status_penawaran)}">${esc(o.status_penawaran)}</span></div>
            <div class="swap">${mini(tab === 'masuk' ? o.barang_penawaran : o.barang_penawaran)}<span class="arrow">⇄</span>${mini(o.barang_tujuan)}</div>
            <div class="actions" style="padding:0">${aksi}${ket}</div>${kontak}</div>`;
    }).join('');
}

async function load() {
    try { ({ data } = await api('/offers')); render(); }
    catch (e) { list.innerHTML = `<div class="empty">${esc(e.message)}</div>`; }
}
async function respon(id, status) {
    if (status === 'Diterima' && !confirm('Terima penawaran ini? Kedua barang akan ditandai sudah ditukar.')) return;
    try { await api('/offers?id=' + id, { method: 'PUT', body: { status_penawaran: status } }); toast('Penawaran ' + status.toLowerCase()); load(); }
    catch (e) { toast(e.message, true); }
}
async function batal(id) {
    if (!confirm('Batalkan penawaran ini?')) return;
    try { await api('/offers?id=' + id, { method: 'DELETE' }); toast('Penawaran dibatalkan'); load(); }
    catch (e) { toast(e.message, true); }
}
document.querySelectorAll('.tab').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
    b.classList.add('active'); tab = b.dataset.t; render();
}));
load();
</script>
@endpush
