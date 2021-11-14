<?php
    $lLabNumber = 25;
    $lTitle = "Lab 25: Cross-Site Scripting - Creating a Cross-site Script Proof of Concept (PoC)";
    $lQuestion = "In the particular cross-site script used in the lab, what is the purpose of the script element? &lt;script&gt;alert('XSS');&lt;/script&gt;";
    $lChoice_1 = "The script element provides the padding needed to offset the injection";
    $lChoice_2 = "The element restarts the browser engine";
    $lChoice_3 = "The element initializes the cross-site scripting filters";
    $lChoice_4 = "This injection happens to land within HTML. The script element tells the browser to pause processing HTML and execute the JavaScript within";
    $lChoice_5 = "The element refreshes the page";
    $lCorrectAnswer = 4;

    require_once("labs/lab-template.inc");
?>