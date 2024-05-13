$(function() {
	var l_message = "";
	var l_query_string = window.location.search;
	const l_url_params = new URLSearchParams(l_query_string);
	const l_status_code = l_url_params.get('popUpNotificationCode');
	
	if (l_status_code){
		switch (l_status_code){
			case "HPH0": l_message = "Feeling Lost? Toggle Hints or Popup Hints to activate dynamic help systems.";break;
			case "L1H0": l_message = "Hints Disabled";break;
			case "L1H1": l_message = 'Hints enabled. Please find a "Hints and Videos" section added to the top of each applicable page.';break;
			case "SUD0": l_message = "Dropping and rebuilding database";break;
			case "SUD1": l_message = "Database has been rebuilt";break;
			case "AU1": l_message = "User Authenticated";break;
			case "LOU1": l_message = "User Logged Out";break;
			case "SSLE1": l_message = "SSL Enforced";break;
			case "SSLO1": l_message = "SSL Optional";break;
			case "SL0": l_message = "Security level set to 0. Hack Away.";break;
			case "SL1": l_message = "Security level set to 1. Try Slightly Harder.";break;
			case "SL5": l_message = "Security level set to 5. Good Luck.";break;
			case "BHD1": l_message = "Bubble Hints Disabled";break;
			case "BHE1": l_message = "Bubble Hints Enabled";break;
			case "LFD1": l_message = "Logs Deleted";break;
			case "LFR1": l_message = "Logs Refreshed";break;
		}// end switch
	
		$.gritter.add({
		    title: 'Status Update',
		    text: l_message,
		    time: 5000
		});
	}// end if l_status_code
});

