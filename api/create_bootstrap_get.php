<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');
session_start();

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

function out(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function buildCaptcha(): array
{
    $ops = ['+', '-'];
    $op = $ops[array_rand($ops)];

    $a = random_int(1, 9);
    $b = random_int(1, 9);

    if ($op === '-' && $b > $a) {
        [$a, $b] = [$b, $a];
    }

    $answer = ($op === '+') ? ($a + $b) : ($a - $b);
    $question = "What is $a $op $b ?";

    $_SESSION['captcha_answer'] = (string)$answer;
    $_SESSION['captcha_question'] = $question;
    unset($_SESSION['captcha_passed']);

    return [
        'question' => $question,
        'answer' => (string)$answer,
    ];
}

try {
    $pdo = db();

    $countryStmt = $pdo->query("
        SELECT name
        FROM phone_countries
        WHERE name IS NOT NULL AND TRIM(name) <> ''
        ORDER BY sort_order, name
    ");
    $countryNames = $countryStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    $phoneStmt = $pdo->query("
        SELECT id, name, code
        FROM phone_countries
        WHERE code IS NOT NULL AND TRIM(code) <> ''
        ORDER BY sort_order, name
    ");
    $phoneRows = $phoneStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $captcha = buildCaptcha();

    $countries = array_map(
        static fn($name): array => [
            'value' => (string)$name,
            'label' => (string)$name,
        ],
        $countryNames
    );

    $phoneCodes = array_map(
        static fn(array $row): array => [
            'id'    => isset($row['id']) ? (int)$row['id'] : 0,
            'name'  => (string)($row['name'] ?? ''),
            'code'  => trim((string)($row['code'] ?? '')),
            'value' => trim((string)($row['code'] ?? '')),
            'label' => trim((string)($row['name'] ?? '')) . ' (' . trim((string)($row['code'] ?? '')) . ')',
        ],
        $phoneRows
    );

    out([
        'ok' => true,
        'message' => '',
        'question' => $captcha['question'],
        'default_country' => 'Sri Lanka',

        'post_types' => [
            ['value' => 'obituary', 'label' => 'Obituary'],
            ['value' => 'remembrance', 'label' => 'Remembrance'],
        ],

        'religions' => [
            'Buddhism',
            'Hinduism',
            'Christianity',
            'Islam',
            'Roman Catholic',
            'Other',
        ],

        'id_types' => [
            'NIC',
            'Passport',
        ],

        'countries' => $countries,
        'phone_codes' => $phoneCodes,
    ]);
} catch (Throwable $e) {
    error_log('create_bootstrap_get.php failed: ' . $e->getMessage());

    out([
        'ok' => false,
        'message' => 'Failed to load create form.',
    ], 500);
}