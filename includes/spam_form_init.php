<?php
// 1. Place this line near the top of the form php file:
// require "includes/spam_form_init.php";
//
// 2. Add these lines to the form HTML
//			<div class="form-row">
//			<input type="hidden" name="spam_form_token" value="{SPAM_FORM_TOKEN}">
//			<div style="display:none">
//			    <label>Company website</label>
//			    <input type="text" name="company_website" autocomplete="off">
//			</div>
//			</div>
//
// 3. See: includes/spam_form_check.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create per-form token
$_SESSION['spam_form_token'] = bin2hex(random_bytes(32));
$_SESSION['spam_form_time']  = time();
define("SPAM_FORM_TOKEN", htmlspecialchars($_SESSION['spam_form_token'], ENT_QUOTES));