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
		if (isset($_POST["target_host"]) || isset($_REQUEST["target_host"])) {
			$lFormSubmitted = TRUE;
		}// end if
		
		if ($lFormSubmitted){
			
			$lProtectAgainstMethodTampering?$lTargetHost = $_POST["target_host"]:$lTargetHost = $_REQUEST["target_host"];
	    	
	    	if ($lProtectAgainstCommandInjection) {
				/* Protect against command injection. 
				 * We validate that an IP is 4 octets, IPV6 fits the pattern, and that domain name is IANA format */
    			$lTargetHostValidated = preg_match(IPV4_REGEX_PATTERN, $lTargetHost) || preg_match(DOMAIN_NAME_REGEX_PATTERN, $lTargetHost) || preg_match(IPV6_REGEX_PATTERN, $lTargetHost);
	    	}else{
    			$lTargetHostValidated=TRUE; 			// do not perform validation
	    	}// end if

	    	if ($lProtectAgainstXSS) {
    			/* Protect against XSS by output encoding */
    			$lTargetHostText = $Encoder->encodeForHTML($lTargetHost);
	    	}else{
				$lTargetHostText = $lTargetHost; 		//allow XSS by not encoding output	    		
	    	}//end if
	    	
		}// end if $lFormSubmitted    	
    	
		try{
    		$lOSCommandInjectionPointBallonTip = $BubbleHintHandler->getHint("OSCommandInjectionPoint");
       		$lReflectedXSSExecutionPointBallonTip = $BubbleHintHandler->getHint("ReflectedXSSExecutionPoint");
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Error attempting to execute query to fetch bubble hints.");
		}// end try
    		    	    	
	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page dns-lookup.php");
	}// end try	
?>

<div class="page-title">DNS Lookup</div>

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
			echo "var lOSCommandInjectionPattern = /[;&]/;";
		}else{
			echo "var lOSCommandInjectionPattern = /*/;";
		}// end if

		if($lEnableJavaScriptValidation){
			echo "var lCrossSiteScriptingPattern = /[<>=()]/;";
		}else{
			echo "var lCrossSiteScriptingPattern = /*/;";
		}// end if
		?>
		
		if(theForm.target_host.value.search(lOSCommandInjectionPattern) > -1){
			alert("Ampersand and semi-colon are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, XSS is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;
		}else if(theForm.target_host.value.search(lCrossSiteScriptingPattern) > -1){
			alert("Characters used in cross-site scripting are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, XSS is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;			
		}else{
			return true;
		}// end if
	};// end JavaScript function onSubmitOfForm()
</script>

<span>
	<a style="text-decoration: none; cursor: pointer;" href="./webservices/soap/ws-lookup-dns-record.php">
		<img style="vertical-align: middle;" src="./images/ajax_logo-75-79.jpg" height="75px" width="78px" />
		<span style="font-weight:bold;">Switch to SOAP Web Service Version of this Page</span>
	</a>
</span>

<form 	action="index.php?page=dns-lookup.php" 
			method="post" 
			enctype="application/x-www-form-urlencoded" 
			onsubmit="return onSubmitOfForm(this);"
			id="idDNSLookupForm">		
	<table style="margin-left:auto; margin-right:auto;">
		<tr id="id-bad-cred-tr" style="display: none;">
			<td colspan="2" class="error-message">
				Error: Invalid Input
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">Who would you like to do a DNS lookup on?<br/><br/>Enter IP or hostname</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="label">Hostname/IP</td>
			<td>
				<input 	type="text" id="idTargetHostInput" name="target_host" size="20" 
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
				<input name="dns-lookup-php-submit-button" class="button" type="submit" value="Lookup DNS" />
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
	    	if ($lTargetHostValidated){
	    		echo '<div class="report-header" ReflectedXSSExecutionPoint="1">Results for '.$lTargetHostText.'</div>';
    			echo '<pre class="report-header" style="text-align:left;">'.shell_exec("nslookup " . $lTargetHost).'</pre>';
				$LogHandler->writeToLog("Executed operating system command: nslookup " . $lTargetHostText);
	    	}else{
	    		echo '<script>document.getElementById("id-bad-cred-tr").style.display=""</script>';
	    	}// end if ($lTargetHostValidated){

    	}catch(Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Input: " . $lTargetHost);
    	}// end try
    	
	}// end if (isset($_POST)) 
?>