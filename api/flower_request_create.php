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
    $fullName = trim((string)($_POST['full_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phoneCode = trim((string)($_POST['phone_code'] ?? ''));
    $mobile = trim((string)($_POST['mobile'] ?? ''));
    $country = trim((string)($_POST['country'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));

    $phone = trim($phoneCode . ' ' . $mobile);

    if ($postId <= 0) {
        echo json_encode(['ok' => false, 'message' => 'Invalid memorial id.']);
        exit;
    }

    if ($fullName === '' || $email === '' || $phone === '' || $country === '') {
        echo json_encode(['ok' => false, 'message' => 'Please fill all required fields to request flowers.']);
        exit;
    }

    $check = db()->prepare("SELECT id FROM posts WHERE id=? LIMIT 1");
    $check->execute([$postId]);
    if (!$check->fetchColumn()) {
        echo json_encode(['ok' => false, 'message' => 'Memorial not found.']);
        exit;
    }

    $stmt = db()->prepare("
        INSERT INTO flower_requests
            (post_id, full_name, email, phone, country, message, status)
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $postId,
        $fullName,
        $email,
        $phone,
        $country,
        $message !== '' ? $message : null,
    ]);

    echo json_encode([
        'ok' => true,
        'message' => 'Thank you. Our team will contact you soon about your flower request.'
    ]);

} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'message' => 'Failed to submit flower request.']);
}