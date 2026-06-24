<?php require_once __DIR__ . '/config.php';
$initialAuth = [
  'loggedIn' => !empty($_SESSION['user_id']),
  'user' => !empty($_SESSION['user_id']) ? [
    'id' => (int) $_SESSION['user_id'],
    'name' => $_SESSION['user_name'] ?? '',
    'username' => $_SESSION['user_username'] ?? '',
  ] : null,
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Yassjokiin — Joki Game Terpercaya</title>
<script>
window.__INITIAL_AUTH__ = <?php echo json_encode($initialAuth, JSON_UNESCAPED_UNICODE); ?>;
(function () {
  function getCookie(name) {
    const m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
    return m ? decodeURIComponent(m[1]) : '';
  }
  const auth = window.__INITIAL_AUTH__ || {};
  const token = localStorage.getItem('yj_token') || sessionStorage.getItem('yj_token') || getCookie('yj_token');
  const loggedInFlag = auth.loggedIn || localStorage.getItem('yj_logged_in') === '1' || sessionStorage.getItem('yj_logged_in') === '1' || !!getCookie('yj_logged_in');
  document.documentElement.setAttribute('data-auth-view', (auth.loggedIn || token || loggedInFlag) ? 'app' : 'login');
})();
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Theme Toggle Button -->
<div class="theme-shell">
  <button id="themeToggle" class="theme-toggle" type="button" aria-label="Ganti tema" title="Ganti tema"></button>
</div>
<!-- End Theme Toggle Button -->

<!-- Login Screen -->
<div id="loginScreen" class="login-screen">
  <div class="login-box">
    <div class="brand"><img src="img/download%20(1).jpg" alt="Yassjokiin logo" class="brand-mark"><span class="yass">Yass</span><span class="jokiin">jokiin</span></div>
    <p class="login-sub">Platform jasa joki game — masuk untuk kelola pesanan &amp; layanan</p>

    <div class="tabs">
      <button class="tab-btn active" id="tabLoginBtn" onclick="switchTab('login')">Masuk</button>
      <button class="tab-btn" id="tabRegisterBtn" onclick="switchTab('register')">Daftar</button>
    </div>
<!-- End Login Screen -->

<!-- Login Form -->
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
<!-- End Login Form -->

<!-- Register Form -->
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
    
    <div class="hint-box">Akun demo: <b>admin</b> / <b>yas123</b></div>
  </div>
</div>
<!-- End Register Form -->

<!-- Main App -->
<div id="app" class="hidden">
  <div class="topbar">
    <div class="container nav">
      <div class="brand"><img src="img/download%20(1).jpg" alt="Yassjokiin logo" class="brand-mark"><span class="yass">Yass</span><span class="jokiin">jokiin</span></div>
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
<!-- End Main App -->


<!-- Hero Section -->  
  <section class="hero" id="beranda">
    <div class="container hero-grid">
      <div>
        <div class="eyebrow">Sistem sederhana • siap presentasi</div>
        <h1>Kelola jasa joki game dengan alur yang <span>mudah dipahami</span>.</h1>
        <p class="lead">Yassjokiin membantu Anda mengatur layanan, pesanan, dan persetujuan klien dalam satu dashboard yang ringkas dan menarik.</p>
        <div class="hero-actions">
          <a href="#layanan" class="btn btn-primary">Lihat Layanan</a>
        </div>
<!-- End Hero Section -->

<!-- Stats Section -->
        <div class="stats">
          <div class="stat"><div class="stat-num" id="statLayanan">—</div><div class="stat-label">Layanan Aktif</div></div>
          <div class="stat"><div class="stat-num" id="statPesanan">—</div><div class="stat-label">Total Pesanan</div></div>
          <div class="stat"><div class="stat-num" id="statSelesai">—</div><div class="stat-label">Pesanan Selesai</div></div>
          <div class="stat"><div class="stat-num" id="statRating">—</div><div class="stat-label">Rating Rata²</div></div>
        </div>
      </div>
<!-- End Stats Section -->

<!-- Info Card Section -->
      <div class="info-card">
        <div class="info-card-head">Alur singkat</div>
        <div class="step-list">
          <div class="step-item">
            <span>1</span>
            <div><strong>Tambah layanan</strong><p>Isi data game, kategori, harga, dan status.</p></div>
          </div>
          <div class="step-item">
            <span>2</span>
            <div><strong>Catat pesanan</strong><p>Buat pesanan baru dan tandatangani persetujuan.</p></div>
          </div>
          <div class="step-item">
            <span>3</span>
            <div><strong>Pantau progres</strong><p>Perbarui status sampai pesanan selesai.</p></div>
          </div>
        </div>
      </div>
    </div>
  </section>
<!-- End Info Card Section -->

 <!-- Services Section --> 
  <section class="section" id="layanan">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Kelola Data</div>
          <h2>Daftar Layanan Joki</h2>
          <p>Tambah, ubah, hapus, dan lihat detail layanan dengan antarmuka yang sederhana.</p>
        </div>
        <button class="btn btn-primary" onclick="openServiceModal()">+ Tambah Layanan</button>
      </div>
<!-- End Services Section -->

<!-- Services Table -->
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
<!-- End Services Table -->

<!-- Services Table Body -->
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
<!-- End Services Table Body -->
 
<!-- Orders Section -->
  <section class="section" id="pesanan" style="background:var(--bg-2);">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Transaksi</div>
          <h2>Daftar Pesanan &amp; TTD Digital</h2>
          <p>Buat pesanan baru, simpan tanda tangan digital, lalu ubah statusnya dari menunggu hingga selesai.</p>
        </div>
        <button class="btn btn-accent" onclick="openOrderModal()">+ Tambah Pesanan</button>
      </div>
<!-- End Orders Section -->

<!-- Orders Table -->
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
  <!-- End Orders Table -->

 <!-- Testimonials Section -->
  <section class="section" id="testimoni">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="eyebrow">Bukti Sosial</div>
          <h2>Cuplikan &amp; Testimoni</h2>
          <p>Tampilkan bukti sosial atau video singkat agar presentasi terasa lebih hidup.</p>
        </div>
      </div>
      <div class="media-grid">
        <div class="media-frame">
          <video controls autoplay loop muted>
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
<!-- End Testimonials Section -->

<!-- Footer Section -->
  <footer>
    <div class="container foot-row">
      <div class="brand" style="font-size:1rem;"><img src="img/download%20(1).jpg" alt="Yassjokiin logo" class="brand-mark brand-mark-sm"><span class="yass">Yass</span><span class="jokiin">jokiin</span></div>
      <div>© 2026 Yassjokiin — Jasa Joki Game Terpercaya.</div>
    </div>
  </footer>
<!-- End Footer Section -->

<!-- Service Modal -->
<div class="overlay hidden" id="serviceModalOverlay">
  <div class="modal">
    <div class="modal-head">
      <h3 id="serviceModalTitle">Tambah Layanan</h3>
      <button class="close-x" onclick="closeModal('serviceModalOverlay')">✕</button>
    </div>
<!-- service modal end --> 

<!-- Service Form -->
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
<!-- End Service Form -->

<!-- View Modal -->
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
<!-- End View Modal -->

<!-- Order Modal -->
<div class="overlay hidden" id="orderModalOverlay">
  <div class="modal">
    <div class="modal-head">
      <h3 id="orderModalTitle">Tambah Pesanan</h3>
      <button class="close-x" onclick="closeModal('orderModalOverlay')">✕</button>
    </div>
<!-- Order Modal End -->


<!-- Order Form -->
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
<!-- End Order Form -->


<!-- View Signature Modal -->
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
<!-- End View Signature Modal -->

<!-- Confirmation Modal -->
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

<script src="yas.js"></script>
</body>
</html>
<!-- End Confirmation Modal -->