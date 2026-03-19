<?php
declare(strict_types=1);

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

function out(array $data): void
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $q = trim((string)($_GET['q'] ?? ''));

    if ($q === '') {
        out(['ok' => true, 'results' => []]);
    }

    $pdo = db();

    $columnsStmt = $pdo->query("SHOW COLUMNS FROM posts");
    $existingColumns = array_map(
        fn($r) => $r['Field'],
        $columnsStmt->fetchAll(PDO::FETCH_ASSOC) ?: []
    );

    $candidateColumns = [
        'full_name',
        'religion',
        'place',
        'location',
        'address',
        'country',
        'district',
        'city',
        'village',
        'type',
        'status',
        'born_date',
        'birth_date',
        'dob',
        'death_date',
        'died_date',
        'dod',
        'created_at'
    ];

    $searchable = array_values(array_filter($candidateColumns, fn($c) => in_array($c, $existingColumns, true)));

    if (empty($searchable)) {
        out(['ok' => false, 'message' => 'No searchable columns found in posts table.']);
    }

    $concatParts = [];
    foreach ($searchable as $col) {
        if (in_array($col, ['born_date', 'birth_date', 'dob', 'death_date', 'died_date', 'dod', 'created_at'], true)) {
            $concatParts[] = "DATE_FORMAT($col, '%Y-%m-%d')";
            $concatParts[] = "DATE_FORMAT($col, '%d-%m-%Y')";
            $concatParts[] = "DATE_FORMAT($col, '%d/%m/%Y')";
            $concatParts[] = "DATE_FORMAT($col, '%M %Y')";
            $concatParts[] = "DATE_FORMAT($col, '%Y')";
        } else {
            $concatParts[] = $col;
        }
    }

    $searchExpr = "LOWER(CONCAT_WS(' ', " . implode(', ', $concatParts) . "))";
    $needle = '%' . mb_strtolower($q, 'UTF-8') . '%';

    $titleColumn = in_array('full_name', $existingColumns, true) ? 'full_name' : $searchable[0];
    $typeColumn = in_array('type', $existingColumns, true) ? 'type' : "''";
    $countryColumn = in_array('country', $existingColumns, true) ? 'country' : "''";
    $locationColumn = in_array('location', $existingColumns, true)
        ? 'location'
        : (in_array('place', $existingColumns, true) ? 'place' : "''");
    $statusColumn = in_array('status', $existingColumns, true) ? 'status' : "''";
    $slugOrId = in_array('id', $existingColumns, true) ? 'id' : $titleColumn;

    $sql = "
        SELECT
            id,
            {$titleColumn} AS title,
            {$typeColumn} AS item_type,
            {$countryColumn} AS country_name,
            {$locationColumn} AS location_name,
            {$statusColumn} AS item_status
        FROM posts
        WHERE {$searchExpr} LIKE :q
        ORDER BY id DESC
        LIMIT 12
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => $needle]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $results = array_map(function ($row) {
        return [
            'id' => (int)($row['id'] ?? 0),
            'title' => (string)($row['title'] ?? ''),
            'type' => (string)($row['item_type'] ?? ''),
            'country' => (string)($row['country_name'] ?? ''),
            'location' => (string)($row['location_name'] ?? ''),
            'status' => (string)($row['item_status'] ?? ''),
            'url' => 'memorial-detail.php?id=' . (int)($row['id'] ?? 0),
        ];
    }, $rows);

    out([
        'ok' => true,
        'results' => $results
    ]);
} catch (Throwable $e) {
    error_log('live_search.php error: ' . $e->getMessage());
    out([
        'ok' => false,
        'message' => 'Search failed.'
    ]);
}