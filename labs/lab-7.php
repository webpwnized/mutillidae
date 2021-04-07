    <?php
    $lLabNumber = 7;
    $lTitle = "Lab 7: SQL Injection - Using SQLi to Bypass Authentication";
    $lQuestion = "Assuming the Mutillidae II system contains a username Jeremy and the system is vulnerable, which of the following SQL injections in the username field of the login page would bypass authentication to login  user Jeremy?";
    $lChoice_1 = "jeremy'  or %44% --";
    $lChoice_2 = "jeremy\" or 44=44 --";
    $lChoice_3 = "jeremy' and 44=43 #";
    $lChoice_4 = "jeremy' #";
    $lChoice_5 = "jeremy' not 44=44 #";
    $lCorrectAnswer = 4;

    require_once("labs/lab-template.inc");
?>