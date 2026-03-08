<?php
/*
 * Logout script
 */

session_start();

// Destroy session
session_destroy();

// Redirect to home page
header('Location: ../../public/index.php');
exit;
?>
