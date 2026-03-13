<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['ok' => false, 'message' => 'Invalid request method.']);
        exit;
    }

    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        echo json_encode(['ok' => false, 'message' => 'Security token invalid. Please try again.']);
        exit;
    }

    $postId = isset($_POST['post_id']) ? max(0, (int)$_POST['post_id']) : 0;
    $name = trim((string)($_POST['sender_name'] ?? ''));
    $msg = trim((string)($_POST['message'] ?? ''));

    if ($postId <= 0) {
        echo json_encode(['ok' => false, 'message' => 'Invalid memorial id.']);
        exit;
    }

    if ($name === '' || $msg === '') {
        echo json_encode(['ok' => false, 'message' => 'Please fill your name and message.']);
        exit;
    }

    $check = db()->prepare("SELECT id FROM posts WHERE id=? LIMIT 1");
    $check->execute([$postId]);
    if (!$check->fetchColumn()) {
        echo json_encode(['ok' => false, 'message' => 'Memorial not found.']);
        exit;
    }

    $ins = db()->prepare("
        INSERT INTO tributes (post_id, tribute_type, message, sender_name, status, created_at)
        VALUES (?, 'message', ?, ?, 'pending', NOW())
    ");
    $ins->execute([$postId, $msg, $name]);

    echo json_encode([
        'ok' => true,
        'message' => 'Thank you. Your tribute is waiting for admin approval.'
    ]);

} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'message' => 'Failed to submit tribute.']);
}