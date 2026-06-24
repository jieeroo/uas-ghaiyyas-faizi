// Layer komunikasi dengan server PHP melalui API fetch.
const API = {
  BASE: (() => {
    const path = window.location.pathname;
    return path.substring(0, path.lastIndexOf('/') + 1);
  })(),
  token: (typeof localStorage !== 'undefined' ? localStorage.getItem('yj_token') : null)
    || (typeof sessionStorage !== 'undefined' ? sessionStorage.getItem('yj_token') : null)
    || null,

  headers() {
    const h = { 'Content-Type': 'application/json' };
    if (this.token) {
      h['Authorization'] = 'Bearer ' + this.token;
      h['X-Auth-Token'] = this.token;
    }
    return h;
  },

  async req(method, endpoint, body = null) {
    const opts = { method, headers: this.headers() };
    if (body !== null) opts.body = JSON.stringify(body);

    const url = new URL(endpoint.replace(/^\/+/, ''), window.location.origin + this.BASE);

    try {
      const res = await fetch(url, opts);
      const text = await res.text();
      let json = {};

      if (text) {
        try { json = JSON.parse(text); }
        catch { json = { error: 'Server mengembalikan respons yang bukan JSON.' }; }
      }

      if (!res.ok) throw new Error(json.error || 'Terjadi kesalahan server.');
      return json;
    } catch (e) {
      showToast('❌ ' + e.message, 'err');
      throw e;
    }
  },

  get:    (ep)       => API.req('GET',    ep),
  post:   (ep, body) => API.req('POST',   ep, body),
  put:    (ep, body) => API.req('PUT',    ep, body),
  delete: (ep)       => API.req('DELETE', ep),
};


// Penyimpanan sementara data layanan dan pesanan yang sedang ditampilkan.
let _servicesCache = [];
let _ordersCache   = [];

