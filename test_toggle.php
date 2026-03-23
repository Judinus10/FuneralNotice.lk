<?php
$_POST['post_id'] = 1;
$_POST['reaction'] = 'pray';
$_COOKIE['rn_sid'] = 'test_session_id';

echo "1st Click:\n";
ob_start();
include 'api/post_reaction_toggle.php';
echo ob_get_clean();

echo "\n2nd Click (Toggle Off):\n";
ob_start();
include 'api/post_reaction_toggle.php';
echo ob_get_clean();
