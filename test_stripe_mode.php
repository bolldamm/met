<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  require "includes/load_main_components.inc.php";

  if (isset($_GET['test'])) {
      $_SESSION['test'] = $_GET['test'];
  }

  echo "SESSION test: " . (isset($_SESSION['test']) ? $_SESSION['test'] : 'NOT SET') . "<br>";
  echo "MET_ENV: " . (defined('MET_ENV') ? MET_ENV : 'NOT DEFINED') . "<br>";

  $keys = require('/home/metmeetings/private/stripe_keys.php');

  echo "Using key starting with: " . substr($keys['publishable_key'], 0, 12) . "...<br>";
  echo "Mode: " . (strpos($keys['publishable_key'], 'pk_test_') === 0 ? 'TEST' : 'LIVE');

