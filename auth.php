<?php

// auth.php
// Fungsi utamanya adalah menangani login, register, logout, dan pengecekan sesi user.

require_once __DIR__ . '/config.php';

ensureDemoUser();

$action = $_GET['action'] ?? '';
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

function ensureDemoUser(): void {
    db()->prepare('DELETE FROM users WHERE username = ?')
        ->execute(['admin']);

    $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute(['yasadmin']);
    if ($stmt->fetch()) return;

    $hash = password_hash('yas123', PASSWORD_BCRYPT);
    db()->prepare('INSERT INTO users (name, username, password) VALUES (?, ?, ?)')
        ->execute(['Demo Admin', 'yasadmin', $hash]);
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
