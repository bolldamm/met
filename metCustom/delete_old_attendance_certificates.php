<?php
/*
Delete workshop and conference attendance certificates
*/

	require "../includes/delete_old_files.php";

// Set five-year period
$age = 3600*24*365*5;

// Set directory
$dir = "/home/metmeetings/www/www/files/customers/workshops";

// Delete workshop attendance certificates older than 3 years
$deleted = delete_older_than($dir, $age);

$successTxt = "Deleted " . count($deleted) . " workshop attendance certificates:<br>" .
    implode("<br>", $deleted);

if ($deleted!=""){
echo $successTxt."<br>";
} else {
echo "No workshop attendance certificates were deleted.<br>";
}

// Set directory
$dir = "/home/metmeetings/www/www/files/customers/conferences";

// Delete conference attendance certificates older than 3 years
$deleted = delete_older_than($dir, $age);

$successTxt = "Deleted " . count($deleted) . " conference attendance certificates:<br>" .
    implode("<br>", $deleted);

if ($deleted!=""){
echo $successTxt;
} else {
echo "No conference attendance certificates were deleted.";
}
echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>