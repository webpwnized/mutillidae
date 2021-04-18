<?php
    $lLabNumber = 15;
    $lTitle = "Lab 15: Command injection - Reverse Meterpreter Shell with Command Injection";
    $lQuestion = "In a Meterpreter shell, the getuid command show the user that the exploit is running under. Establish a Meterpreter shell, then run the getuid command. What user is the exploit is running under?";
    $lChoice_1 = "apache";
    $lChoice_2 = "php-sys";
    $lChoice_3 = "apache2";
    $lChoice_4 = "www-data";
    $lChoice_5 = "mutillidae";
    $lCorrectAnswer = 4;

    require_once("labs/lab-template.inc");
?>