<?php
    $lLabNumber = 29;
    $lTitle = "Lab 29: Cross-site Request Forgery - Voting for NMap";
    $lQuestion = "Does the Cross-site Request Forgery (CSRF) attack on the user-poll.php page still work if the request method is changed to 'POST'?";
    $lChoice_1 = "Yes";
    $lChoice_2 = "No";
    $lChoice_3 = "It depends on the timing of the attack";
    $lChoice_4 = "It depends on whether HTTP or HTTPS is used";
    $lChoice_5 = "There is no way to tell";
    $lCorrectAnswer = 1;

    require_once("labs/lab-template.inc");
?>