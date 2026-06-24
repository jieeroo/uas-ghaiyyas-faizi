<?php

// config.php
// Berisi koneksi database, lama sesi login, dan fungsi bantu backend seperti auth dan response JSON.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'yassjokiin');
define('DB_USER', 'root');          
define('DB_PASS', '');              
define('DB_CHARSET', 'utf8mb4');

define('SESSION_LIFETIME', 60 * 60 * 8);  
define('ALLOWED_ORIGIN', '*');              


// Membuat koneksi database sekali saja dan mengembalikannya untuk dipakai berulang kali.
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}


// Mengirim response dalam format JSON ke frontend.
function respond(mixed $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
    header('Access-Control-Allow-Credentials: true');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}


// Mengirim response error yang konsisten ke frontend.
function error(string $msg, int $code = 400): never {
    respond(['ok' => false, 'error' => $msg], $code);
}


// Membaca token Bearer dari header Authorization request.
function bearerToken(): ?string {
    $h = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (str_starts_with($h, 'Bearer ')) {
        return trim(substr($h, 7));
    }

    $alt = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? '';
    if ($alt !== '') {
        return trim($alt);
    }

    $queryToken = $_GET['token'] ?? null;
    if (is_string($queryToken) && $queryToken !== '') {
        return trim($queryToken);
    }

    return null;
}


// Memastikan request memiliki sesi login yang valid sebelum mengakses data sensitif.
function requireAuth(): int {
    $token = bearerToken();
    if ($token) {
        $stmt = db()->prepare(
            'SELECT user_id FROM sessions WHERE token = ? AND expires_at > NOW()'
        );
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        if ($row) {
            return (int) $row['user_id'];
        }
    }

    if (!empty($_SESSION['user_id'])) {
        return (int) $_SESSION['user_id'];
    }

    error('Unauthorized — sesi tidak valid atau sudah berakhir.', 401);
}


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    http_response_code(204);
    exit;
}
