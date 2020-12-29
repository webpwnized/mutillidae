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
	}catch(Exception $e){
	    echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page html5-storage.php");
	}// end try
?>

<div class="page-title">DNS Lookup</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

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

		if(theForm.target_host.value.search(lOSCommandInjectionPattern) > -1){
			alert("Malicious characters are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;
		}else if(theForm.target_host.value.search(lCrossSiteScriptingPattern) > -1){
			alert("Characters used in cross-site scripting are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
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
	<table>
		<tr id="id-bad-cred-tr" style="display: none;">
			<td colspan="2" class="error-message">
				Error: Invalid Input
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">Enter IP or hostname</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="label">Hostname/IP</td>
			<td>
				<input 	type="text" id="idTargetHostInput" name="target_host" size="20"
						autofocus="autofocus"
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
	    		echo '<div class="report-header">Results for '.$lTargetHostText.'</div>';
	    		if ($lProtectAgainstCommandInjection) {
	    		    $lResults = dns_get_record($lTargetHost, DNS_A);
	    		    echo '<pre class="output">';
	    		    foreach ($lResults as $lItem => $lArray) {
	    		        foreach ($lArray as $lRecord => $lValue) {
	    		            if ($lRecord == "host" || $lRecord == "ip") {
    	    		            echo $lRecord.': '.$lValue.'<br />';
    	    		        }// end if
	    		        }// end foreach
	    		        echo '<br />';
	    		    }// end foreach
	    		    echo '</pre>';
	    		}else{
	    		    echo '<pre class="output">'.shell_exec("nslookup " . $lTargetHost).'</pre>';
	    		}//end if $lProtectAgainstCommandInjection
				$LogHandler->writeToLog("Executed operating system command: nslookup " . $lTargetHostText);
	    	}else{
	    		echo '<script>document.getElementById("id-bad-cred-tr").style.display=""</script>';
	    	}// end if $lTargetHostValidated

    	}catch(Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Input: " . $lTargetHost);
    	}// end try

	}// end if (isset($_POST))
?>
