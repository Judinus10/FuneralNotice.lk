<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

function out(bool $ok, string $message, array $extra = []): void
{
    echo json_encode(array_merge([
        'ok' => $ok,
        'message' => $message,
    ], $extra), JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        out(false, 'Invalid request method.');
    }

    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        out(false, 'Security token invalid. Please refresh and try again.');
    }

    $postId = isset($_POST['post_id']) ? max(0, (int)$_POST['post_id']) : 0;
    $slug = strtolower(trim((string)($_POST['tribute_slug'] ?? '')));
    $byName = trim((string)($_POST['by_name'] ?? ''));
    $byOrg = trim((string)($_POST['by_org'] ?? ''));
    $byCountry = trim((string)($_POST['by_country'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));
    $extraJsonRaw = trim((string)($_POST['extra_json'] ?? ''));

    if ($postId <= 0) {
        out(false, 'Invalid memorial id.');
    }

    if ($slug === '') {
        out(false, 'Tribute type is missing.');
    }

    if ($byName === '' || $message === '') {
        out(false, 'Please fill your name and tribute message.');
    }

    $pdo = db();

    $postSt = $pdo->prepare("SELECT id FROM posts WHERE id = ? LIMIT 1");
    $postSt->execute([$postId]);
    if (!$postSt->fetchColumn()) {
        out(false, 'Memorial not found.');
    }

    $typeSt = $pdo->prepare("
        SELECT id, slug, title
        FROM tribute_types
        WHERE slug = ? AND is_active = 1
        LIMIT 1
    ");
    $typeSt->execute([$slug]);
    $type = $typeSt->fetch(PDO::FETCH_ASSOC);

    if (!$type) {
        out(false, 'Tribute type not configured. Add it in tribute_types table.');
    }

    $typeId = (int)$type['id'];

    $templateId = null;

    try {
        // First try by tribute_type_id
        $tplSt = $pdo->prepare("SELECT id FROM tribute_templates WHERE tribute_type_id = ? ORDER BY id ASC LIMIT 1");
        $tplSt->execute([$typeId]);
        $templateId = $tplSt->fetchColumn();
    } catch (Throwable $e) {
        $templateId = null;
    }

    if (!$templateId) {
        try {
            $tplFallback = $pdo->query("SELECT id FROM tribute_templates ORDER BY id ASC LIMIT 1");
            $templateId = $tplFallback ? $tplFallback->fetchColumn() : null;
        } catch (Throwable $e) {
            $templateId = null;
        }
    }

    if (!$templateId) {
        out(false, 'No tribute template found. Add at least one row in tribute_templates.');
    }

    $entryCols = [];
    try {
        $entryCols = $pdo->query("SHOW COLUMNS FROM tribute_entries")->fetchAll(PDO::FETCH_COLUMN);
    } catch (Throwable $e) {
        out(false, 'tribute_entries table is missing or invalid.');
    }

    $entryCols = array_flip($entryCols);

    $data = [];

    if (isset($entryCols['post_id'])) $data['post_id'] = $postId;
    if (isset($entryCols['tribute_type_id'])) $data['tribute_type_id'] = $typeId;
    if (isset($entryCols['template_id'])) $data['template_id'] = (int)$templateId;
    if (isset($entryCols['delivery'])) $data['delivery'] = 0;
    if (isset($entryCols['by_name'])) $data['by_name'] = $byName;
    if (isset($entryCols['by_org'])) $data['by_org'] = $byOrg !== '' ? $byOrg : null;
    if (isset($entryCols['by_country'])) $data['by_country'] = $byCountry !== '' ? $byCountry : null;
    if (isset($entryCols['message'])) $data['message'] = $message;
    if (isset($entryCols['status'])) $data['status'] = 'pending';
    if (isset($entryCols['extra_json'])) $data['extra_json'] = $extraJsonRaw !== '' ? $extraJsonRaw : null;
    if (isset($entryCols['created_at'])) $data['created_at'] = date('Y-m-d H:i:s');

    if (empty($data)) {
        out(false, 'No matching columns found in tribute_entries table.');
    }

    $columns = array_keys($data);
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $sql = "INSERT INTO tribute_entries (" . implode(', ', $columns) . ") VALUES ($placeholders)";

    $st = $pdo->prepare($sql);
    $st->execute(array_values($data));

    out(true, 'Thank you. Your tribute is waiting for admin approval.');

} catch (Throwable $e) {
    out(false, 'Failed to save tribute.');
}