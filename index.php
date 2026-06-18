<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Yassjokiin — Joki Game Terpercaya</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="stylesheet" href="style.css">
</head>
<body>


<div id="loginScreen" class="login-screen">
  <div class="login-box">
    <div class="brand"><span class="mark">YJ</span><span class="yass">Yass</span><span class="jokiin">jokiin</span></div>
    <p class="login-sub">Platform jasa joki game — masuk untuk kelola pesanan &amp; layanan</p>

    <div class="tabs">
      <button class="tab-btn active" id="tabLoginBtn" onclick="switchTab('login')">Masuk</button>
      <button class="tab-btn" id="tabRegisterBtn" onclick="switchTab('register')">Daftar</button>
    </div>

    <div id="loginMsg" class="form-msg"></div>

    <form id="loginForm" onsubmit="handleLogin(event)">
      <div class="field">
        <label>Username</label>
        <input type="text" id="loginUsername" placeholder="masukkan username" required>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" id="loginPassword" placeholder="••••••••" required>
      </div>
      <button class="btn btn-primary" type="submit" id="loginBtn" style="width:100%;justify-content:center;padding:13px;">Masuk ke Dashboard</button>
    </form>

    <form id="registerForm" class="hidden" onsubmit="handleRegister(event)">
      <div class="field">
        <label>Nama Lengkap</label>
        <input type="text" id="regName" placeholder="nama kamu" required>
      </div>
      <div class="field">
        <label>Username</label>
        <input type="text" id="regUsername" placeholder="buat username" required>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" id="regPassword" placeholder="buat password (min. 6 karakter)" required>
      </div>
      <button class="btn btn-accent" type="submit" id="registerBtn" style="width:100%;justify-content:center;padding:13px;">Daftar Akun</button>
    </form>

    <div class="hint-box">Akun demo: <b>admin</b> / <b>admin123</b> — data disimpan di database MySQL melalui PHP API.</div>
  </div>
</div>


