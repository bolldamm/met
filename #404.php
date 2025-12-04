<?php
http_response_code(404);  

// Path to service files
$htaccess_file = '/home/metmeetings/private/.htaccess_banned_ips';
$log_file = '/home/metmeetings/private/blocked_ips.txt';

// Get visitor IP
$ip = $_SERVER['REMOTE_ADDR'];
$requested = $_SERVER['REQUEST_URI'];
$time = date('Y-m-d H:i:s');

// Define whitelist
$whitelist = ['favicon.ico', 'pagines/Other%20pages', 'files/metm_files', '/en/'];

foreach ($whitelist as $allowed) {
    if (stripos($requested, $allowed) !== false) {
        include 'error404.php';
        exit;
    }
}

// Log the attempt
file_put_contents($log_file, "[$time] $ip tried $requested\n", FILE_APPEND);

// Check if IP is already in the list
$ban_list = file_exists($htaccess_file) ? file($htaccess_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

if (!in_array($ip, $ban_list)) {
    // Append to the list
    file_put_contents($htaccess_file, "Deny from $ip\n", FILE_APPEND);
}

// Immediately block access
include 'error404.php';
exit;
?>