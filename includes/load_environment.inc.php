<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: text/html; charset=utf-8");

if (!defined('MET_ENV')) {
    define('MET_ENV', 'LOCAL'); // LOCAL, PRODUCTION
}

// Allow ?test=1 or ?test=0 on any page to set test mode for the session
// This switches Stripe to test mode (Verifacti mode is controlled by API key in config)
if (isset($_GET['test'])) {
    if ($_GET['test'] == '0') {
        unset($_SESSION['test']);
    } else {
        $_SESSION['test'] = $_GET['test'];
    }
}

