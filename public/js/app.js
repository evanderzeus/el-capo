/* Helper bersama: pemanggilan API (session + CSRF), toast, dan komponen kartu. */
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

async function api(path, { method = 'GET', body = null } = {}) {
  const opts = { method, credentials: 'same-origin', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF } };
  if (body instanceof FormData) {
    opts.body = body;
  } else if (body) {
    opts.headers['Content-Type'] = 'application/json';
    opts.body = JSON.stringify(body);
  }
  const res = await fetch('/api' + path, opts);
  let data = {};
  try { data = await res.json(); } catch (_) {}
  if (res.status === 401 && !location.pathname.startsWith('/login')) {
    location.href = '/login';
  }
  if (!res.ok) {
    const err = new Error(data.message || 'Terjadi kesalahan');
    err.status = res.status;
    throw err;
  }
  return data;
}

function esc(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

const rp = n => 'Rp' + Number(n).toLocaleString('id-ID');
const kisaran = i => `${rp(i.harga_min)} – ${rp(i.harga_max)}`;

function toast(msg, isErr = false) {
  const el = document.createElement('div');
  el.className = 'toast' + (isErr ? ' err' : '');
  el.textContent = msg;
  document.body.appendChild(el);
  setTimeout(() => el.remove(), 3200);
}

function thumb(i) {
  return `<div class="thumb">${i.foto_url ? `<img src="${esc(i.foto_url)}" alt="${esc(i.nama_barang)}" loading="lazy">` : '📦'}</div>`;
}

function itemCard(i, extra = '') {
  return `<div class="item">
    <a href="/items/${i.id}">${thumb(i)}
    <div class="body">
      <h3>${esc(i.nama_barang)}</h3>
      <div class="price">${kisaran(i)}</div>
      <div style="margin-top:6px"><span class="badge">${esc(i.kondisi)}</span>
      ${i.status === 'ditukar' ? '<span class="badge ditukar">Sudah ditukar</span>' : ''}
      ${i.is_mine ? '<span class="badge">Milik Anda</span>' : `<span class="muted" style="font-size:.85rem"> @${esc(i.pemilik?.username)}</span>`}</div>
    </div></a>${extra}</div>`;
}

async function logout() {
  try { await api('/logout', { method: 'POST' }); } finally { location.href = '/login'; }
}

function debounce(fn, ms = 300) {
  let t;
  return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
}
