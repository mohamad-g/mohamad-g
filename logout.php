<?php
// Include necessary files
require_once '../backend/config/config.php';
require_once '../backend/config/database.php';
require_once '../backend/includes/auth.php';

// Process logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    logout();
    header('Location: login.php?logout=1');
    exit;
}

// Redirect to admin dashboard if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
