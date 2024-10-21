<?php
	/* Command Injection
	 * Method Tampering
	 * Cross Site Scripting
	 * HTML Injection
	 * Server-side Request Forgery (SSRF) */

	try {
    	switch ($_SESSION["security-level"]){
			default: // Default case: This code is insecure
    		case "0": // This code is insecure
    		case "1": // This code is insecure
				$lProtectAgainstMethodTampering = false;
				$lProtectAgainstCommandInjection=false;
				$lProtectAgainstXSS = false;
				$lProtectAgainstSSRF = false;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lProtectAgainstCommandInjection=true;
   				$lProtectAgainstMethodTampering = true;
   				$lProtectAgainstXSS = true;
   				$lProtectAgainstSSRF = true;
    		break;
    	}// end switch

    	$lFormSubmitted = false;
		if (isset($_POST["ServerURL"]) || isset($_REQUEST["ServerURL"])) {
			$lFormSubmitted = true;
		}// end if

		$lDefaultServerURL = "http://".$_SERVER['SERVER_NAME']."/webservices/rest/ws-test-connectivity.php";

		if ($lFormSubmitted){

		    if ($lProtectAgainstCommandInjection) {
		        // We do not accept user input to determine where HTTP request will be sent by the application
		        $lServerURL = $lDefaultServerURL;
		    }else{
		        $lProtectAgainstMethodTampering?$lServerURL = $_POST["ServerURL"]:$lServerURL = $_REQUEST["ServerURL"];
		    }//end if

	    	if ($lProtectAgainstXSS) {
    			/* Protect against XSS by output encoding */
    			$lServerURLText = $Encoder->encodeForHTML($lServerURL);
	    	}else{
	    	    /* allow XSS by not encoding output */
				$lServerURLText = $lServerURL;
	    	}//end if

		}// end if $lFormSubmitted

	}catch(Exception $e){
        echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page test-connectivity.php");
    }// end try
?>

<div class="page-title"><span style="font-size: 18pt;">Can you hear me now?</div>

<?php include_once __SITE_ROOT__.'/includes/back-button.inc';?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

<!-- BEGIN HTML OUTPUT  -->
<form action="index.php?page=test-connectivity.php"
		method="post"
		enctype="application/x-www-form-urlencoded"
		id="idEchoForm">
	<table>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">Click the Test Connectivity Button to Test Webservice Connection</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input name="ServerURL" value="<?php echo $lDefaultServerURL; ?>" type="hidden" id="idServerURLInput" />
				<input name="echo-php-submit-button" class="button" type="submit" value="Test Connectivity" />
			</td>
		</tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
	</table>
</form>

<?php
/* Output results of shell command sent to operating system */
if ($lFormSubmitted){
	    try{
			$lCurrentOrigin = $_SERVER['HTTP_HOST'];
	        echo '<div class="report-header">Results for '.$lServerURLText.'</div>';
            echo '<pre class="output">' .
				 shell_exec("curl --silent -H 'Origin: http://$lCurrentOrigin' " . $lServerURL) .
				 '</pre>';
	        $LogHandler->writeToLog("Executed PHP command: curl --silent " . $lServerURLText);
    	}catch(Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Input: " . $lServerURLText);
    	}// end try
	}// end if (isset($_POST))
?>
