<?php
ini_set('display_errors', 0); // Hide errors in production JSON
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/home_helpers.php';

try {
    $sid = ensure_sid();

    $postId = (int)($_POST['post_id'] ?? 0);
    $reaction = trim((string)($_POST['reaction'] ?? ''));

    if ($postId <= 0 || $reaction === '') {
        throw new Exception('Invalid request data');
    }

    $allowedReactions = ['pray', 'wow', 'sad', 'heart', 'flower', 'candle'];
    if (!in_array($reaction, $allowedReactions, true)) {
        throw new Exception('Invalid reaction type');
    }

    $db = db();

    // Check existing
    $chkSt = $db->prepare("SELECT reaction FROM post_reactions WHERE post_id = :pid AND session_id = :sid LIMIT 1");
    $chkSt->execute([':pid' => $postId, ':sid' => $sid]);
    $existing = $chkSt->fetchColumn();

    if ($existing) {
        if ($existing === $reaction) {
            // Toggle OFF
            $delSt = $db->prepare("DELETE FROM post_reactions WHERE post_id = :pid AND session_id = :sid");
            $delSt->execute([':pid' => $postId, ':sid' => $sid]);
            $myReaction = '';
        } else {
            // Update to new reaction
            $updSt = $db->prepare("UPDATE post_reactions SET reaction = :r WHERE post_id = :pid AND session_id = :sid");
            $updSt->execute([':r' => $reaction, ':pid' => $postId, ':sid' => $sid]);
            $myReaction = $reaction;
        }
    } else {
        // Insert new
        $insSt = $db->prepare("INSERT INTO post_reactions (post_id, session_id, reaction) VALUES (:pid, :sid, :r)");
        $insSt->execute([':pid' => $postId, ':sid' => $sid, ':r' => $reaction]);
        $myReaction = $reaction;
    }

    // Get new total
    $cntSt = $db->prepare("SELECT COUNT(*) FROM post_reactions WHERE post_id = :pid");
    $cntSt->execute([':pid' => $postId]);
    $total = (int)$cntSt->fetchColumn();

    json_response([
        'ok'           => true,
        'my_reaction'  => $myReaction,
        'react_total'  => $total
    ]);

} catch (Throwable $e) {
    json_response([
        'ok'      => false,
        'message' => $e->getMessage()
    ], 400);
}
