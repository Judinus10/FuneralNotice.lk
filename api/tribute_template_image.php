<?php
declare(strict_types=1);

require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mode = strtolower(trim((string)($_GET['mode'] ?? 'image')));

if ($id <= 0) {
    http_response_code(400);
    exit('Invalid template id.');
}

$fieldBlob = $mode === 'banner' ? 'banner_blob' : 'image_blob';
$fieldMime = $mode === 'banner' ? 'banner_mime' : 'image_mime';

try {
    $pdo = db();

    $stmt = $pdo->prepare("
        SELECT {$fieldBlob} AS blob_data, {$fieldMime} AS mime_type
        FROM tribute_templates
        WHERE id = ?
          AND is_active = 1
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || empty($row['blob_data'])) {
        http_response_code(404);
        exit('Image not found.');
    }

    $mime = trim((string)($row['mime_type'] ?? ''));
    if ($mime === '') {
        $mime = 'image/jpeg';
    }

    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=86400');
    echo $row['blob_data'];
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    exit('Server error.');
}