// Helper untuk menampilkan nominal dalam format rupiah.
function rupiah(n) { return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }
function esc(str) {
  return String(str).replace(/[&<>"']/g,
    c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
// Menunda eksekusi fungsi agar pencarian tidak terlalu sering dipanggil.
function debounce(fn, ms) {
  let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}
// Menampilkan notifikasi singkat di bawah layar saat aksi berhasil atau gagal.
function showToast(msg, type = 'ok') {
  const el = document.getElementById('toast');
  if (!el) return;
  el.textContent = msg;
  el.className = 'toast show ' + type;
  setTimeout(() => {
    if (el) el.className = 'toast hidden';
  }, 3200);
}
// Mengubah tombol menjadi mode loading saat proses penyimpanan sedang berjalan.
function setLoading(btnId, loading, label = 'Simpan') {
  const btn = document.getElementById(btnId);
  if (!btn) return;
  btn.disabled = loading;
  btn.textContent = loading ? 'Menyimpan...' : label;
}
// Membuat gambar placeholder sederhana jika layanan belum punya foto.
function placeholderImg(text, color) {
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><rect width="100%" height="100%" fill="${color}"/><text x="50%" y="50%" font-family="Space Grotesk" font-size="34" fill="#0a0d16" text-anchor="middle" dominant-baseline="central" font-weight="700">${text}</text></svg>`;
  return 'data:image/svg+xml;base64,' + btoa(svg);
}

// Sistem tema: gelap, terang, dan otomatis sesuai preferensi perangkat.
const THEME_KEY = 'yj_theme_mode';
function getSystemTheme() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}
function getThemeIcon(mode) {
  const resolvedMode = mode === 'auto' ? getSystemTheme() : mode;
  if (resolvedMode === 'dark') {
    return `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>`;
  }
  return `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v2.5M12 18.5V21M4.5 12H3m18 0h-1.5M6.4 6.4l-1.06-1.06M18.66 18.66l-1.06-1.06M6.4 17.6l-1.06 1.06M18.66 5.34l-1.06 1.06M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/></svg>`;
}
function applyTheme(mode) {
  const resolvedMode = mode === 'auto' ? getSystemTheme() : mode;
  document.documentElement.setAttribute('data-theme', mode);
  document.documentElement.style.colorScheme = resolvedMode;
  const button = document.getElementById('themeToggle');
  if (button) {
    button.dataset.mode = mode;
    button.innerHTML = getThemeIcon(mode);
    button.setAttribute('aria-label', mode === 'dark' ? 'Aktifkan tema terang' : mode === 'light' ? 'Aktifkan tema gelap' : 'Ganti tema');
    button.title = mode === 'dark' ? 'Tema gelap' : mode === 'light' ? 'Tema terang' : 'Tema otomatis';
  }
}
function changeTheme(mode) {
  localStorage.setItem(THEME_KEY, mode);
  applyTheme(mode);
}
function toggleTheme() {
  const currentMode = localStorage.getItem(THEME_KEY) || 'auto';
  const nextMode = currentMode === 'auto' ? 'light' : currentMode === 'light' ? 'dark' : 'auto';
  changeTheme(nextMode);
}
function initTheme() {
  const savedMode = localStorage.getItem(THEME_KEY) || 'auto';
  applyTheme(savedMode);
  const button = document.getElementById('themeToggle');
  if (button) button.addEventListener('click', toggleTheme);
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (localStorage.getItem(THEME_KEY) === 'auto') applyTheme('auto');
  });
}

function syncAuthView(showApp) {
  const app = document.getElementById('app');
  const loginScreen = document.getElementById('loginScreen');
  if (!app || !loginScreen) return;
  document.documentElement.setAttribute('data-auth-view', showApp ? 'app' : 'login');
  if (showApp) {
    app.classList.remove('hidden');
    loginScreen.classList.add('hidden');
  } else {
    app.classList.add('hidden');
    loginScreen.classList.remove('hidden');
  }
}

function setCookie(name, value, days = 7) {
  const expires = new Date(Date.now() + days * 86400000).toUTCString();
  document.cookie = `${name}=${encodeURIComponent(value)}; path=/; expires=${expires}; SameSite=Lax`;
}
function deleteCookie(name) {
  document.cookie = `${name}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
}
function getStoredAuthState() {
  const token = localStorage.getItem('yj_token') || sessionStorage.getItem('yj_token') || document.cookie.match(/(?:^|; )yj_token=([^;]*)/)?.[1] || '';
  const user = localStorage.getItem('yj_user') || sessionStorage.getItem('yj_user') || '';
  const loggedInFlag = localStorage.getItem('yj_logged_in') === '1' || sessionStorage.getItem('yj_logged_in') === '1' || !!document.cookie.match(/(?:^|; )yj_logged_in=1/);
  return {
    token: decodeURIComponent(token || ''),
    user: user ? JSON.parse(user) : null,
    loggedInFlag,
  };
}
function persistAuthState(token, user) {
  if (token) {
    localStorage.setItem('yj_token', token);
    sessionStorage.setItem('yj_token', token);
    setCookie('yj_token', token);
  } else {
    localStorage.removeItem('yj_token');
    sessionStorage.removeItem('yj_token');
    deleteCookie('yj_token');
  }

  if (user) {
    localStorage.setItem('yj_user', JSON.stringify(user));
    sessionStorage.setItem('yj_user', JSON.stringify(user));
    localStorage.setItem('yj_logged_in', '1');
    sessionStorage.setItem('yj_logged_in', '1');
    setCookie('yj_logged_in', '1');
  } else {
    localStorage.removeItem('yj_user');
    sessionStorage.removeItem('yj_user');
    localStorage.removeItem('yj_logged_in');
    sessionStorage.removeItem('yj_logged_in');
    deleteCookie('yj_logged_in');
  }
}

// Mengganti tampilan form login/register saat user ingin masuk atau daftar.
function switchTab(tab) {
  document.getElementById('tabLoginBtn').classList.toggle('active', tab === 'login');
  document.getElementById('tabRegisterBtn').classList.toggle('active', tab === 'register');
  document.getElementById('loginForm').classList.toggle('hidden', tab !== 'login');
  document.getElementById('registerForm').classList.toggle('hidden', tab !== 'register');
  hideMsg();
}
function showMsg(text, type) {
  const el = document.getElementById('loginMsg');
  el.textContent = text; el.className = 'form-msg show ' + type;
}
function hideMsg() { document.getElementById('loginMsg').className = 'form-msg'; }

// Memproses login user dan membuka dashboard jika kredensial benar.
async function handleLogin(e) {
  e.preventDefault();
  const btn = document.getElementById('loginBtn');
  if (!btn) return;
  btn.disabled = true; btn.textContent = 'Masuk...';
  try {
    const username = document.getElementById('loginUsername')?.value?.trim() || '';
    const password = document.getElementById('loginPassword')?.value || '';
    const res = await API.post('/auth.php?action=login', { username, password });
    if (!res || !res.ok) throw new Error(res?.error || 'Login gagal.');
    API.token = res.token;
    persistAuthState(res.token || null, res.user || {});
    syncAuthView(true);
    enterApp(res.user);
  } catch (err) {
    showMsg(err.message || 'Username atau password salah.', 'err');
  } finally {
    btn.disabled = false; btn.textContent = 'Masuk ke Dashboard';
  }
}

// Membuat akun baru melalui endpoint register di server PHP.
async function handleRegister(e) {
  e.preventDefault();
  const btn = document.getElementById('registerBtn');
  btn.disabled = true; btn.textContent = 'Mendaftar...';
  try {
    await API.post('/auth.php?action=register', {
      name:     document.getElementById('regName').value.trim(),
      username: document.getElementById('regUsername').value.trim(),
      password: document.getElementById('regPassword').value,
    });
    showMsg('Akun berhasil dibuat! Silakan masuk.', 'ok');
    switchTab('login');
    document.getElementById('loginUsername').value = document.getElementById('regUsername').value;
    document.getElementById('registerForm').reset();
  } catch (err) {
    showMsg(err.message || 'Gagal mendaftar.', 'err');
  } finally {
    btn.disabled = false; btn.textContent = 'Daftar Akun';
  }
}

// Menghapus sesi pengguna dan mengembalikan tampilan ke layar login.
async function logout() {
  try { await API.post('/auth.php?action=logout', {}); } catch (_) {}
  API.token = null;
  persistAuthState(null, null);
  const app = document.getElementById('app');
  const loginScreen = document.getElementById('loginScreen');
  const loginForm = document.getElementById('loginForm');
  syncAuthView(false);
  if (loginForm) loginForm.reset();
}

// Menampilkan dashboard utama setelah login berhasil.
function enterApp(user) {
  if (!user) return;
  syncAuthView(true);
  const userNameLabel = document.getElementById('userNameLabel');
  const userAvatar = document.getElementById('userAvatar');
  if (userNameLabel) userNameLabel.textContent = user.name || user.username || 'User';
  if (userAvatar) userAvatar.textContent = (user.name || user.username || 'U').charAt(0).toUpperCase();
  renderServices();
  renderOrders();
  fillOrderServiceOptions();
  initSignaturePad();
}


(async function checkSession() {
  try {
    const savedState = getStoredAuthState();
    const savedToken = savedState.token || '';
    const savedUser = savedState.user;
    const loggedInFlag = savedState.loggedInFlag;
    const shouldShowApp = Boolean(savedToken || loggedInFlag);

    if (!shouldShowApp) {
      syncAuthView(false);
      return;
    }

    API.token = savedToken || '';
    syncAuthView(true);
    enterApp(savedUser || { name: 'User', username: 'User' });

    try {
      const res = await API.get('/auth.php?action=me');
      if (res && res.ok && res.user) {
        persistAuthState(savedToken || null, res.user);
        enterApp(res.user);
      }
    } catch (_) {
      persistAuthState(savedToken || null, savedUser || null);
    }
  } catch (_) {
    persistAuthState(null, null);
    syncAuthView(false);
  }
})();

initTheme();

// Menghitung ringkasan statistik yang tampil di hero section dashboard.
function renderStats() {
  const aktif   = _servicesCache.filter(s => s.status === 'Aktif').length;
  const total   = _ordersCache.length;
  const selesai = _ordersCache.filter(o => o.status === 'Selesai').length;
  const avg     = _servicesCache.length
    ? (_servicesCache.reduce((a, s) => a + Number(s.rating || 0), 0) / _servicesCache.length)
    : 0;
  document.getElementById('statLayanan').textContent = aktif;
  document.getElementById('statPesanan').textContent = total;
  document.getElementById('statSelesai').textContent = selesai;
  document.getElementById('statRating').textContent  = avg.toFixed(1);
}


let tempImages = [];

// Mengambil dan menampilkan data layanan dari server ke tabel layanan.
async function renderServices() {
  const q = document.getElementById('searchServices').value || '';
  const body = document.getElementById('servicesBody');
  body.innerHTML = `<tr class="empty-row"><td colspan="7">Memuat data...</td></tr>`;
  try {
    const res = await API.get(`/services.php?q=${encodeURIComponent(q)}`);
    _servicesCache = res.data;
    renderStats();

    if (!_servicesCache.length) {
      body.innerHTML = `<tr class="empty-row"><td colspan="7">Tidak ada layanan ditemukan.</td></tr>`;
      return;
    }
    body.innerHTML = _servicesCache.map(s => {
      const img = (s.images && s.images[0]) ? s.images[0] : placeholderImg('?','#2a3350');
      const statusBadge = s.status === 'Aktif' ? 'badge-success' : 'badge-muted';
      return `<tr>
        <td><div class="thumb-row"><img src="${img}" alt=""><div>
          <div style="font-weight:600;">${esc(s.nama)}</div>
          <div style="font-size:.76rem;color:var(--muted);">${esc(s.kategori)}</div>
        </div></div></td>
        <td>${esc(s.game)}</td>
        <td>${esc(s.kategori)}</td>
        <td>${rupiah(s.harga)}</td>
        <td><span class="badge badge-info">★ ${Number(s.rating).toFixed(1)}</span></td>
        <td><span class="badge ${statusBadge}">${esc(s.status)}</span></td>
        <td><div class="row-actions">
          <button class="btn btn-icon btn-ghost btn-sm" title="Lihat"  onclick="viewService(${s.id})">👁</button>
          <button class="btn btn-icon btn-ghost btn-sm" title="Edit"   onclick="openServiceModal(${s.id})">✎</button>
          <button class="btn btn-icon btn-danger btn-sm" title="Hapus" onclick="confirmDelete('service',${s.id})">🗑</button>
        </div></td>
      </tr>`;
    }).join('');
  } catch (_) {
    body.innerHTML = `<tr class="empty-row"><td colspan="7">Gagal memuat data. Periksa koneksi ke server.</td></tr>`;
  }
}

// Membuka modal untuk menambah atau mengedit data layanan.
async function openServiceModal(id) {
  tempImages = [];
  document.getElementById('serviceForm').reset();
  document.getElementById('svcPreview').innerHTML = '';

  if (id) {
    document.getElementById('serviceModalTitle').textContent = 'Edit Layanan';
    document.getElementById('svcId').value = id;
    try {
      const res = await API.get(`/services.php?id=${id}`);
      const s = res.data;
      document.getElementById('svcNama').value     = s.nama;
      document.getElementById('svcGame').value     = s.game;
      document.getElementById('svcKategori').value = s.kategori;
      document.getElementById('svcHarga').value    = s.harga;
      document.getElementById('svcRating').value   = s.rating;
      document.getElementById('svcStatus').value   = s.status;
      document.getElementById('svcDeskripsi').value = s.deskripsi || '';
      tempImages = [...(s.images || [])];
      renderPreview();
    } catch (_) { return; }
  } else {
    document.getElementById('serviceModalTitle').textContent = 'Tambah Layanan';
    document.getElementById('svcId').value = '';
  }
  openModal('serviceModalOverlay');
}

// Menangani upload beberapa gambar layanan sekaligus.
function handleMultiUpload(e) {
  const files = Array.from(e.target.files || []);
  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = ev => { tempImages.push(ev.target.result); renderPreview(); };
    reader.readAsDataURL(file);
  });
  e.target.value = '';
}
// Menampilkan pratinjau gambar yang sudah dipilih sebelum disimpan.
function renderPreview() {
  document.getElementById('svcPreview').innerHTML = tempImages.map((src, i) => `
    <div class="preview-item"><img src="${src}"><button type="button" onclick="removeTempImage(${i})">✕</button></div>
  `).join('');
}
// Menghapus gambar yang sudah dipilih dari daftar preview sebelum disimpan.
function removeTempImage(i) { tempImages.splice(i, 1); renderPreview(); }

// Menyimpan data layanan baru atau hasil edit ke database melalui PHP.
async function saveService(e) {
  e.preventDefault();
  setLoading('svcSaveBtn', true, 'Simpan Layanan');
  const id = document.getElementById('svcId').value;
  const payload = {
    nama:      document.getElementById('svcNama').value.trim(),
    game:      document.getElementById('svcGame').value,
    kategori:  document.getElementById('svcKategori').value,
    harga:     Number(document.getElementById('svcHarga').value),
    rating:    Number(document.getElementById('svcRating').value),
    status:    document.getElementById('svcStatus').value,
    deskripsi: document.getElementById('svcDeskripsi').value.trim(),
    images:    tempImages.length ? tempImages : [placeholderImg('?','#2a3350')],
  };
  try {
    if (id) {
      await API.put(`/services.php?id=${id}`, payload);
      showToast('✅ Layanan berhasil diperbarui.');
    } else {
      await API.post('/services.php', payload);
      showToast('✅ Layanan berhasil ditambahkan.');
    }
    closeModal('serviceModalOverlay');
    renderServices();
    fillOrderServiceOptions();
  } catch (err) {
    showToast('❌ ' + (err.message || 'Terjadi kesalahan saat menyimpan layanan.'));
  } finally {
    setLoading('svcSaveBtn', false, 'Simpan Layanan');
  }
}

// Menampilkan detail lengkap layanan dalam modal tampilan detail.
async function viewService(id) {
  try {
    const res = await API.get(`/services.php?id=${id}`);
    const s = res.data;
    const gallery = (s.images||[]).map(src =>
      `<div class="preview-item" style="width:90px;height:90px;"><img src="${src}"></div>`
    ).join('');
    document.getElementById('viewModalBody').innerHTML = `
      <h3 style="margin-top:0;">${esc(s.nama)}</h3>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
        <span class="badge badge-info">${esc(s.game)}</span>
        <span class="badge badge-muted">${esc(s.kategori)}</span>
        <span class="badge ${s.status==='Aktif'?'badge-success':'badge-warning'}">${esc(s.status)}</span>
      </div>
      <p style="color:var(--muted);">${esc(s.deskripsi||'Tidak ada deskripsi.')}</p>
      <p><b>Harga:</b> ${rupiah(s.harga)} &nbsp;•&nbsp; <b>Rating:</b> ★ ${Number(s.rating).toFixed(1)}</p>
      <label>Galeri Gambar</label>
      <div class="preview-grid">${gallery || '<span style="color:var(--muted);font-size:.85rem;">Tidak ada gambar.</span>'}</div>
    `;
    openModal('viewModalOverlay');
  } catch (_) {}
}


// Mengisi pilihan layanan pada form tambah/edit pesanan.
async function fillOrderServiceOptions() {
  try {
    const res = await API.get('/services.php');
    const sel = document.getElementById('ordLayanan');
    sel.innerHTML = res.data.map(s =>
      `<option value="${s.id}">${esc(s.nama)} — ${esc(s.game)}</option>`
    ).join('');
  } catch (_) {}
}

// Mengambil dan menampilkan data pesanan ke tabel pesanan.
async function renderOrders() {
  const q = document.getElementById('searchOrders').value || '';
  const body = document.getElementById('ordersBody');
  body.innerHTML = `<tr class="empty-row"><td colspan="6">Memuat data...</td></tr>`;
  try {
    const res = await API.get(`/orders.php?q=${encodeURIComponent(q)}`);
    _ordersCache = res.data;
    renderStats();

    if (!_ordersCache.length) {
      body.innerHTML = `<tr class="empty-row"><td colspan="6">Belum ada pesanan.</td></tr>`;
      return;
    }
    const statusBadge = st => ({
      'Selesai':'badge-success','Diproses':'badge-info','Menunggu':'badge-warning','Dibatalkan':'badge-muted'
    }[st] || 'badge-muted');

    body.innerHTML = _ordersCache.map(o => {
      const ttdCell = o.has_ttd
        ? `<button class="btn btn-ghost btn-sm" onclick="viewSignature(${o.id})">🖋 Lihat</button>`
        : `<span class="badge badge-muted">Belum TTD</span>`;
      return `<tr>
        <td style="font-weight:600;">${esc(o.nama)}</td>
        <td>${o.layanan_nama ? esc(o.layanan_nama) : '<span style="color:var(--muted);">Layanan dihapus</span>'}</td>
        <td>${esc(o.tanggal)}</td>
        <td><span class="badge ${statusBadge(o.status)}">${esc(o.status)}</span></td>
        <td>${ttdCell}</td>
        <td><div class="row-actions">
          <button class="btn btn-icon btn-ghost btn-sm" title="Edit"  onclick="openOrderModal(${o.id})">✎</button>
          <button class="btn btn-icon btn-danger btn-sm" title="Hapus" onclick="confirmDelete('order',${o.id})">🗑</button>
        </div></td>
      </tr>`;
    }).join('');
  } catch (_) {
    body.innerHTML = `<tr class="empty-row"><td colspan="6">Gagal memuat data.</td></tr>`;
  }
}

// Membuka modal untuk menambah atau mengedit pesanan beserta tanda tangan digital.
async function openOrderModal(id) {
  await fillOrderServiceOptions();
  document.getElementById('orderForm').reset();
  document.getElementById('signPreviewWrap').classList.add('hidden');
  clearSignature();

  if (id) {
    document.getElementById('orderModalTitle').textContent = 'Edit Pesanan';
    document.getElementById('ordId').value = id;
    try {
      const res = await API.get(`/orders.php?id=${id}`);
      const o = res.data;
      document.getElementById('ordNama').value    = o.nama;
      document.getElementById('ordLayanan').value = o.service_id;
      document.getElementById('ordTanggal').value = o.tanggal;
      document.getElementById('ordStatus').value  = o.status;
      if (o.ttd) {
        document.getElementById('signPreviewWrap').classList.remove('hidden');
        document.getElementById('signPreviewImg').src = o.ttd;
      }
    } catch (_) { return; }
  } else {
    document.getElementById('orderModalTitle').textContent = 'Tambah Pesanan';
    document.getElementById('ordId').value = '';
    document.getElementById('ordTanggal').value = new Date().toISOString().slice(0, 10);
  }
  openModal('orderModalOverlay');
  setTimeout(() => resizeCanvas(), 60);
}

// Menyimpan data pesanan baru atau hasil edit ke server.
async function saveOrder(e) {
  e.preventDefault();
  setLoading('ordSaveBtn', true, 'Simpan Pesanan');
  const id = document.getElementById('ordId').value;
  const newTtd = getSignatureDataIfDrawn();
  const payload = {
    nama:       document.getElementById('ordNama').value.trim(),
    service_id: document.getElementById('ordLayanan').value || null,
    tanggal:    document.getElementById('ordTanggal').value,
    status:     document.getElementById('ordStatus').value,
    ttd:        newTtd || null,
  };
  try {
    if (id) {
      await API.put(`/orders.php?id=${id}`, payload);
      showToast('✅ Pesanan berhasil diperbarui.');
    } else {
      await API.post('/orders.php', payload);
      showToast('✅ Pesanan berhasil ditambahkan.');
    }
    closeModal('orderModalOverlay');
    renderOrders();
  } catch (_) {
  } finally {
    setLoading('ordSaveBtn', false, 'Simpan Pesanan');
  }
}

// Menampilkan tanda tangan digital klien dalam modal preview.
async function viewSignature(id) {
  try {
    const res = await API.get(`/orders.php?id=${id}`);
    if (!res.data.ttd) return;
    document.getElementById('signViewImg').src = res.data.ttd;
    openModal('signViewOverlay');
  } catch (_) {}
}


// Area tanda tangan digital untuk persetujuan pesanan.
let canvas, ctx, drawing = false, hasDrawn = false;
function initSignaturePad() {
  canvas = document.getElementById('signCanvas');
  ctx = canvas.getContext('2d');
  resizeCanvas();
  ['mousedown','touchstart'].forEach(ev => canvas.addEventListener(ev, startDraw, {passive:false}));
  ['mousemove','touchmove'].forEach(ev => canvas.addEventListener(ev, drawMove, {passive:false}));
  ['mouseup','mouseleave','touchend'].forEach(ev => canvas.addEventListener(ev, endDraw));
  window.addEventListener('resize', resizeCanvas);
}
// Menyesuaikan ukuran kanvas tanda tangan sesuai layar perangkat.
function resizeCanvas() {
  if (!canvas) return;
  const ratio = window.devicePixelRatio || 1;
  const rect = canvas.getBoundingClientRect();
  if (rect.width === 0) return;
  canvas.width  = rect.width  * ratio;
  canvas.height = rect.height * ratio;
  ctx.scale(ratio, ratio);
  ctx.lineWidth = 2.4; ctx.lineCap = 'round'; ctx.strokeStyle = '#1c2438';
  canvas.dataset.empty = '1';
}
// Mengambil posisi pointer atau sentuhan pada kanvas tanda tangan.
function getPos(e) {
  const rect = canvas.getBoundingClientRect();
  return {
    x: (e.touches ? e.touches[0].clientX : e.clientX) - rect.left,
    y: (e.touches ? e.touches[0].clientY : e.clientY) - rect.top,
  };
}
// Memulai proses menggambar tanda tangan saat mouse atau jari disentuh.
function startDraw(e) { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
// Menangani pergerakan saat user menggambar tanda tangan.
function drawMove(e) {
  if (!drawing) return; e.preventDefault();
  const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke();
  hasDrawn = true; canvas.dataset.empty = '';
  document.querySelector('.sign-hint').style.display = 'none';
}
// Mengakhiri proses menggambar ketika sentuhan atau klik selesai.
function endDraw() { drawing = false; }
// Menghapus tanda tangan yang ada di kanvas.
function clearSignature() {
  if (!ctx) return;
  const rect = canvas.getBoundingClientRect();
  ctx.clearRect(0, 0, rect.width, rect.height);
  hasDrawn = false; canvas.dataset.empty = '1';
  const hint = document.querySelector('.sign-hint');
  if (hint) hint.style.display = 'block';
}
// Mengubah tanda tangan yang sudah digambar menjadi data gambar base64.
function getSignatureDataIfDrawn() {
  if (!hasDrawn) return null;
  const data = canvas.toDataURL('image/png');
  hasDrawn = false;
  return data;
}


// Membuka modal tertentu di halaman.
function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
// Menutup modal tertentu di halaman.
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

// Menyimpan target data yang akan dihapus saat konfirmasi muncul.
let deleteTarget = null;
function confirmDelete(type, id) {
  deleteTarget = { type, id };
  document.getElementById('confirmMsg').textContent = type === 'service'
    ? 'Yakin ingin menghapus layanan ini? Pesanan terkait akan terlepas dari layanan.'
    : 'Yakin ingin menghapus pesanan ini beserta TTD-nya?';
  openModal('confirmOverlay');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
  if (!deleteTarget) return;
  const btn = document.getElementById('confirmDeleteBtn');
  btn.disabled = true; btn.textContent = 'Menghapus...';
  try {
    if (deleteTarget.type === 'service') {
      await API.delete(`/services.php?id=${deleteTarget.id}`);
      showToast('🗑 Layanan berhasil dihapus.');
      renderServices(); fillOrderServiceOptions();
    } else {
      await API.delete(`/orders.php?id=${deleteTarget.id}`);
      showToast('🗑 Pesanan berhasil dihapus.');
      renderOrders();
    }
    renderStats();
    closeModal('confirmOverlay');
  } catch (_) {
  } finally {
    btn.disabled = false; btn.textContent = 'Hapus';
    deleteTarget = null;
  }
});


document.querySelectorAll('.overlay').forEach(ov => {
  ov.addEventListener('click', e => { if (e.target === ov) ov.classList.add('hidden'); });
});


// Mengekspor data layanan atau pesanan ke CSV, Excel, atau format cetak.
function exportTable(which, type) {
  let rows, filename;
  if (which === 'services') {
    rows = _servicesCache.map(s => ({
      Nama: s.nama, Game: s.game, Kategori: s.kategori, Harga: s.harga, Rating: s.rating, Status: s.status
    }));
    filename = 'layanan_yassjokiin';
  } else {
    rows = _ordersCache.map(o => ({
      Klien: o.nama, Layanan: o.layanan_nama || '-', Tanggal: o.tanggal,
      Status: o.status, TTD: o.has_ttd ? 'Sudah' : 'Belum'
    }));
    filename = 'pesanan_yassjokiin';
  }

  if (!rows.length) { alert('Tidak ada data untuk diekspor.'); return; }

  if (type === 'csv') {
    const headers = Object.keys(rows[0]);
    const csv = [headers.join(',')].concat(
      rows.map(r => headers.map(h => `"${String(r[h]).replace(/"/g,'""')}"`).join(','))
    ).join('\n');
    downloadBlob(csv, filename + '.csv', 'text/csv;charset=utf-8;');
  } else if (type === 'xlsx') {
    const ws = XLSX.utils.json_to_sheet(rows);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Data');
    XLSX.writeFile(wb, filename + '.xlsx');
  } else if (type === 'print') {
    const headers = Object.keys(rows[0]);
    let html = `<html><head><title>${filename}</title>
      <style>body{font-family:sans-serif;padding:24px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #999;padding:8px;font-size:13px;text-align:left;}th{background:#eee;}</style>
      </head><body><h2>Yassjokiin — ${which === 'services' ? 'Daftar Layanan' : 'Daftar Pesanan'}</h2><table><thead><tr>`;
    html += headers.map(h => `<th>${h}</th>`).join('') + '</tr></thead><tbody>';
    rows.forEach(r => { html += '<tr>' + headers.map(h => `<td>${r[h]}</td>`).join('') + '</tr>'; });
    html += '</tbody></table></body></html>';
    const w = window.open('', '_blank');
    w.document.write(html); w.document.close(); w.print();
  }
}

// Membuat file unduhan dari isi teks atau data blob.
function downloadBlob(content, filename, mime) {
  const blob = new Blob([content], { type: mime });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href = url; a.download = filename; a.click();
  URL.revokeObjectURL(url);
}

(function () {
  try {
    function getCookie(name) {
      const m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
      return m ? decodeURIComponent(m[1]) : '';
    }
    const auth = window.__INITIAL_AUTH__ || {};
    const token = localStorage.getItem('yj_token') || sessionStorage.getItem('yj_token') || getCookie('yj_token');
    const loggedInFlag = auth.loggedIn || localStorage.getItem('yj_logged_in') === '1' || sessionStorage.getItem('yj_logged_in') === '1' || !!getCookie('yj_logged_in');
    const app = document.getElementById('app');
    const loginScreen = document.getElementById('loginScreen');
    document.documentElement.setAttribute('data-auth-view', (auth.loggedIn || token || loggedInFlag) ? 'app' : 'login');
    if ((auth.loggedIn || token || loggedInFlag) && app && loginScreen) {
      app.classList.remove('hidden');
      loginScreen.classList.add('hidden');
    }
  } catch (_) {}
})();