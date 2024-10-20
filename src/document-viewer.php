<?php
	/* Known Vulnerabilities:
		Cross Site Scripting,
		HTML injection,
		HTTP Parameter Pollution
		Method Tampering
		Application Log Injection
	*/

	$lHTMLControls = 'required="required"';
		
	switch ($_SESSION["security-level"]){
		default: // Default case: This code is insecure
   		case "0": // This code is insecure
   			// DO NOTHING: This is insecure
			$lEncodeOutput = false;
			$lProtectAgainstMethodSwitching = false;
			$lHTTPParameterPollutionDetected = false;
			$lEnableHTMLControls = false;
		break;
		case "1": // This code is insecure
   			// DO NOTHING: This is insecure
			$lEncodeOutput = false;
			$lProtectAgainstMethodSwitching = false;
			$lHTTPParameterPollutionDetected = false;
			$lEnableHTMLControls = true;
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
			$lEncodeOutput = true;
			$lProtectAgainstMethodSwitching = true;
			$lEnableHTMLControls = true;
				
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
   		$lDocumentToBeFramed="documentation/robots.php";
   	}//end if

	// Encode output to protect against cross site scripting
	if ($lEncodeOutput){
		$lDocumentToBeFramed = $Encoder->encodeForHTML($lDocumentToBeFramed);
	}// end if
		   	
	// if parameter pollution is not detected, print user choice
   	if (!$lHTTPParameterPollutionDetected){
		$lDocumentToBeFramedMessage = "Currently viewing document &quot;{$lDocumentToBeFramed}&quot;";
   	}// end if
	   	   	
	$LogHandler->writeToLog("User chose to view document: " . $lDocumentToBeFramed);
?>

<div class="page-title">Document Viewer</div>

<?php include_once __SITE_ROOT__.'/includes/back-button.inc';?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

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
					<input	name="PathToDocument" id="id_path_to_document" type="radio" 
							value="robots.txt"
							checked="checked"
							autofocus="autofocus"
							<?php if ($lEnableHTMLControls) {echo $lHTMLControls;} ?>
					/>&nbsp;&nbsp;Robots.txt<br />
					<input	name="PathToDocument" id="id_path_to_document" type="radio"
							value="documentation/mutillidae-installation-on-xampp-win7.pdf" 
							<?php if ($lEnableHTMLControls) {echo $lHTMLControls;} ?>
					/>&nbsp;&nbsp;Installation Instructions: Windows (PDF)<br />
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
