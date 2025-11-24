#!/usr/local/bin/php.cli
<?php

$taskScript  = '/home/metmeetings/private/auto_integrity_task.php';

// Run the task by requiring it
if (file_exists($taskScript)) {
    require $taskScript;
    if ($SendMessageFlag) {
        mail($emailTo, $subject, $message, "From: {$emailFrom}\r\n");
    }
}

?>