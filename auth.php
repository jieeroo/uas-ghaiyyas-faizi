<?php

// auth.php
// Fungsi utamanya adalah menangani login, register, logout, dan pengecekan sesi user.

require_once __DIR__ . '/config.php';

ensureDemoUser();

$action = $_GET['action'] ?? '';
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

function ensureDemoUser(): void {
    // Pastikan user admin dari SQL seed tetap ada.
    // Username: admin | Password: yas123
    $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute(['admin']);
    if ($stmt->fetch()) return;

    $hash = password_hash('yas123', PASSWORD_BCRYPT);
    db()->prepare('INSERT INTO users (name, username, password) VALUES (?, ?, ?)')
        ->execute(['Administrator', 'admin', $hash]);
}

// Proses login user dan membuat session token jika kredensial benar.
if ($action === 'login') {
    $username = trim($body['username'] ?? '');
    $password = $body['password'] ?? '';

    if (!$username || !$password) error('Username dan password wajib diisi.');

    $stmt = db()->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        error('Username atau password salah.', 401);
    }

    // Buat token baru
    $token     = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);

    db()->prepare(
        'INSERT INTO sessions (token, user_id, expires_at) VALUES (?, ?, ?)'
    )->execute([$token, $user['id'], $expiresAt]);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_username'] = $user['username'];

    respond([
        'ok'    => true,
        'token' => $token,
        'user'  => ['id' => $user['id'], 'name' => $user['name'], 'username' => $user['username']],
    ]);
}


// Proses pendaftaran akun baru dan menyimpan password terenkripsi.
if ($action === 'register') {
    $name     = trim($body['name'] ?? '');
    $username = trim($body['username'] ?? '');
    $password = $body['password'] ?? '';

    if (!$name || !$username || !$password) error('Semua kolom wajib diisi.');
    if (mb_strlen($password) < 6) error('Password minimal 6 karakter.');

    
    $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) error('Username sudah digunakan.');

    $hash = password_hash($password, PASSWORD_BCRYPT);
    db()->prepare(
        'INSERT INTO users (name, username, password) VALUES (?, ?, ?)'
    )->execute([$name, $username, $hash]);

    respond(['ok' => true, 'message' => 'Akun berhasil dibuat! Silakan masuk.']);
}


// Menghapus sesi aktif saat user melakukan logout.
if ($action === 'logout') {
    $token = bearerToken();
    if ($token) {
        db()->prepare('DELETE FROM sessions WHERE token = ?')->execute([$token]);
    }
    session_unset();
    session_destroy();
    respond(['ok' => true]);
}


// Mengambil data user yang sedang login berdasarkan token sesi.
if ($action === 'me') {
    $userId = requireAuth();
    $stmt = db()->prepare('SELECT id, name, username FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    respond(['ok' => true, 'user' => $user]);
}

error('Action tidak dikenal.');