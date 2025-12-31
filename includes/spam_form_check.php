<?php
// 1. See: includes/spam_form_init.php
//
// 2. Place this line before the processing part of the form processing php file:
// require "includes/spam_form_check.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= CONFIG ================= */
$SPAM_MIN_TIME    = 3;        // seconds
$SPAM_MAX_SUBMITS = 2;        // per window
$SPAM_WINDOW      = 3600;     // seconds, 3600 = 1 hour
/* ========================================== */

function spam_fail(int $code = 403, string $msg = ''): void {
//    http_response_code($code);
//    if ($msg !== '') {
//        echo $msg;
//    }
//    exit;

    echo 3;
    $GLOBALS['valid'] = false;

}

/* -------- 1. Session + token -------- */
if (
    empty($_SESSION['spam_form_token']) ||
    empty($_POST['spam_form_token']) ||
    !hash_equals($_SESSION['spam_form_token'], $_POST['spam_form_token'])
) {
    spam_fail(403, 'Invalid submission.');
}

/* -------- 2. Timing check -------- */
$elapsed = time() - ($_SESSION['spam_form_time'] ?? 0);
if ($elapsed < $SPAM_MIN_TIME) {
    spam_fail(403);
}

/* -------- 3. Honeypot -------- */
if (!empty($_POST['company_website'] ?? '')) {
    // Silent discard
    exit;
}

/* -------- 4. Rate limiting -------- */
$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$key = 'spam_rate_' . md5($ip);

$_SESSION[$key] ??= [];

// Drop expired timestamps
$_SESSION[$key] = array_filter(
    $_SESSION[$key],
    fn($t) => $t > time() - $SPAM_WINDOW
);

if (count($_SESSION[$key]) >= $SPAM_MAX_SUBMITS) {
    spam_fail(429, 'Too many submissions.');
}

$_SESSION[$key][] = time();

/* -------- 5. One-time token -------- */
unset($_SESSION['spam_form_token'], $_SESSION['spam_form_time']);
