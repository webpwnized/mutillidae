<?php
    $lLabNumber = 21;
    $lTitle = "Lab 21: Insecure Direct Object Reference - Web Shell with Local File Inclusion";
    $lQuestion = "The pwd command prints the working directory. After establishing a shell connection, run the pwd command. What directory did the exploit land inside of?";
    $lChoice_1 = "/var/log/apache2";
    $lChoice_2 = "/etc/passwd";
    $lChoice_3 = "/var/www/mutillidae";
    $lChoice_4 = "/var/www/html";
    $lChoice_5 = "/usr/share/apache2";
    $lCorrectAnswer = 3;

    require_once("labs/lab-template.inc");
?>