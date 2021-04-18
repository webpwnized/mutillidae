<?php
    $lLabNumber = 26;
    $lTitle = "Lab 26: Cross-Site Scripting - Browser Exploitation Framework";
    $lQuestion = "In the Browser Exploitation Framework lab, how does the Browser Exploitation Framework infect the browser?";
    $lChoice_1 = "BeEF logs into the database and poisons fields of datatype varchar";
    $lChoice_2 = "The reflected cross-site script downloads a self-starting script from the BeEF server";
    $lChoice_3 = "The reflected cross-site script infects the user's email";
    $lChoice_4 = "BeEF executes code on the application server that pulls down the infection";
    $lChoice_5 = "BeEF is magic";
    $lCorrectAnswer = 2;

    require_once("labs/lab-template.inc");
?>