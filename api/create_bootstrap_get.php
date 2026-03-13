<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');
session_start();

header('Content-Type: application/json; charset=utf-8');

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

    return ['question' => $question];
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

$user = preg_replace('/\s+/', '', (string)($data['answer'] ?? ''));
$expected = preg_replace('/\s+/', '', (string)($_SESSION['captcha_answer'] ?? ''));

if ($user !== '' && $expected !== '' && hash_equals($expected, $user)) {
    $_SESSION['captcha_passed'] = 1;
    out(['ok' => true]);
}

$newCaptcha = buildCaptcha();

out([
    'ok' => false,
    'message' => 'Captcha incorrect. Try the new one.',
    'question' => $newCaptcha['question']
], 422);