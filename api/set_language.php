<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

$lang = trim((string)($_POST['lang'] ?? 'en'));
$allowed = ['en', 'ta', 'si'];

if (!in_array($lang, $allowed, true)) {
    echo json_encode([
        'ok' => false,
        'message' => 'Invalid language.'
    ]);
    exit;
}

$_SESSION['lang'] = $lang;

echo json_encode([
    'ok' => true,
    'lang' => $lang
]);
exit;