<div id="app" class="hidden">

 
  <div class="topbar">
    <div class="container nav">
      <div class="brand"><span class="mark">YJ</span><span class="yass">Yass</span><span class="jokiin">jokiin</span></div>
      <div class="nav-links">
        <a href="#beranda">Beranda</a>
        <a href="#layanan">Layanan</a>
        <a href="#pesanan">Pesanan</a>
        <a href="#testimoni">Testimoni</a>
      </div>
      <div class="nav-right">
        <div class="user-chip">
          <div class="avatar" id="userAvatar">A</div>
          <span id="userNameLabel">Admin</span>
        </div>
        <button class="btn btn-ghost btn-sm" onclick="logout()">Keluar</button>
      </div>
    </div>
  </div>

  
  <section class="hero" id="beranda">
    <div class="container hero-grid">
      <div>
        <div class="eyebrow">Joki Rank · All Games</div>
        <h1>Push rank kamu sambil kamu <span>rebahan santai.</span></h1>
        <p class="lead">Yassjokiin menyediakan jasa joki Mobile Legends, Valorant, Genshin Impact, PUBG, dan Free Fire dengan joki ber-rank tinggi, aman, dan bergaransi.</p>
        <div class="hero-actions">
          <a href="#layanan" class="btn btn-primary">Lihat Layanan</a>
          <button class="btn btn-ghost" onclick="openVideoModal()">▶ Cara Order</button>
        </div>

        <div class="stats">
          <div class="stat"><div class="stat-num" id="statLayanan">—</div><div class="stat-label">Layanan Aktif</div></div>
          <div class="stat"><div class="stat-num" id="statPesanan">—</div><div class="stat-label">Total Pesanan</div></div>
          <div class="stat"><div class="stat-num" id="statSelesai">—</div><div class="stat-label">Pesanan Selesai</div></div>
          <div class="stat"><div class="stat-num" id="statRating">—</div><div class="stat-label">Rating Rata²</div></div>
        </div>
      </div>

      <div class="tier-card">
        <div class="tier-label">Contoh Progress Rank Klien</div>
        <div class="tier-track">
          <div class="tier done"><div class="dot">🥉</div>Bronze</div>
          <div class="tier done"><div class="dot">🥈</div>Silver</div>
          <div class="tier active"><div class="dot">🥇</div>Gold</div>
          <div class="tier"><div class="dot">💎</div>Platinum</div>
          <div class="tier"><div class="dot">👑</div>Mythic</div>
        </div>
        <div class="bar"><i style="width:46%"></i></div>
        <div class="progress-text">Progress menuju <b>Platinum</b>: 46% — estimasi <b>2 hari</b> pengerjaan oleh joki tim Yassjokiin.</div>
      </div>
    </div>
  </section>

  
  <section class="section" id="layanan">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Kelola Data</div>
          <h2>Daftar Layanan Joki</h2>
          <p>Tambah, ubah, hapus, dan unggah bukti/gambar layanan joki — lengkap dengan pencarian &amp; ekspor data.</p>
        </div>
        <button class="btn btn-primary" onclick="openServiceModal()">+ Tambah Layanan</button>
      </div>

      <div class="card">
        <div class="toolbar">
          <div class="search-wrap">
            <input type="text" id="searchServices" placeholder="Cari layanan, game, atau kategori..." oninput="debounce(renderServices, 300)()">
          </div>
          <div class="right-tools">
            <button class="btn btn-ghost btn-sm" onclick="exportTable('services','csv')">⇩ CSV</button>
            <button class="btn btn-ghost btn-sm" onclick="exportTable('services','xlsx')">⇩ Excel</button>
            <button class="btn btn-ghost btn-sm" onclick="exportTable('services','print')">🖨 Print</button>
          </div>
        </div>
        <div class="table-wrap">
          <table id="tblServices">
            <thead>
              <tr>
                <th>Layanan</th><th>Game</th><th>Kategori</th><th>Harga</th><th>Rating</th><th>Status</th><th>Aksi</th>
              </tr>
            </thead>
            <tbody id="servicesBody"><tr class="empty-row"><td colspan="7">Memuat data...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

 
  <section class="section" id="pesanan" style="background:var(--bg-2);">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Transaksi</div>
          <h2>Daftar Pesanan &amp; TTD Digital</h2>
          <p>Catat pesanan masuk dan konfirmasi persetujuan klien lewat tanda tangan digital di canvas.</p>
        </div>
        <button class="btn btn-accent" onclick="openOrderModal()">+ Tambah Pesanan</button>
      </div>

      <div class="card">
        <div class="toolbar">
          <div class="search-wrap">
            <input type="text" id="searchOrders" placeholder="Cari nama klien, layanan, atau status..." oninput="debounce(renderOrders, 300)()">
          </div>
          <div class="right-tools">
            <button class="btn btn-ghost btn-sm" onclick="exportTable('orders','csv')">⇩ CSV</button>
            <button class="btn btn-ghost btn-sm" onclick="exportTable('orders','xlsx')">⇩ Excel</button>
            <button class="btn btn-ghost btn-sm" onclick="exportTable('orders','print')">🖨 Print</button>
          </div>
        </div>
        <div class="table-wrap">
          <table id="tblOrders">
            <thead>
              <tr>
                <th>Klien</th><th>Layanan</th><th>Tanggal</th><th>Status</th><th>TTD</th><th>Aksi</th>
              </tr>
            </thead>
            <tbody id="ordersBody"><tr class="empty-row"><td colspan="6">Memuat data...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

 
  <section class="section" id="testimoni">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Bukti Sosial</div>
          <h2>Cuplikan &amp; Testimoni</h2>
          <p>Video proses joki dan testimoni audio dari klien Yassjokiin.</p>
        </div>
      </div>
      <div class="media-grid">
        <div class="media-frame">
          <video controls poster="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.poster.jpg">
            <source src="img/video gameplay.mp4" type="video/mp4">
            Browser kamu tidak mendukung video.
          </video>
        </div>
        <div class="testi-list">
          <div class="testi-card">
            <div class="av">YV</div>
            <div class="meta">
              <b>Yvonne — Blood Strike</b>
              <span>"Joki cepet winstreak lagi, dari Master 3 ke Mythic 4 hari selesai!"</span>
              <audio controls src="audio/testimoni.mp3"></audio>
            </div>
          </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <div class="container foot-row">
      <div class="brand" style="font-size:1rem;"><span class="mark" style="width:30px;height:30px;font-size:.8rem;">YJ</span><span class="yass">Yass</span><span class="jokiin">jokiin</span></div>
      <div>© 2026 Yassjokiin — Jasa Joki Game Terpercaya.</div>
    </div>
  </footer>

  <button class="bgm-btn" id="bgmBtn" onclick="toggleBgm()" title="Putar/berhenti musik latar">🔇</button>
  <audio id="bgmAudio" loop src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3"></audio>
