<?php
$postId = 1;
$reaction = 'pray';
$sid = 'fresh_session_' . uniqid();

$_POST['post_id'] = $postId;
$_POST['reaction'] = $reaction;
$_COOKIE['rn_sid'] = $sid;

echo "1st Click (Fresh Session):\n";
ob_start();
include 'api/post_reaction_toggle.php';
echo ob_get_clean();

$_COOKIE['rn_sid'] = $sid; // Re-set cookie for toggle
echo "\n2nd Click (Toggle Off):\n";
ob_start();
include 'api/post_reaction_toggle.php';
echo ob_get_clean();
