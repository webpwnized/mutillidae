<?php 
	/* Command Injection
	 * Method Tampering
	 * Cross Site Scripting
	 * HTML Injection */

	try {	    	
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure. No input validation is performed.
				$lEnableJavaScriptValidation = FALSE;
				$lEnableHTMLControls = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
				$lProtectAgainstCommandInjection=FALSE;
				$lProtectAgainstXSS = FALSE;
    		break;

    		case "1": // This code is insecure. No input validation is performed.
				$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
				$lProtectAgainstMethodTampering = FALSE;
				$lProtectAgainstCommandInjection=FALSE;
				$lProtectAgainstXSS = FALSE;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lProtectAgainstCommandInjection=TRUE;
				$lEnableHTMLControls = TRUE;
    			$lEnableJavaScriptValidation = TRUE;
   				$lProtectAgainstMethodTampering = TRUE;
   				$lProtectAgainstXSS = TRUE; 			
    		break;
    	}// end switch
    	
    	$lFormSubmitted = FALSE;
		if (isset($_POST["message"]) || isset($_REQUEST["message"])) {
			$lFormSubmitted = TRUE;
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
    	
		try{
    		$lOSCommandInjectionPointBallonTip = $BubbleHintHandler->getHint("OSCommandInjectionPoint");
       		$lReflectedXSSExecutionPointBallonTip = $BubbleHintHandler->getHint("ReflectedXSSExecutionPoint");
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Error attempting to execute query to fetch bubble hints.");
		}// end try
    		    	    	
	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page echo.php");
	}// end try	
?>

<div class="page-title"><span style="font-size: 18pt;">Echo</span>, <span style="font-size: 16pt;">Echo</span>, <span style="font-size: 14pt;">Echo</span>...</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<script type="text/javascript">
	$(function() {
		$('[OSCommandInjectionPoint]').attr("title", "<?php echo $lOSCommandInjectionPointBallonTip; ?>");
		$('[OSCommandInjectionPoint]').balloon();
		$('[ReflectedXSSExecutionPoint]').attr("title", "<?php echo $lReflectedXSSExecutionPointBallonTip; ?>");
		$('[ReflectedXSSExecutionPoint]').balloon();
	});
</script>
    
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

<form 	action="index.php?page=echo.php" 
			method="post" 
			enctype="application/x-www-form-urlencoded" 
			onsubmit="return onSubmitOfForm(this);"
			id="idEchoForm">		
	<table style="margin-left:auto; margin-right:auto;">
		<tr id="id-bad-cred-tr" style="display: none;">
			<td colspan="2" class="error-message">
				Error: Invalid Input
			</td>
		</tr>
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
						OSCommandInjectionPoint="1"
						<?php
							if ($lEnableHTMLControls) {
								echo('minlength="1" maxlength="20" required="required"');
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
	        echo '<div class="report-header" ReflectedXSSExecutionPoint="1">Results for '.$lMessageText.'</div>';
	        
	        if ($lProtectAgainstCommandInjection) {
	            echo '<pre class="report-header" style="text-align:left;">'.$lMessage.'</pre>';
	            $LogHandler->writeToLog("Executed PHP command: echo " . $lMessageText);
	        }else{
	            echo '<pre class="report-header" style="text-align:left;">'.shell_exec("echo " . $lMessage).'</pre>';
	            $LogHandler->writeToLog("Executed operating system command: echo " . $lMessageText);
	        }//end if

    	}catch(Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Input: " . $lMessage);
    	}// end try
    	
	}// end if (isset($_POST)) 
?>