</div>


<div class="overlay hidden" id="serviceModalOverlay">
  <div class="modal">
    <div class="modal-head">
      <h3 id="serviceModalTitle">Tambah Layanan</h3>
      <button class="close-x" onclick="closeModal('serviceModalOverlay')">✕</button>
    </div>
    <form id="serviceForm" onsubmit="saveService(event)">
      <div class="modal-body">
        <input type="hidden" id="svcId">
        <div class="field-row">
          <div class="field">
            <label>Nama Layanan</label>
            <input type="text" id="svcNama" placeholder="contoh: Joki Rank Mythic" required>
          </div>
          <div class="field">
            <label>Game</label>
            <select id="svcGame" required>
              <option value="">Pilih game</option>
              <option>Mobile Legends</option>
              <option>Valorant</option>
              <option>Genshin Impact</option>
              <option>PUBG Mobile</option>
              <option>Free Fire</option>
              <option>Honkai Star Rail</option>
            </select>
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label>Kategori</label>
            <select id="svcKategori" required>
              <option value="">Pilih kategori</option>
              <option>Rank Boost</option>
              <option>Farming Item</option>
              <option>Top Up Akun</option>
              <option>Win Streak</option>
            </select>
          </div>
          <div class="field">
            <label>Harga (Rp)</label>
            <input type="number" id="svcHarga" placeholder="contoh: 50000" min="0" required>
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label>Rating (0 - 5)</label>
            <input type="number" id="svcRating" placeholder="4.8" min="0" max="5" step="0.1" required>
          </div>
          <div class="field">
            <label>Status</label>
            <select id="svcStatus" required>
              <option>Aktif</option>
              <option>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="field">
          <label>Deskripsi</label>
          <textarea id="svcDeskripsi" rows="3" placeholder="Jelaskan detail layanan..."></textarea>
        </div>
        <div class="field">
          <label>Upload Gambar (bisa lebih dari satu)</label>
          <div class="upload-zone" onclick="document.getElementById('svcImages').click()">
            <strong>Klik untuk pilih file</strong> — bisa pilih beberapa gambar sekaligus (PNG/JPG)
          </div>
          <input type="file" id="svcImages" accept="image/*" multiple class="hidden" onchange="handleMultiUpload(event)">
          <div class="preview-grid" id="svcPreview"></div>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn btn-ghost" onclick="closeModal('serviceModalOverlay')">Batal</button>
        <button type="submit" class="btn btn-primary" id="svcSaveBtn">Simpan Layanan</button>
      </div>
    </form>
  </div>
</div>


<div class="overlay hidden" id="viewModalOverlay">
  <div class="modal">
    <div class="modal-head">
      <h3>Detail Layanan</h3>
      <button class="close-x" onclick="closeModal('viewModalOverlay')">✕</button>
    </div>
    <div class="modal-body" id="viewModalBody"></div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeModal('viewModalOverlay')">Tutup</button>
    </div>
  </div>
