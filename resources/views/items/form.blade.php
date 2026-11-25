@extends('layouts.app')
@section('title', $id ? 'Edit Barang' : 'Tambah Barang')
@section('content')
<h1>{{ $id ? 'Edit Barang' : 'Tambah Barang' }}</h1>
<p class="sub">Isi informasi barang yang ingin kamu tukar.</p>
<form id="f" class="card form">
    <label for="nama_barang">Nama barang</label>
    <input id="nama_barang" required maxlength="100">
    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" rows="4" required maxlength="2000"></textarea>
    <label for="kondisi">Kondisi</label>
    <select id="kondisi" required>
        @foreach (\App\Models\Item::KONDISI as $k)<option>{{ $k }}</option>@endforeach
    </select>
    <div class="row">
        <div><label for="harga_min">Harga min (Rp)</label><input id="harga_min" type="number" min="0" required></div>
        <div><label for="harga_max">Harga maks (Rp)</label><input id="harga_max" type="number" min="0" required></div>
    </div>
    <label for="foto">Foto (JPG/PNG/WEBP, maks 2 MB)</label>
    <input id="foto" type="file" accept="image/jpeg,image/png,image/webp">
    <img id="preview" class="preview" alt="Pratinjau">
    <div style="display:flex;gap:8px;margin-top:22px">
        <button class="btn">Simpan</button>
        <a href="/my-items" class="btn ghost">Batal</a>
    </div>
</form>
@endsection
@push('scripts')
<script>
const ID = {{ $id ?? 'null' }};

foto.addEventListener('change', () => {
    const f = foto.files[0];
    if (f) { preview.src = URL.createObjectURL(f); preview.style.display = 'block'; }
});

async function init() {
    if (!ID) return;
    try {
        const { data: i } = await api('/items?id=' + ID);
        if (!i.is_mine) { location.href = '/my-items'; return; }
        nama_barang.value = i.nama_barang; deskripsi.value = i.deskripsi; kondisi.value = i.kondisi;
        harga_min.value = i.harga_min; harga_max.value = i.harga_max;
        if (i.foto_url) { preview.src = i.foto_url; preview.style.display = 'block'; }
    } catch (e) { toast(e.message, true); }
}

f.addEventListener('submit', async e => {
    e.preventDefault();
    if (+harga_max.value < +harga_min.value) { toast('Harga maks harus ≥ harga min', true); return; }
    const fd = new FormData();
    fd.append('nama_barang', nama_barang.value.trim());
    fd.append('deskripsi', deskripsi.value.trim());
    fd.append('kondisi', kondisi.value);
    fd.append('harga_min', harga_min.value);
    fd.append('harga_max', harga_max.value);
    if (foto.files[0]) fd.append('foto', foto.files[0]);
    const btn = f.querySelector('button'); btn.disabled = true;
    try {
        if (ID) { fd.append('_method', 'PUT'); await api('/items?id=' + ID, { method: 'POST', body: fd }); }
        else { await api('/items', { method: 'POST', body: fd }); }
        toast('Barang berhasil disimpan');
        setTimeout(() => location.href = '/my-items', 700);
    } catch (err) { toast(err.message, true); btn.disabled = false; }
});
init();
</script>
@endpush
