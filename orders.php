<?php

// orders.php
// Fungsi utamanya adalah membaca, menambah, mengedit, dan menghapus pesanan beserta tanda tangan digital.

require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int) $_GET['id'] : null;

// Menampilkan semua pesanan atau detail satu pesanan sesuai request.
if ($method === 'GET') {
    if ($id) {
        $row = fetchOrder($id);
        if (!$row) error('Pesanan tidak ditemukan.', 404);
        respond(['ok' => true, 'data' => $row]);
    }

 
    $q    = '%' . trim($_GET['q'] ?? '') . '%';
    $stmt = db()->prepare(
        'SELECT o.id, o.nama, o.service_id, o.tanggal, o.status,
                (o.ttd IS NOT NULL AND o.ttd != \'\') AS has_ttd,
                s.nama AS layanan_nama
         FROM orders o
         LEFT JOIN services s ON s.id = o.service_id
         WHERE o.nama LIKE ? OR o.status LIKE ? OR s.nama LIKE ?
         ORDER BY o.created_at DESC'
    );
    $stmt->execute([$q, $q, $q]);
    respond(['ok' => true, 'data' => $stmt->fetchAll()]);
}


// Menyimpan pesanan baru ke database.
if ($method === 'POST') {
    requireAuth();
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $data = validateOrderBody($body);

    $stmt = db()->prepare(
        'INSERT INTO orders (nama, service_id, tanggal, status, ttd)
         VALUES (:nama, :service_id, :tanggal, :status, :ttd)'
    );
    $stmt->execute($data);
    $newId = (int) db()->lastInsertId();

    respond(['ok' => true, 'data' => fetchOrder($newId)], 201);
}


// Mengubah data pesanan yang sudah ada, termasuk tanda tangan digital.
if ($method === 'PUT') {
    requireAuth();
    if (!$id) error('ID pesanan diperlukan.');

    $body = json_decode(file_get_contents('php://input'), true) ?? [];

 
    if (!isset($body['ttd']) || $body['ttd'] === null) {
        $old = fetchOrder($id);
        if (!$old) error('Pesanan tidak ditemukan.', 404);
        $body['ttd'] = $old['ttd'];
    }

    $data = validateOrderBody($body);
    $data[':id'] = $id;

    $stmt = db()->prepare(
        'UPDATE orders SET nama=:nama, service_id=:service_id,
         tanggal=:tanggal, status=:status, ttd=:ttd
         WHERE id=:id'
    );
    if (!$stmt->execute($data) || $stmt->rowCount() === 0) {
        error('Pesanan tidak ditemukan.', 404);
    }

    respond(['ok' => true, 'data' => fetchOrder($id)]);
}


// Menghapus pesanan berdasarkan ID.
if ($method === 'DELETE') {
    requireAuth();
    if (!$id) error('ID pesanan diperlukan.');

    $stmt = db()->prepare('DELETE FROM orders WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) error('Pesanan tidak ditemukan.', 404);

    respond(['ok' => true, 'message' => 'Pesanan berhasil dihapus.']);
}

error('Method tidak didukung.', 405);


// Menjaga data pesanan tetap valid sebelum disimpan ke database.
function validateOrderBody(array $b): array {
    $nama      = trim($b['nama'] ?? '');
    $serviceId = isset($b['service_id']) && $b['service_id'] !== '' ? (int) $b['service_id'] : null;
    $tanggal   = $b['tanggal'] ?? '';
    $validStatus = ['Menunggu','Diproses','Selesai','Dibatalkan'];
    $status    = in_array($b['status'] ?? '', $validStatus) ? $b['status'] : 'Menunggu';
    $ttd       = $b['ttd'] ?? null;   
    if (!$nama) error('Nama klien wajib diisi.');
    if (!$tanggal || !strtotime($tanggal)) error('Tanggal tidak valid.');

    return [
        ':nama'       => $nama,
        ':service_id' => $serviceId,
        ':tanggal'    => $tanggal,
        ':status'     => $status,
        ':ttd'        => $ttd,
    ];
}

// Mengambil satu pesanan lengkap beserta nama layanan terkait.
function fetchOrder(int $id): ?array {
    $stmt = db()->prepare(
        'SELECT o.*, s.nama AS layanan_nama
         FROM orders o
         LEFT JOIN services s ON s.id = o.service_id
         WHERE o.id = ?'
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        
        $row['has_ttd'] = !empty($row['ttd']);
    }
    return $row ?: null;
}