</div>


<div class="overlay hidden" id="orderModalOverlay">
  <div class="modal">
    <div class="modal-head">
      <h3 id="orderModalTitle">Tambah Pesanan</h3>
      <button class="close-x" onclick="closeModal('orderModalOverlay')">✕</button>
    </div>
    <form id="orderForm" onsubmit="saveOrder(event)">
      <div class="modal-body">
        <input type="hidden" id="ordId">
        <div class="field-row">
          <div class="field">
            <label>Nama Klien</label>
            <input type="text" id="ordNama" placeholder="nama pelanggan" required>
          </div>
          <div class="field">
            <label>Layanan</label>
            <select id="ordLayanan" required></select>
          </div>
        </div>
        <div class="field-row">
          <div class="field">
            <label>Tanggal Pesan</label>
            <input type="date" id="ordTanggal" required>
          </div>
          <div class="field">
            <label>Status</label>
            <select id="ordStatus" required>
              <option>Menunggu</option>
              <option>Diproses</option>
              <option>Selesai</option>
              <option>Dibatalkan</option>
            </select>
          </div>
        </div>
        <div class="field">
          <label>Tanda Tangan Digital (Persetujuan Klien)</label>
          <div class="sign-wrap">
            <canvas id="signCanvas"></canvas>
            <span class="sign-hint">Gambar tanda tangan dengan mouse / jari di sini</span>
          </div>
          <div class="sign-actions">
            <button type="button" class="btn btn-ghost btn-sm" onclick="clearSignature()">Hapus TTD</button>
            <span style="font-size:.78rem;color:var(--muted);align-self:center;">TTD akan otomatis tersimpan saat klik Simpan</span>
          </div>
          <div class="sign-preview hidden" id="signPreviewWrap" style="margin-top:10px;">
            <img id="signPreviewImg" alt="preview ttd">
            <span style="font-size:.8rem;color:var(--muted);">TTD tersimpan sebelumnya</span>
          </div>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn btn-ghost" onclick="closeModal('orderModalOverlay')">Batal</button>
        <button type="submit" class="btn btn-accent" id="ordSaveBtn">Simpan Pesanan</button>
      </div>
    </form>
  </div>
</div>


<div class="overlay hidden" id="signViewOverlay">
  <div class="modal" style="max-width:420px;">
    <div class="modal-head">
      <h3>Tanda Tangan Klien</h3>
      <button class="close-x" onclick="closeModal('signViewOverlay')">✕</button>
    </div>
    <div class="modal-body" style="text-align:center;">
      <img id="signViewImg" style="background:#fff;border-radius:10px;max-height:240px;margin:0 auto;">
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeModal('signViewOverlay')">Tutup</button>
    </div>
  </div>
</div>


<div class="overlay hidden" id="videoModalOverlay">
  <div class="modal" style="max-width:720px;">
    <div class="modal-head">
      <h3>Cara Order di Yassjokiin</h3>
      <button class="close-x" onclick="closeModal('videoModalOverlay')">✕</button>
    </div>
    <div class="modal-body">
      <div class="media-frame">
        <video controls autoplay>
          <source src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4" type="video/mp4">
        </video>
      </div>
      <ol style="color:var(--muted);font-size:.9rem;margin-top:14px;">
        <li>Pilih layanan joki pada bagian "Layanan".</li>
        <li>Hubungi admin &amp; lakukan pembayaran.</li>
        <li>Tanda tangani persetujuan secara digital.</li>
        <li>Pantau status pesanan hingga selesai.</li>
      </ol>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeModal('videoModalOverlay')">Tutup</button>
    </div>
  </div>
</div>


