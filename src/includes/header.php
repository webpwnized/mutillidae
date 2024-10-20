<?php
    $lSecurityLevel = $_SESSION["security-level"];
	$lSecurityLevelMessage = "Security Level: ".$lSecurityLevel;

    switch ($lSecurityLevel){
		default: // Default case: This code is insecure
        case "0": // This code is insecure
            $lSecurityLevelMessage = $lSecurityLevelMessage." (Hosed)";
            break;
        case "1": // This code is insecure
            // DO NOTHING: This is equivalent to using client side security
            $lSecurityLevelMessage = $lSecurityLevelMessage." (Client-Side Security)";
            break;

        case "2":
        case "3":
        case "4":
        case "5": // This code is fairly secure
            $lSecurityLevelMessage = $lSecurityLevelMessage." (Secure)";
            break;
    }// end switch

	if(isset($_SESSION["user_is_logged_in"]) && $_SESSION["user_is_logged_in"]){

	    switch ($lSecurityLevel){
			default: // Default case: This code is insecure
			case "0": // This code is insecure
	   		case "1": // This code is insecure
	   			// DO NOTHING: This is equivalent to using client side security
				$logged_in_user = $_SESSION["logged_in_user"];
			break;

	   		case "2":
	   		case "3":
	   		case "4":
	   		case "5": // This code is fairly secure
	   			// encode the entire message following OWASP standards
	   			// this is HTML encoding because we are outputting data into HTML
				$logged_in_user = $Encoder->encodeForHTML($_SESSION["logged_in_user"]);
			break;
	   	}// end switch

	   	$lUserID = $_SESSION["uid"];

	   	$lUserAuthorizationLevelText = 'User';

	   	if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]){
	   		$lUserAuthorizationLevelText = 'Admin';
	   	}// end if

		$lAuthenticationStatusMessage =
			'Logged In ' .
			$lUserAuthorizationLevelText . ": " .
			'<span class="logged-in-user">'.$logged_in_user.'</span>'.
			'<a href="index.php?page=view-account-profile.php&uid='.$lUserID.'">'.
            '<img class="icon" src="images/view-icon-20-20.png" /></a>' .
			'<a href="index.php?page=edit-account-profile.php&uid='.$lUserID.'">'.
            '<img class="icon" src="images/edit-icon-20-20.png" /></a>';
	} else {
		$logged_in_user = "anonymous";
		$lAuthenticationStatusMessage = "Not Logged In";
	}// end if

	if ($_SESSION["EnforceSSL"] == "True"){
		$lEnforceSSLLabel = "Drop TLS";
	}else {
		$lEnforceSSLLabel = "Enforce TLS";
	}//end if

	$lHintsMessage = "Hints: ".$_SESSION["hints-enabled"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

	<link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon" />

	<link rel="stylesheet" type="text/css" href="styles/global-styles.css" />
	<link rel="stylesheet" type="text/css" href="styles/ddsmoothmenu/ddsmoothmenu.css" />
	<link rel="stylesheet" type="text/css" href="javascript/jQuery/colorbox/colorbox.css" />
	<link rel="stylesheet" type="text/css" href="styles/gritter/jquery.gritter.css" />

	<script src="javascript/jQuery/jquery.js"></script>
	<script src="javascript/jQuery/colorbox/jquery.colorbox-min.js"></script>
	<script src="javascript/ddsmoothmenu/ddsmoothmenu.js"></script>
	<script src="javascript/gritter/jquery.gritter.min.js"></script>
	<script src="javascript/hints/hints-menu.js"></script>
	<script src="javascript/inline-initializers/jquery-init.js"></script>
	<script src="javascript/inline-initializers/ddsmoothmenu-init.js"></script>
	<script src="javascript/inline-initializers/populate-web-storage.js"></script>
	<script src="javascript/inline-initializers/gritter-init.js"></script>
	<script src="javascript/inline-initializers/hints-menu-init.js"></script>
</head>
<body>
<table class="main-table-frame">
	<tr class="main-table-frame-dark">
		<td class="main-table-frame-first-bar" colspan="2">
			<img src="images/coykillericon-50-38.png" alt="Coykiller Icon"/>
			OWASP Mutillidae II: Keep Calm and Pwn On
		</td>
	</tr>
	<tr class="main-table-frame-dark">
		<td class="main-table-frame-second-bar" colspan="2">
			<?php /* Note: $C_VERSION_STRING in index.php */
			    echo $C_VERSION_STRING;
			?>
			<span><?php echo $lSecurityLevelMessage; ?></span>
			<span><?php echo $lHintsMessage; ?></span>
			<span><?php echo $lAuthenticationStatusMessage ?></span>
		</td>
	</tr>
	<tr class="main-table-frame-menu-bar">
		<td class="main-table-frame-menu-bar" colspan="2">
			<a href="index.php?page=home.php&popUpNotificationCode=HPH0">Home</a>
			|
			<?php
				if (isset($_SESSION["user_is_logged_in"]) && $_SESSION["user_is_logged_in"]){
					echo '<a href="index.php?do=logout">Logout</a>';
				} else {
					echo '<a href="index.php?page=login.php">Login/Register</a>';
				}// end if
			?>
			|
			<?php
				if ($_SESSION['security-level'] == 0){
					echo '<a href="index.php?do=toggle-hints&page='.$lPage.'">Toggle Hints</a> |';
				}// end if
			?>
			<a href="index.php?do=toggle-security&page=<?php echo $lPage?>">Toggle Security</a>
			|
			<a href="index.php?do=toggle-enforce-ssl&page=<?php echo $lPage?>"><?php echo $lEnforceSSLLabel; ?></a>
			|
			<a href="set-up-database.php">Reset DB</a>
			|
			<a href="index.php?page=show-log.php">View Log</a>
			|
			<a href="index.php?page=captured-data.php">View Captured Data</a>
		</td>
	</tr>
	<tr>
		<td class="main-table-frame-left">
			<?php require_once 'main-menu.php'; ?>
			<div>&nbsp;</div>
			<div>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="45R3YEXENU97S">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="Donate Today!">
					<img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				Want to Help?
			</div>
			<div>&nbsp;</div>
			<div>
				<a href="http://www.youtube.com/user/webpwnized" target="_blank">
					<img alt="Webpwnized YouTube Channel" src="./images/youtube-play-icon-40-40.png" />
					<br/>
					Video Tutorials
				</a>
			</div>
			<div>&nbsp;</div>
			<div>
				<a href="https://twitter.com/webpwnized" target="_blank">
					<img alt="Webpwnized Twitter Channel" src="./images/twitter-bird-48-48.png" />
					<br/>
					Announcements
				</a>
			</div>
			<div>&nbsp;</div>
			<div>
				<a
					href="https://www.sans.org/reading-room/whitepapers/application/introduction-owasp-mutillidae-ii-web-pen-test-training-environment-34380"
					target="_blank"
					title="Whitepaper: Introduction to OWASP Mutillidae II Web Pen Test Training Environment"
				>
					<img alt="Webpwnized Twitter Channel" src="./images/pdf-icon-48-48.png" />
					<br/>
					Getting Started
				</a>
			</div>
			<div>&nbsp;</div>
		</td>
		<td class="main-table-frame-right">
			<!-- Begin Content -->
