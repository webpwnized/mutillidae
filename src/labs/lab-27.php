<?php
    $lLabNumber = 27;
    $lTitle = "Lab 27: Cross-Site Scripting - Bypassing Client-side Defenses";
    $lQuestion = "In the lab, the JavaScript form validation is disabled by deleting the onsubmit event. Which of the following happens when the event is deleted?";
    $lChoice_1 = "Nothing. More steps are needed to disable the form validation.";
    $lChoice_2 = "The browser window refreshes";
    $lChoice_3 = "The JavaScript form validation is disabled immediately";
    $lChoice_4 = "The browser has to be refreshed to commit the changes";
    $lChoice_5 = "The changes take effect the next time the browser starts";
    $lCorrectAnswer = 3;

    require_once("labs/lab-template.inc");
?>