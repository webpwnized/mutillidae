<?php
	if($_SESSION['loggedin'] == "True"){

		switch ($_SESSION["security-level"]){
	   		case "0": // This code is insecure
				$logged_in_user = $_SESSION['logged_in_user'];
				$logged_in_usersignature = $_SESSION['logged_in_usersignature'];
				$lSecurityLevelDescription = "Hosed";
	   		break;
	   		case "1": // This code is insecure
	   			// DO NOTHING: This is equivalent to using client side security		
				$logged_in_user = $_SESSION['logged_in_user'];
				$logged_in_usersignature = $_SESSION['logged_in_usersignature'];
				$lSecurityLevelDescription = "Arrogent";
	   		break;
		    
	   		case "2":
	   		case "3":
	   		case "4":
	   		case "5": // This code is fairly secure
	  			/* 
	  			 * NOTE: Input validation is excellent but not enough. The output must be
	  			 * encoded per context. For example, if output is placed in HTML,
	  			 * then HTML encode it. Blacklisting is a losing proposition. You 
	  			 * cannot blacklist everything. The business requirements will usually
	  			 * require allowing dangerous charaters. In the example here, we can 
	  			 * validate username but we have to allow special characters in passwords
	  			 * least we force weak passwords. We cannot validate the signature hardly 
	  			 * at all. The business requirements for text fields will demand most
	  			 * characters. Output encoding is the answer. Validate what you can, encode it
	  			 * all.
	  			 */
	   			// encode the entire message following OWASP standards
	   			// this is HTML encoding because we are outputting data into HTML
				$logged_in_user = $Encoder->encodeForHTML($_SESSION['logged_in_user']);
				$logged_in_usersignature = $Encoder->encodeForHTML($_SESSION['logged_in_usersignature']);
				$lSecurityLevelDescription = "Secure";
			break;
	   	}// end switch		

	   	$lUserAuthorizationLevelText = 'User';
	   	if ($_SESSION['is_admin'] == 'TRUE'){
	   		$lUserAuthorizationLevelText = 'Admin';
	   	}// end if

		$lAuthenticationStatusMessage = 
				'Logged In ' . 
				$lUserAuthorizationLevelText . ": " . 
				'<span style="color:#990000;font-weight:bold;">'.$logged_in_user . "</span> (" . 
				$logged_in_usersignature . ")";
	} else {
		$logged_in_user = "anonymous";
		$lAuthenticationStatusMessage = "Not Logged In";
	}// end if($_SESSION['loggedin'] == "True")

	if ($_SESSION["EnforceSSL"] == "True"){
		$lEnforceSSLLabel = "Drop SSL";
	}else {
		$lEnforceSSLLabel = "Enforce SSL";
	}//end if

	if ($BubbleHintHandler->hintsAreDispayed() == 1){
		$lPopupHintsLabel = "Hide Popup Hints";
	}else {
		$lPopupHintsLabel = "Show Popup Hints";
	}//end if

	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
			$lSecurityLevelDescription = "Hosed";
   		break;
   		case "1": // This code is insecure
   			// DO NOTHING: This is equivalent to using client side security		
			$lSecurityLevelDescription = "Client-side Security";
   		break;
	    
   		case "2":
   		case "3":
   		case "4":
   		case "5": // This code is fairly secure
			$lSecurityLevelDescription = "Server-side Security";
		break;
   	}// end switch		
	
	$lHintsMessage = "Hints: ".$_SESSION["hints-enabled"];
	$lSecurityLevelMessage = "Security Level: ".$_SESSION["security-level"]." (".$lSecurityLevelDescription.")";

	try{
   		$lReflectedXSSExecutionPointBallonTip = $BubbleHintHandler->getHint("ReflectedXSSExecutionPoint");
   		$lCookieTamperingAffectedAreaBallonTip = $BubbleHintHandler->getHint("CookieTamperingAffectedArea"); 
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error attempting to execute query to fetch bubble hints.");
	}// end try	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon" />	
	<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
	<link rel="stylesheet" type="text/css" href="./styles/ddsmoothmenu/ddsmoothmenu.css" />
	<link rel="stylesheet" type="text/css" href="./styles/ddsmoothmenu/ddsmoothmenu-v.css" />

	<script type="text/javascript" src="./javascript/bookmark-site.js"></script>
	<script type="text/javascript" src="./javascript/ddsmoothmenu/ddsmoothmenu.js"></script>
	<script type="text/javascript" src="./javascript/ddsmoothmenu/jquery.min.js">
		/***********************************************
		* Smooth Navigational Menu- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
		* This notice MUST stay intact for legal use
		* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
		***********************************************/
	</script>
	<script type="text/javascript">
		ddsmoothmenu.init({
			mainmenuid: "smoothmenu1", //menu DIV id
			orientation: 'v', //Horizontal or vertical menu: Set to "h" or "v"
			classname: 'ddsmoothmenu', //class added to menu's outer DIV
			//customtheme: ["#cccc44", "#cccccc"],
			contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
		});
	</script>
	<script type="text/javascript">
		$(function() {
			$('[ReflectedXSSExecutionPoint]').attr("title", "<?php echo $lReflectedXSSExecutionPointBallonTip; ?>");
			$('[ReflectedXSSExecutionPoint]').balloon();
			$('[CookieTamperingAffectedArea]').attr("title", "<?php echo $lCookieTamperingAffectedAreaBallonTip; ?>");
			$('[CookieTamperingAffectedArea]').balloon();
		});
	</script>
