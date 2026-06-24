<?php
// services.php
// Fungsi utamanya adalah membaca, menambah, mengedit, dan menghapus layanan beserta gambar pendukungnya.


require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int) $_GET['id'] : null;

// Menampilkan semua layanan atau detail satu layanan sesuai request.
if ($method === 'GET') {
    if ($id) {
        // detail satu
        $stmt = db()->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([$id]);
        $svc = $stmt->fetch();
        if (!$svc) error('Layanan tidak ditemukan.', 404);
        $svc['images'] = fetchImages($id);
        respond(['ok' => true, 'data' => $svc]);
    }

    
    $q      = '%' . trim($_GET['q'] ?? '') . '%';
    $stmt   = db()->prepare(
        'SELECT * FROM services
         WHERE nama LIKE ? OR game LIKE ? OR kategori LIKE ?
         ORDER BY created_at DESC'
    );
    $stmt->execute([$q, $q, $q]);
    $rows = $stmt->fetchAll();

    
    foreach ($rows as &$row) {
        $imgStmt = db()->prepare(
            'SELECT image_data FROM service_images WHERE service_id = ? ORDER BY sort_order LIMIT 1'
        );
        $imgStmt->execute([$row['id']]);
        $img = $imgStmt->fetch();
        $row['images'] = $img ? [$img['image_data']] : [];
    }
    unset($row);

    respond(['ok' => true, 'data' => $rows]);
}


// Menyimpan layanan baru ke database dan menyimpan gambarnya.
if ($method === 'POST') {
    requireAuth();
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $data = validateServiceBody($body);

    $stmt = db()->prepare(
        'INSERT INTO services (nama, game, kategori, harga, rating, status, deskripsi)
         VALUES (:nama, :game, :kategori, :harga, :rating, :status, :deskripsi)'
    );
    $stmt->execute($data);
    $newId = (int) db()->lastInsertId();

    saveImages($newId, $body['images'] ?? []);
    $svc = fetchService($newId);
    respond(['ok' => true, 'data' => $svc], 201);
}


// Mengubah data layanan yang sudah ada dan memperbarui gambar terkait.
if ($method === 'PUT') {
    requireAuth();
    if (!$id) error('ID layanan diperlukan.');
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $data = validateServiceBody($body);
    $data[':id'] = $id;

    $stmt = db()->prepare(
        'UPDATE services SET nama=:nama, game=:game, kategori=:kategori,
         harga=:harga, rating=:rating, status=:status, deskripsi=:deskripsi
         WHERE id=:id'
    );
    if (!$stmt->execute($data) || $stmt->rowCount() === 0) {
        error('Layanan tidak ditemukan.', 404);
    }

    
    if (isset($body['images'])) {
        db()->prepare('DELETE FROM service_images WHERE service_id = ?')->execute([$id]);
        saveImages($id, $body['images']);
    }

    respond(['ok' => true, 'data' => fetchService($id)]);
}


// Menghapus layanan berdasarkan ID.
if ($method === 'DELETE') {
    requireAuth();
    if (!$id) error('ID layanan diperlukan.');

    $stmt = db()->prepare('DELETE FROM services WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) error('Layanan tidak ditemukan.', 404);

    respond(['ok' => true, 'message' => 'Layanan berhasil dihapus.']);
}

error('Method tidak didukung.', 405);


// Menjaga data layanan tetap valid sebelum disimpan ke database.
function validateServiceBody(array $b): array {
    $nama     = trim($b['nama']     ?? '');
    $game     = trim($b['game']     ?? '');
    $kategori = trim($b['kategori'] ?? '');
    $harga    = (int) ($b['harga']  ?? 0);
    $rating   = min(5.0, max(0.0, (float) ($b['rating'] ?? 0)));
    $status   = in_array($b['status'] ?? '', ['Aktif','Nonaktif']) ? $b['status'] : 'Aktif';
    $deskripsi = trim($b['deskripsi'] ?? '');

    if (!$nama || !$game || !$kategori) error('Nama, game, dan kategori wajib diisi.');

    return [
        ':nama'      => $nama,
        ':game'      => $game,
        ':kategori'  => $kategori,
        ':harga'     => $harga,
        ':rating'    => $rating,
        ':status'    => $status,
        ':deskripsi' => $deskripsi,
    ];
}

// Menyimpan data gambar layanan ke tabel service_images.
function saveImages(int $serviceId, array $images): void {
    if (!$images) return;
    $stmt = db()->prepare(
        'INSERT INTO service_images (service_id, image_data, sort_order) VALUES (?, ?, ?)'
    );
    foreach ($images as $i => $imgData) {
        $stmt->execute([$serviceId, $imgData, $i]);
    }
}

// Mengambil daftar gambar yang terkait dengan satu layanan.
function fetchImages(int $serviceId): array {
    $stmt = db()->prepare(
        'SELECT image_data FROM service_images WHERE service_id = ? ORDER BY sort_order'
    );
    $stmt->execute([$serviceId]);
    return array_column($stmt->fetchAll(), 'image_data');
}

// Mengambil satu layanan lengkap beserta gambarnya untuk ditampilkan ke frontend.
function fetchService(int $id): ?array {
    $stmt = db()->prepare('SELECT * FROM services WHERE id = ?');
    $stmt->execute([$id]);
    $svc = $stmt->fetch();
    if ($svc) $svc['images'] = fetchImages($id);
    return $svc ?: null;
}