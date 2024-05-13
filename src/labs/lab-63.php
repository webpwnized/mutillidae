<?php
$lLabNumber = 63;
$lTitle = "Lab 63: Software Composition Analysis - OWASP Dependency Check";
$lQuestion = "Referring to the Summary section of the OWASP Dependency Check report that shows an additional vulnerable jQuery package that was not flagged by Retire.js, why is OWASP Dependency Check potentially able to find more issues than Retire.js?";
$lChoice_1 = "OWASP Dependency Check scans all the application source code. Retire.js only has access to the packages loaded by the client.";
$lChoice_2 = "Retire.js is not good at its intended job";
$lChoice_3 = "OWASP Dependency Check taps into social media to crowd source vulnerabilities";
$lChoice_4 = "Retire.js cannot detect issues in jQuery libraries used in web pages";
$lChoice_5 = "OWASP Dependency Check is a direct descendant of Chuck Norris, so is clairvoyant";
$lCorrectAnswer = 1;

require_once("labs/lab-template.inc");
?>
