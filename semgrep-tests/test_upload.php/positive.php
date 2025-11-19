<?php
$fname = $_FILES["file"]["name"];
move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/uploads/" . $fname);
?>
