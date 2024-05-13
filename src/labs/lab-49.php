<?php
$lLabNumber = 49;
$lTitle = "Lab 49: Logging - Log Disclosure";
$lQuestion = "What type of information is found in /var/log/apache2/mutillidae-access.log?";
$lChoice_1 = "The log file does not exist";
$lChoice_2 = "A list of users with access to the database";
$lChoice_3 = "Errors caused by the user";
$lChoice_4 = "The web pages that users have accessed organized by client IP address";
$lChoice_5 = "PHP source code";
$lCorrectAnswer = 4;

require_once("labs/lab-template.inc");
?>