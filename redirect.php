<?php

generalUtils::redirigir("/en/programme:1365#" . substr(substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'],":")), 4));
?>