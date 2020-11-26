
<?php
	/* Known Vulnerabilities: 
		Cross Site Scripting, 
		HTML injection,
		HTTP Parameter Pollution
		Method Tampering
		Application Log Injection
	*/
		
	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   			// DO NOTHING: This is insecure		
			$lEncodeOutput = FALSE;
			$lProtectAgainstMethodSwitching = FALSE;
			$lHTTPParameterPollutionDetected = FALSE;
			$lEnableHTMLControls = FALSE;
		break;
		case "1": // This code is insecure
   			// DO NOTHING: This is insecure		
			$lEncodeOutput = FALSE;
			$lProtectAgainstMethodSwitching = FALSE;
			$lHTTPParameterPollutionDetected = FALSE;
			$lEnableHTMLControls = TRUE;
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
   			// encode the output following OWASP standards
   			// this will be HTML encoding because we are outputting data into HTML
			$lEncodeOutput = TRUE;
			$lProtectAgainstMethodSwitching = TRUE;
			$lEnableHTMLControls = TRUE;
				
			// Detect multiple params with same name (HTTP Parameter Pollution)
			$lQueryString  = explode('&', $_SERVER['QUERY_STRING']);
			$lKeys = array();
			$lPair = array();
			$lParameter = "";
			
			foreach ($lQueryString as $lParameter){
				$lPair = explode('=', $lParameter);
				array_push($lKeys, $lPair[0]);
			}//end for each

			$lCountUnique = count(array_unique($lKeys));
			$lCountTotal = count($lKeys);
			
			$lHTTPParameterPollutionDetected = ($lCountUnique < $lCountTotal);
			
   		break;
   	}// end switch
   	
   	// initialize message
  	$lDocumentToBeFramedMessage="No choice selected";
   	$lDocumentChosen=(isset($_REQUEST["PathToDocument"]));
   	
   	if ($lDocumentChosen){
		// if we want to enforce GET method, we need to be careful to specify $_GET
	   	if(!$lProtectAgainstMethodSwitching){
	   		$lDocumentToBeFramed = $_REQUEST["PathToDocument"];
	   	}else{
	   		$lDocumentToBeFramed = $_GET["PathToDocument"];
	   	}//end if 
   	}else{
   		$lDocumentToBeFramed="documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php";
   	}//end if

	// Encode output to protect against cross site scripting 
	if ($lEncodeOutput){
		$lDocumentToBeFramed = $Encoder->encodeForHTML($lDocumentToBeFramed);
	}// end if
		   	
	// if parameter pollution is not detected, print user choice 
   	if (!$lHTTPParameterPollutionDetected){
		$lDocumentToBeFramedMessage = "Currently viewing document &quot;{$lDocumentToBeFramed}&quot;";
   	}// end if isSet($_POST["document-viewer-php-submit-button"])
	   	   	
	$LogHandler->writeToLog("User chose to view document: " . $lDocumentToBeFramed);   	
?>

<div class="page-title">Document Viewer</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<fieldset style="text-align: center;">
	<legend>Document Viewer</legend>
	<form 	action="index.php" 
			method="GET"
			enctype="application/x-www-form-urlencoded" 
			id="idDocumentForm">
		<input type="hidden" name="page" value="document-viewer.php" />
		<table>
			<tr id="id-bad-path-to-document-tr" style="display: none;">
				<td class="error-message">
					Validation Error: HTTP Parameter Pollution Detected. Input cannot be trusted.
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td id="id-document-viewer-form-header-td" class="form-header">Please Choose Document to View</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td style="text-align:left;">
					<input 	name="PathToDocument" id="id_path_to_document" type="radio" 
							value="documentation/change-log.txt"
							checked="checked"
							autofocus="autofocus"
							<?php
								if ($lEnableHTMLControls) {
									echo('required="required"');
								}// end if
							?>					
					/>&nbsp;&nbsp;Change Log<br />
					<input	name="PathToDocument" id="id_path_to_document" type="radio" 
							value="robots.txt"
							<?php
								if ($lEnableHTMLControls) {
									echo('required="required"');
								}// end if
							?>
					/>&nbsp;&nbsp;Robots.txt<br />
					<input	name="PathToDocument" id="id_path_to_document" type="radio"
							value="documentation/mutillidae-installation-on-xampp-win7.pdf" 
							<?php
								if ($lEnableHTMLControls) {
									echo('required="required"');
								}// end if
							?>
					/>&nbsp;&nbsp;Installation Instructions: Windows 7 (PDF)<br />
					<input	name="PathToDocument" id="id_path_to_document" type="radio"
							value="documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php" 
							<?php
								if ($lEnableHTMLControls) {
									echo('required="required"');
								}// end if
							?>
					/>&nbsp;&nbsp;How to access Mutillidae over Virtual-Box-network<br />
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td style="text-align:center;">
					<input name="document-viewer-php-submit-button" class="button" type="submit" value="View Document" />
				</td>
			</tr>
		</table>
	</form>

	<div>&nbsp;</div>
	<div class="label">
	<?php 
		if (!$lEncodeOutput){
			echo $lDocumentToBeFramedMessage; 
		}else{
			echo $Encoder->encodeForHTML($lDocumentToBeFramedMessage);
		}// end if
		$LogHandler->writeToLog("Framing document: " . $lDocumentToBeFramedMessage);
	?>
	</div>
	<div>&nbsp;</div>
	<iframe src="<?php echo $lDocumentToBeFramed; ?>" width="700px" height="500px"></iframe>
</fieldset>

<?php
	if ($lHTTPParameterPollutionDetected) {
		echo '<script>document.getElementById("id-bad-path-to-document-tr").style.display="";</script>'; 
	}// end if ($lHTTPParameterPollutionDetected)
?>