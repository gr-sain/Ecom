<?php
// Site configuration
define('SITE_NAME', 'E-Commerce Store');
define('SITE_URL', 'http://localhost/E_Commrece_pro');
define('CURRENCY', '$');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database class
require_once __DIR__ . '/../classes/Database.php';
?>