<?php
	/**
	 * 
	 * Si no estamos logueado, enviamos al usuario fuera del gestor de manera inmediata.
	 * 
	 */
  // ---- Config ----
$sessionLifetimeHours = 1; // expire after N hours of inactivity
// $sessionLifetimeHours = 1/60; // 1 minute
$sessionLifetime = $sessionLifetimeHours * 3600;

// ---- Sliding expiry logic ----
if (isset($_SESSION['user']['activated'])) {
    // Has the inactivity period exceeded the limit?
    if (time() - $_SESSION['user']['activated'] > $sessionLifetime) {
        // Too long without activity -> expire the session
        unset($_SESSION['user']); 
        // session_unset();
        // session_destroy();
    } else {
        // Still within allowed inactivity -> reset timer
        $_SESSION['user']['activated'] = time();
    }
} else {
    // If no timestamp exists yet, create it (first login or session start)
    $_SESSION['user']['activated'] = time();
}

	if(!isset($_SESSION["user"])){
		if(isset($esAjax)){
			exit();	
		}else{
			generalUtils::redirigir("index.php");
		}
	}
?>