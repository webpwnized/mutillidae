<?php
	/*
	 * If you are trying to cause path relative stylesheet
	 * injection, a test case is
	 *
	 * http://172.16.0.130/mutillidae/index.php?page=styling-frame.php&page-to-frame=styling.php/foo/bar/%0A{}*{color:red;}///
	 * This works in IE 11 if the browser is in compatibility mode
	 */

	try{
		$Encoder = null;

		if (session_status() == PHP_SESSION_NONE){
		    session_start();
		}// end if

		if (!isset($_SESSION["security-level"])){
		    $_SESSION["security-level"] = 0;
		}// end if

    	switch ($_SESSION["security-level"]){
			default: // Default case: This code is insecure
    		case "0": // This code is insecure
    		case "1": // This code is insecure
				$lProtectAgainstMethodTampering = false;
				$lEncodeOutput = false;
			break;

			case "2":
			case "3":
			case "4":
    		case "5": // This code is fairly secure
				require_once './includes/constants.php';
				require_once __SITE_ROOT__.'/classes/EncodingHandler.php';
    			$Encoder = new EncodingHandler();
    			$lProtectAgainstMethodTampering = true;
				$lEncodeOutput = true;
			break;
    	};//end switch

    	$lParameterSubmitted = false;
		if (isset($_REQUEST["page-title"])) {
			$lParameterSubmitted = true;
		}// end if

		$lPageTitle = "Styling with Mutillidae";
		if ($lParameterSubmitted){
	    	if ($lProtectAgainstMethodTampering) {
				$lPageTitle = $_GET["page-title"];
	    	}else{
				$lPageTitle = $_REQUEST["page-title"];
	    	};// end if $lProtectAgainstMethodTampering

	    	if($lEncodeOutput){
	    		$lPageTitle = $Encoder->encodeForHTML($lPageTitle);
	    	};// end if
		};// end if $lFormSubmitted

   	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
   	};// end try;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en" xml:lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
	<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
	<title><?php echo $lPageTitle?></title>
</head>
<body>
	<table>
		<tr><td>&nbsp;</td></tr>
		<tr><td><div class="page-title"><?php echo $lPageTitle?></div></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td class="form-header">
				I've been framed!
			</td>
		</tr>
		<tr>
			<td>
				I've been framed by <?php echo $_SERVER['PHP_SELF']; ?>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	</table>
</body>
</html>