<?php
/*
Delete all conference profile image files
*/

$files = glob('/home/metmeetings/www/www/files/METM_attendees/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
    // echo $file . "<br>";
  }
}

echo "Success!<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>