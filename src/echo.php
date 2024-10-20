<?php
	/* Command Injection
	 * Method Tampering
	 * Cross Site Scripting
	 * HTML Injection */

	try {
    	switch ($_SESSION["security-level"]){
			default: // Default case: This code is insecure
    		case "0": // This code is insecure
				$lEnableJavaScriptValidation = false;
				$lEnableHTMLControls = false;
				$lProtectAgainstMethodTampering = false;
				$lProtectAgainstCommandInjection=false;
				$lProtectAgainstXSS = false;
    		break;

    		case "1": // This code is insecure
				$lEnableJavaScriptValidation = true;
				$lEnableHTMLControls = true;
				$lProtectAgainstMethodTampering = false;
				$lProtectAgainstCommandInjection=false;
				$lProtectAgainstXSS = false;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lProtectAgainstCommandInjection=true;
				$lEnableHTMLControls = true;
    			$lEnableJavaScriptValidation = true;
   				$lProtectAgainstMethodTampering = true;
   				$lProtectAgainstXSS = true;
    		break;
    	}// end switch

    	$lFormSubmitted = false;
		if (isset($_POST["message"]) || isset($_REQUEST["message"])) {
			$lFormSubmitted = true;
		}// end if

		if ($lFormSubmitted){

			$lProtectAgainstMethodTampering?$lMessage = $_POST["message"]:$lMessage = $_REQUEST["message"];

	    	if ($lProtectAgainstXSS) {
    			/* Protect against XSS by output encoding */
    			$lMessageText = $Encoder->encodeForHTML($lMessage);
	    	}else{
				$lMessageText = $lMessage; 		//allow XSS by not encoding output
	    	}//end if

		}// end if $lFormSubmitted

	}catch(Exception $e){
        echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page echo.php");
    }// end try
?>

<div class="page-title"><span style="font-size: 18pt;">Echo</span>, <span style="font-size: 16pt;">Echo</span>, <span style="font-size: 14pt;">Echo</span>...</div>

<?php include_once __SITE_ROOT__.'/includes/back-button.inc';?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

<!-- BEGIN HTML OUTPUT  -->
<script type="text/javascript">
	var onSubmitOfForm = function(/* HTMLForm */ theForm){

		<?php
		if($lEnableJavaScriptValidation){
			echo "var lOSCommandInjectionPattern = /[;&|<>]/;";
			echo "var lCrossSiteScriptingPattern = /[<>=()]/;";
		}else{
			echo "var lOSCommandInjectionPattern = /[]/;";
			echo "var lCrossSiteScriptingPattern = /[]/;";
		}// end if
		?>

		if(theForm.message.value.search(lOSCommandInjectionPattern) > -1){
			alert("Malicious characters are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;
		}else if(theForm.message.value.search(lCrossSiteScriptingPattern) > -1){
			alert("Characters used in cross-site scripting are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;
		}else{
			return true;
		}// end if
	};// end JavaScript function onSubmitOfForm()
</script>

<a href="index.php?page=content-security-policy.php">
	<img src="images/shield-icon-75-75.png" alt="Shield Icon" />
    <span class="label">Switch to Content Security Policy (CSP)</span>
</a>
<span class="buffer"></span>
<a href="index.php?page=cors.php">
	<img src="images/cors-icon-75-75.png" alt="CORS Icon" />
    <span class="label">Switch to Cross-Origin Resouce Sharing</span>
</a>

<form 	action="index.php?page=echo.php"
			method="post"
			enctype="application/x-www-form-urlencoded"
			onsubmit="return onSubmitOfForm(this);"
			id="idEchoForm">
	<table>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">Enter message to echo</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="label">Message</td>
			<td>
				<input 	type="text" id="idMessageInput" name="message" size="20"
						autofocus="autofocus"

						<?php
							if ($lEnableHTMLControls) {
								echo 'minlength="1" maxlength="20" required="required"';
							}// end if
						?>
				/>
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input name="echo-php-submit-button" class="button" type="submit" value="Echo Message" />
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
	        echo '<div class="report-header">Results for '.$lMessageText.'</div>';

	        if ($lProtectAgainstCommandInjection) {
	            echo '<pre class="output">'.$lMessageText.'</pre>';
	            $LogHandler->writeToLog("Executed PHP command: echo " . $lMessageText);
	        }else{
	            echo '<pre class="output">'.shell_exec("echo " . $lMessage).'</pre>';
	            $LogHandler->writeToLog("Executed operating system command: echo " . $lMessageText);
	        }//end if

    	}catch(Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Input: " . $lMessageText);
    	}// end try

	}// end if (isset($_POST))
?>