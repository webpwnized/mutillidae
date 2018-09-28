<?php 
	switch ($_GET["popUpNotificationCode"]){
		case "HPH0": $lhintsPopUpNotificationMessage = "<b>Feeling Lost?</b> Toggle <b><u>Hints</u></b> or <b><u>Popup Hints</u></b> to activate dynamic help systems.";break;
		case "L1H0": $lhintsPopUpNotificationMessage = "Hints Disabled";break;
		case "L1H1": $lhintsPopUpNotificationMessage = "Hints enabled. Please find a \"Hints and Videos\" section added to the top of each applicable page.";break;
		case "SUD0": $lhintsPopUpNotificationMessage = "Dropping and rebuilding database";break;
		case "SUD1": $lhintsPopUpNotificationMessage = "Database has been rebuilt";break;
		case "AU1": $lhintsPopUpNotificationMessage = "User Authenticated";break;
		case "LOU1": $lhintsPopUpNotificationMessage = "User Logged Out";break;
		case "SSLE1": $lhintsPopUpNotificationMessage = "SSL Enforced";break;
		case "SSLO1": $lhintsPopUpNotificationMessage = "SSL Optional";break;
		case "SL0": $lhintsPopUpNotificationMessage = "Security level set to 0. Hack Away.";break;
		case "SL1": $lhintsPopUpNotificationMessage = "Security level set to 1. Try Slightly Harder.";break;
		case "SL5": $lhintsPopUpNotificationMessage = "Security level set to 5. Good Luck.";break;
		case "BHD1": $lhintsPopUpNotificationMessage = "Bubble Hints Disabled";break;
		case "BHE1": $lhintsPopUpNotificationMessage = "Bubble Hints Enabled";break;
		case "LFD1": $lhintsPopUpNotificationMessage = "Logs Deleted";break;
		case "LFR1": $lhintsPopUpNotificationMessage = "Logs Refreshed";break;
	}// end switch
?>

<link rel="stylesheet" type="text/css" href="styles/gritter/jquery.gritter.css" />
<script type="text/javascript" src="javascript/gritter/jquery.gritter.min.js"></script>
<script>
$.gritter.add({
    // (string | mandatory) the heading of the notification
    title: 'Status Update',
    // (string | mandatory) the text inside the notification
    text: '<?php echo $lhintsPopUpNotificationMessage ?>',
    // time until fade begins
    time: 5000
});
</script>