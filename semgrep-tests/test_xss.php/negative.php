<?php
$message = $_GET['message'];
$safe = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
echo "<pre>" . $safe . "</pre>";
?>
