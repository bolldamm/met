<?php
/*
Delete old invoices
*/

	require "../includes/delete_old_files.php";

// Set five-year period
$age = 3600*24*365*5;

// Set directory
$dir = "/home/metmeetings/www/www/files/customers/invoice/pdf";

// Delete invoices older than 5 years
$deleted = delete_older_than($dir, $age);

$successTxt = "Deleted " . count($deleted) . " invoices:<br>" .
    implode("<br>", $deleted);

if ($deleted!=""){
echo $successTxt."<br>";
} else {
echo "No invoices were deleted.<br>";
}

echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>