</head>
<body onload="onLoadOfBody(this);">
<table class="main-table-frame" border="1px" cellspacing="0px" cellpadding="0px">
	<tr>
		<td bgcolor="#ccccff" align="center" colspan="7">
			<table width="100%">
				<tr>
					<td style="text-align:center;">
						<span style="text-align:center; font-weight: bold; font-size:30px; text-align: center;">
						<img style="vertical-align: middle; margin-right: 10px;" border="0px" align="top" src="images/coykillericon-50-38.png"/>
							OWASP Mutillidae II: Keep Calm and Pwn On
						</span>
					</td>
				</tr>		
			</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#ccccff" align="center" colspan="7">
			<?php /* Note: $C_VERSION_STRING in index.php */ ?>
			<span class="version-header"><?php echo $C_VERSION_STRING;?></span>
			<span id="idSecurityLevelHeading" class="version-header" style="margin-left: 20px;"><?php echo $lSecurityLevelMessage; ?></span>
			<span id="idHintsStatusHeading" CookieTamperingAffectedArea="1" class="version-header" style="margin-left: 20px;"><?php echo $lHintsMessage; ?></span>
			<span id="idSystemInformationHeading" ReflectedXSSExecutionPoint="1" class="version-header" style="margin-left: 20px;"><?php echo $lAuthenticationStatusMessage ?></span>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="header-menu-table">
			<table class="header-menu-table">
				<tr>
					<td><a href="index.php?page=home.php&popUpNotificationCode=HPH0">Home</a></td>
					<td>|</td>
					<td>
						<?php
							if ($_SESSION['loggedin'] == 'True'){
								echo '<a href="index.php?do=logout">Logout</a>';
							} else {
								echo '<a href="index.php?page=login.php">Login/Register</a>';
							}// end if
						?>		
					</td>
					<td>|</td>
					<?php 
						if ($_SESSION['security-level'] == 0){
							echo '<td><a href="index.php?do=toggle-hints&page='.$lPage.'">Toggle Hints</a></td><td>|</td>';
						}// end if
					?>
					<td><a href="index.php?do=toggle-bubble-hints&page=<?php echo $lPage?>"><?php echo $lPopupHintsLabel; ?></a></td>
					<td>|</td>
					<td><a href="index.php?do=toggle-security&page=<?php echo $lPage?>">Toggle Security</a></td>
					<td>|</td>
					<td><a href="index.php?do=toggle-enforce-ssl&page=<?php echo $lPage?>"><?php echo $lEnforceSSLLabel; ?></a></td>
					<td>|</td>
					<td><a href="set-up-database.php">Reset DB</a></td>
					<td>|</td>
					<td><a href="index.php?page=show-log.php">View Log</a></td>
					<td>|</td>
					<td><a href="index.php?page=captured-data.php">View Captured Data</a></td>
				</tr>
			</table>	
		</td>
	</tr>
	<tr>
		<td style="vertical-align:top;text-align:left;background-color:#ccccff;width:125pt;">
			<?php require_once 'main-menu.php'; ?>
			<div>&nbsp;</div>
			<div class="label" style="text-align: center;">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="45R3YEXENU97S">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				<span style="color: blue;">Want to Help?</span>
			</div>
			<div>&nbsp;</div>
			<div class="label" style="text-align: center;">
				<a href="http://www.youtube.com/user/webpwnized" style="white-space:nowrap;" target="_blank">
					<img align="middle" alt="Webpwnized YouTube Channel" src="./images/youtube-48-48.png" />
					<br/>
					Video Tutorials
				</a>
			</div>
			<div>&nbsp;</div>
			<div class="label" style="text-align: center;">
				<a href="https://twitter.com/webpwnized" target="_blank">
					<img align="middle" alt="Webpwnized Twitter Channel" src="./images/twitter-bird-48-48.png" />
					<br/>
					Announcements
				</a>
			</div>		
			<div>&nbsp;</div>
			<div class="label" style="text-align: center;">
				<a 
					href="https://www.sans.org/reading-room/whitepapers/application/introduction-owasp-mutillidae-ii-web-pen-test-training-environment-34380" 
					target="_blank"
					title="Whitepaper: Introduction to OWASP Mutillidae II Web Pen Test Training Environment"
				>			
					<img align="middle" alt="Webpwnized Twitter Channel" src="./images/pdf-icon-48-48.png" />
					<br/>
					Getting Started
				</a>
			</div>
			<div>&nbsp;</div>
		</td>
		<td valign="top">
			<blockquote>
			<!-- Begin Content -->
