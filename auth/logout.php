<?php
/**
 * Logout Handler
 */
require_once '../includes/auth.php';

logoutUser();
header('Location: /PC/index.php');
exit();
?>
