
<?php

/**** Testing Input ****
<?xml version='1.0'?>
<!DOCTYPE change-log [
<!ENTITY systemEntity SYSTEM "robots.txt">
]>
<change-log>
	<text>&systemEntity;</text>
</change-log>
*/
 
	function HandleXmlError($errno, $errstr, $errfile, $errline){
	    if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0)){
	        throw new DOMException($errstr);
	    }else{
	        return false;
	    }//end if
	}// end function HandleXmlError

	try {	    	
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure
				$lEnableHTMLControls = FALSE;
    			//$lFormMethod = "GET";
				$lEnableJavaScriptValidation = FALSE;
				$lEnableXMLValidation = FALSE;
				$lEnableXMLEncoding = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
				libxml_disable_entity_loader(FALSE);
			break;
    		
    		case "1": // This code is insecure
				$lEnableHTMLControls = TRUE;
    			//$lFormMethod = "GET";
				$lEnableJavaScriptValidation = TRUE;
				$lEnableXMLValidation = FALSE;
				$lEnableXMLEncoding = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
				libxml_disable_entity_loader(FALSE);
			break;
	    		
			case "2":
			case "3":
			case "4":
    		case "5": // This code is fairly secure
				$lEnableHTMLControls = TRUE;
    			//$lFormMethod = "POST";
				$lEnableJavaScriptValidation = TRUE;
				$lEnableXMLValidation = TRUE;
				$lEnableXMLEncoding = TRUE;
				$lProtectAgainstMethodTampering = TRUE;
				libxml_disable_entity_loader(TRUE);
			break;
    	}//end switch

    	if ($lEnableHTMLControls) {
    		$lHTMLControlAttributes='required="required"';
    	}else{
    		$lHTMLControlAttributes="";
    	}// end if

    	$lFormSubmitted = FALSE;
		if (isset($_POST["xml-validator-php-submit-button"]) || isset($_REQUEST["xml-validator-php-submit-button"])) {
			$lFormSubmitted = TRUE;
		}// end if

		if ($lFormSubmitted){
	    	if ($lProtectAgainstMethodTampering) {
	    			$lXMLValidatorSubmitButton = $_POST["xml-validator-php-submit-button"];
	    			$lXML = $_POST["xml"];
	    	}else{
	    			$lXMLValidatorSubmitButton = $_REQUEST["xml-validator-php-submit-button"];
	    			$lXML = $_REQUEST["xml"];
	    	}// end if $lProtectAgainstMethodTampering

	    	try {
	    		if ($lEnableXMLEncoding){
	    			$lXMLToLog = $Encoder->encodeForXML($lXML);
	    		}else{
	    			$lXMLToLog = $lXML;
	       		};
				$LogHandler->writeToLog("Recieved request to validate XML for: " . $lXMLToLog);					
			} catch (Exception $e) {
				//do nothing
			}// end try	
		}// end if $lFormSubmitted
		
   	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
   	}// end try;
?>

<script type="text/javascript">
	<?php 
	if($lEnableJavaScriptValidation){
		echo "var lValidateInput = \"TRUE\"" . PHP_EOL;
	}else{
		echo "var lValidateInput = \"FALSE\"" . PHP_EOL;
	}// end if		
	?>
			
	function onSubmitOfForm(/*HTMLFormElement*/ theForm){
		try{
			var lUnsafePhrases = /ENTITY/i;

			if(lValidateInput == "TRUE"){
				if (theForm.xml.value.length === 0){
						alert('Please enter a value.');
						return false;
				}// end if
				
				if (theForm.xml.value.search(lUnsafePhrases) > -1){
						alert('Dangerous phrases detected. We can\'t allow these. This all powerful blacklist will stop such attempts.\n\nMuch like padlocks, filtering cannot be defeated.\n\nBlacklisting is l33t like l33tspeak.');
						return false;
				}// end if
			}// end if(lValidateInput)
			
			return true;
		}catch(e){
			alert("Error: " + e.message);
		}// end catch
	}// end function onSubmitOfForm(/*HTMLFormElement*/ theForm)
	
</script>

<div class="page-title">XML Validator</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<form 	action="./index.php?page=xml-validator.php"
		method="POST" 
		enctype="application/x-www-form-urlencoded"
		onsubmit="return onSubmitOfForm(this);"
>
	<input type="hidden" name="page" value="xml-validator.php" />	
	<table>
		<tr id="id-bad-cred-tr" style="display: none;">
			<td colspan="2" class="error-message">
				Authentication Error: Bad XML Input
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">Please Enter XML to Validate</td>
		</tr>
		<tr>
			<td colspan="2">
				<span class="label">Example:</span>
				&lt;somexml&gt;&lt;message&gt;Hello World&lt;/message&gt;&lt;/somexml&gt;
			</td>
		</tr>
		<tr>
			<td class="label">XML</td>
			<td>
				<textarea name="xml" rows="8" cols="50" id="idXMLTextArea" title="Please enter XML to validate" autofocus="autofocus" <?php echo $lHTMLControlAttributes ?>></textarea>
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input name="xml-validator-php-submit-button" class="button" type="submit" value="Validate XML" />
			</td>
		</tr>
	</table>	
</form>

<?php
	if (isset($lXMLValidatorSubmitButton) && !empty($lXMLValidatorSubmitButton) && strlen($lXML) > 0){

		try{		
			if(!($lEnableXMLValidation && (preg_match(XML_EXTERNAL_ENTITY_REGEX_PATTERNS, $lXML) || !preg_match(VALID_XML_CHARACTERS, $lXML)))){

				echo "<fieldset>";
				echo "<legend>XML Submitted</legend>";
				echo "<div width='600px' class=\"important-code\">" . $Encoder->encodeForXML($lXML) . "</div>";
				echo "</fieldset>";
				echo "<div>&nbsp;</div>";
				
				try {
					set_error_handler('HandleXmlError');
					
					$lDOMDocument = new DOMDocument();
					$lDOMDocument->resolveExternals = true;
					$lDOMDocument->substituteEntities = true;
					$lDOMDocument->preserveWhiteSpace=true;
					$lDOMDocument->loadXML($lXML);

					echo "<fieldset>";
					echo "<legend>Text Content Parsed From XML</legend>";
					echo "<div width='600px'>" . $lDOMDocument->textContent . "</div>";
					echo "</fieldset>";
					echo "<div>&nbsp;</div>";

					restore_error_handler();
				} catch(Exception $e) {
					echo $CustomErrorHandler->FormatError($e, "Could not parse XML because the input is mal-formed or could not be interpreted.");
				}//end try

			}else{
				echo "<div>&nbsp;</div>";
				echo "<div style=\"width:500px;margin-right:auto;margin-left:auto;\" class=\"warning-message\">
						Possible XML external entity injection attack detected.<br/>
						Support has been notified.
					  </div>";
			}//end if

    	} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, $lQueryString);
       	}// end try;
    	
	}// end if (isset($_POST)) 
?>