<?php
// tributes.php
declare(strict_types=1);

if (!isset($id)) {
    http_response_code(500);
    exit('Include tributes.php from details.php after $id is set.');
}

require_once __DIR__ . '/db.php';

$postId = (int)$id;

/**
 * Ensure tribute_types table exists
 */
try {
    db()->exec("
        CREATE TABLE IF NOT EXISTS tribute_types (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(64) NOT NULL,
            title VARCHAR(120) NOT NULL,
            subtitle VARCHAR(255) DEFAULT NULL,
            icon_path VARCHAR(255) DEFAULT NULL,
            file_name VARCHAR(120) NOT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} catch (Throwable $e) {
    // ignore
}

/**
 * Load tribute types
 */
try {
    $st = db()->prepare("
        SELECT id, slug, title, subtitle, file_name
        FROM tribute_types
        WHERE is_active = 1
        ORDER BY sort_order ASC, id ASC
    ");
    $st->execute();
    $tributeTypes = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $tributeTypes = [];
}
?>

<link rel="stylesheet" href="style/tributes-popup.css">

<!-- Tribute Type Chooser Modal -->
<div id="tributeChooserModal" class="tribute-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="tributeChooserTitle">
    <div class="tribute-modal tribute-chooser-modal">
        <div class="tribute-modal-head">
            <h3 id="tributeChooserTitle">Select Tribute Type</h3>
        </div>

        <?php if (!empty($tributeTypes)): ?>
            <div class="tribute-chooser-grid">
                <?php foreach ($tributeTypes as $t): ?>
                    <?php
                    $fileBase = pathinfo($t['file_name'], PATHINFO_FILENAME);

                    $candidates = [
                        "assets/chooser_{$fileBase}.png",
                        "assets/chooser_{$fileBase}.jpg",
                        "assets/chooser_{$fileBase}.jpeg",
                        "assets/chooser_{$fileBase}.webp",
                    ];

                    $iconRel = null;
                    foreach ($candidates as $relPath) {
                        if (file_exists(__DIR__ . '/' . $relPath)) {
                            $iconRel = $relPath;
                            break;
                        }
                    }
                    ?>
                    <button
                        type="button"
                        class="tribute-chooser-card"
                        data-tribute-file="<?= htmlspecialchars($t['file_name'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                        <div class="tribute-chooser-media">
                            <?php if ($iconRel): ?>
                                <img src="<?= htmlspecialchars($iconRel, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php else: ?>
                                <div class="tribute-chooser-fallback">🕊</div>
                            <?php endif; ?>
                        </div>

                        <div class="tribute-chooser-content">
                            <h5><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <p><?= htmlspecialchars($t['subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="tribute-empty">
                No tribute types are configured yet. Add records to the <code>tribute_types</code> table.
            </div>
        <?php endif; ?>

        <div class="tribute-modal-actions">
            <button type="button" class="tribute-btn tribute-btn-light" data-close-tribute="#tributeChooserModal">Close</button>
        </div>
    </div>
</div>

<!-- Tribute iframe popup -->
<div id="tributeFrameOverlay" class="tribute-frame-backdrop" aria-modal="true">
    <div class="tribute-frame-shell">
        <button type="button" class="tribute-frame-close" id="tributeFrameClose" aria-label="Close">×</button>
        <iframe id="tributeFrame" title="Post a Tribute"></iframe>
    </div>
</div>

<script>
window.TRIBUTE_POPUP_CONFIG = {
    postId: <?= json_encode($postId, JSON_UNESCAPED_SLASHES) ?>,
    basePath: 'tributes/'
};
</script>
<script src="script/tributes-popup.js"></script>