<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

function h(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$postId = isset($_GET['post_id']) ? max(0, (int)$_GET['post_id']) : 0;
if ($postId <= 0) {
    http_response_code(400);
    exit('Invalid post id');
}

$post = null;
try {
    $st = db()->prepare("SELECT id, full_name, type, status FROM posts WHERE id = ? LIMIT 1");
    $st->execute([$postId]);
    $post = $st->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $post = null;
}

if (!$post) {
    http_response_code(404);
    exit('Memorial not found');
}

$postName = trim((string)($post['full_name'] ?? 'Memorial'));
$csrf = $_SESSION['csrf'];