<div class="overlay hidden" id="confirmOverlay">
  <div class="modal" style="max-width:380px;">
    <div class="modal-head">
      <h3>Konfirmasi Hapus</h3>
      <button class="close-x" onclick="closeModal('confirmOverlay')">✕</button>
    </div>
    <div class="modal-body">
      <p id="confirmMsg" style="color:var(--muted);">Yakin ingin menghapus data ini?</p>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeModal('confirmOverlay')">Batal</button>
      <button class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
    </div>
  </div>
</div>


<div id="toast" class="toast hidden"></div>

<script>

const API = {
  BASE: (() => {
    const path = window.location.pathname;
    return path.substring(0, path.lastIndexOf('/') + 1);
  })(),
  token: localStorage.getItem('yj_token') || null,

  headers() {
    const h = { 'Content-Type': 'application/json' };
    if (this.token) h['Authorization'] = 'Bearer ' + this.token;
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


let _servicesCache = [];
let _ordersCache   = [];

function rupiah(n) { return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }
function esc(str) {
  return String(str).replace(/[&<>"']/g,
    c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function debounce(fn, ms) {
  let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}
function showToast(msg, type = 'ok') {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className = 'toast show ' + type;
  setTimeout(() => el.className = 'toast hidden', 3200);
}
function setLoading(btnId, loading, label = 'Simpan') {
  const btn = document.getElementById(btnId);
  if (!btn) return;
  btn.disabled = loading;
  btn.textContent = loading ? 'Menyimpan...' : label;
}
function placeholderImg(text, color) {
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><rect width="100%" height="100%" fill="${color}"/><text x="50%" y="50%" font-family="Space Grotesk" font-size="34" fill="#0a0d16" text-anchor="middle" dominant-baseline="central" font-weight="700">${text}</text></svg>`;
  return 'data:image/svg+xml;base64,' + btoa(svg);
}


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

async function handleLogin(e) {
  e.preventDefault();
  const btn = document.getElementById('loginBtn');
  btn.disabled = true; btn.textContent = 'Masuk...';
  try {
    const res = await API.post('/auth.php?action=login', {
      username: document.getElementById('loginUsername').value.trim(),
      password: document.getElementById('loginPassword').value,
    });
    API.token = res.token;
    localStorage.setItem('yj_token', res.token);
    localStorage.setItem('yj_user', JSON.stringify(res.user));
    enterApp(res.user);
  } catch (err) {
    showMsg(err.message || 'Username atau password salah.', 'err');
  } finally {
    btn.disabled = false; btn.textContent = 'Masuk ke Dashboard';
  }
}

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

async function logout() {
  try { await API.post('/auth.php?action=logout', {}); } catch (_) {}
  API.token = null;
  localStorage.removeItem('yj_token');
  localStorage.removeItem('yj_user');
  document.getElementById('app').classList.add('hidden');
  document.getElementById('loginScreen').classList.remove('hidden');
  document.getElementById('loginForm').reset();
}

function enterApp(user) {
  if (!user) return;
  document.getElementById('loginScreen').classList.add('hidden');
  document.getElementById('app').classList.remove('hidden');
  document.getElementById('userNameLabel').textContent = user.name || user.username;
  document.getElementById('userAvatar').textContent = (user.name || user.username).charAt(0).toUpperCase();
  renderServices();
  renderOrders();
  fillOrderServiceOptions();
  initSignaturePad();
}


(async function checkSession() {
  const savedToken = localStorage.getItem('yj_token');
  const savedUser = JSON.parse(localStorage.getItem('yj_user') || 'null');

  if (savedToken) API.token = savedToken;

  if (savedUser) {
    enterApp(savedUser);
  }

  if (!API.token) return;

  try {
    const res = await API.get('/auth.php?action=me');
    if (res && res.ok && res.user) {
      localStorage.setItem('yj_user', JSON.stringify(res.user));
      enterApp(res.user);
    }
  } catch (_) {
    // Tetap tampilkan dashboard dari data yang tersimpan saat refresh.
  }
})();


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

function handleMultiUpload(e) {
  const files = Array.from(e.target.files || []);
  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = ev => { tempImages.push(ev.target.result); renderPreview(); };
    reader.readAsDataURL(file);
  });
  e.target.value = '';
}
function renderPreview() {
  document.getElementById('svcPreview').innerHTML = tempImages.map((src, i) => `
    <div class="preview-item"><img src="${src}"><button type="button" onclick="removeTempImage(${i})">✕</button></div>
  `).join('');
}
function removeTempImage(i) { tempImages.splice(i, 1); renderPreview(); }

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
  } catch (_) {
    showToast('❌ Terjadi kesalahan saat menyimpan layanan.');
  } finally {
    setLoading('svcSaveBtn', false, 'Simpan Layanan');
  }
}

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


async function fillOrderServiceOptions() {
  try {
    const res = await API.get('/services.php');
    const sel = document.getElementById('ordLayanan');
    sel.innerHTML = res.data.map(s =>
      `<option value="${s.id}">${esc(s.nama)} — ${esc(s.game)}</option>`
    ).join('');
  } catch (_) {}
}

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
    ttd:        newTtd || null,   // null = server pertahankan TTD lama (saat PUT)
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

async function viewSignature(id) {
  try {
    const res = await API.get(`/orders.php?id=${id}`);
    if (!res.data.ttd) return;
    document.getElementById('signViewImg').src = res.data.ttd;
    openModal('signViewOverlay');
  } catch (_) {}
}


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
function getPos(e) {
  const rect = canvas.getBoundingClientRect();
  return {
    x: (e.touches ? e.touches[0].clientX : e.clientX) - rect.left,
    y: (e.touches ? e.touches[0].clientY : e.clientY) - rect.top,
  };
}
function startDraw(e) { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
function drawMove(e) {
  if (!drawing) return; e.preventDefault();
  const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke();
  hasDrawn = true; canvas.dataset.empty = '';
  document.querySelector('.sign-hint').style.display = 'none';
}
function endDraw() { drawing = false; }
function clearSignature() {
  if (!ctx) return;
  const rect = canvas.getBoundingClientRect();
  ctx.clearRect(0, 0, rect.width, rect.height);
  hasDrawn = false; canvas.dataset.empty = '1';
  const hint = document.querySelector('.sign-hint');
  if (hint) hint.style.display = 'block';
}
function getSignatureDataIfDrawn() {
  if (!hasDrawn) return null;
  const data = canvas.toDataURL('image/png');
  hasDrawn = false;
  return data;
}


function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
function openVideoModal() { openModal('videoModalOverlay'); }

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

function downloadBlob(content, filename, mime) {
  const blob = new Blob([content], { type: mime });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href = url; a.download = filename; a.click();
  URL.revokeObjectURL(url);
}


function toggleBgm() {
  const audio = document.getElementById('bgmAudio');
  const btn   = document.getElementById('bgmBtn');
  if (audio.paused) { audio.play(); btn.textContent = '🔊'; }
  else { audio.pause(); btn.textContent = '🔇'; }
}
</script>


<style>
.toast {
  position: fixed; bottom: 24px; right: 24px; z-index: 9999;
  background: var(--card, #1a2035); color: var(--text, #e8eaf0);
  border: 1px solid var(--border, #2a3350); border-radius: 10px;
  padding: 12px 20px; font-size: .9rem; font-family: 'Outfit', sans-serif;
  box-shadow: 0 8px 32px rgba(0,0,0,.35); transition: opacity .3s, transform .3s;
  opacity: 0; transform: translateY(12px); pointer-events: none;
}
.toast.show  { opacity: 1; transform: translateY(0); pointer-events: auto; }
.toast.hidden { display: none; }
.toast.err   { border-color: #fb7185; color: #fb7185; }
</style>
</body>
</html>
