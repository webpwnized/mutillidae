<?php
$fname = basename($_FILES["file"]["name"]);  // sanitized
move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/uploads/" . $fname);
?>
