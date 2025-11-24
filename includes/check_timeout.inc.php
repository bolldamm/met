<?php

      // ---- Config ----
$sessionLifetimeHours = 0; // expire after N hours of inactivity. Set to 0 for permanent session.
// $sessionLifetimeHours = 1/60; // 1 minute
$sessionLifetime = $sessionLifetimeHours * 3600;

// ---- Sliding expiry logic ----
if (isset($_SESSION['met_user']['activated'])) {
    // Only check inactivity if $sessionLifetime is not 0
    if ($sessionLifetime > 0 && (time() - $_SESSION['met_user']['activated'] > $sessionLifetime)) {
        // Too long without activity -> expire the session
        generalUtils::redirigir("logout.php");
        exit;
        // unset($_SESSION["met_user"]);
        // unset($_SESSION["conference_user"]);
        // unset($_SESSION["registration_desk"]);
        // unset($_SESSION["conference_attendee"]);
	// $_SESSION["auth_target"] = 'https://www.metmeetings.org/';
	
	//Redirigimos al login
	// generalUtils::redirigir($_SESSION["auth_target"]);

    } else {
        // Still within allowed inactivity (or permanent session) -> reset timer
        $_SESSION['met_user']['activated'] = time();
    }
} else {
    // Only set 'activated' if $_SESSION['met_user'] exists and has other keys
    if (!empty($_SESSION['met_user']) && count($_SESSION['met_user']) > 0) {
        $_SESSION['met_user']['activated'] = time();
    }
}

?>