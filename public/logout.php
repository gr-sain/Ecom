<?php
require_once '../config/session.php';
require_once '../config/config.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: ' . SITE_URL . '/public/login.php');
exit();
?>