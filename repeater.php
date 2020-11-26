<?php 
	try{
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure.
				$lEnableJavaScriptValidation = FALSE;
				$lEnableHTMLControls = FALSE;
				$lEnableBufferOverflowProtection = FALSE;
				$lProtectAgainstMethodSwitching = FALSE;
				$lCreateParameterAdditionVulnerability = TRUE;
    		break;

    		case "1": // This code is insecure.
				$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
				$lEnableBufferOverflowProtection = FALSE;
				$lProtectAgainstMethodSwitching = FALSE;
				$lCreateParameterAdditionVulnerability = TRUE;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
    			$lEnableBufferOverflowProtection = TRUE;
				$lProtectAgainstMethodSwitching = TRUE;
				$lCreateParameterAdditionVulnerability = FALSE;
    		break;
    	}// end switch
    	
		// if we want to enforce POST method, we need to be careful to specify $_POST
	   	if(!$lProtectAgainstMethodSwitching){
			$lSubmitButtonClicked = isset($_REQUEST["repeater-php-submit-button"]);
	   	}else{
			$lSubmitButtonClicked = isset($_POST["repeater-php-submit-button"]);
	   	}//end if    	

	   	if($lSubmitButtonClicked){

			// if we want to enforce POST method, we need to be careful to specify $_POST
		   	if(!$lProtectAgainstMethodSwitching){
		   		$lStringToRepeat = $_REQUEST["string_to_repeat"];
		   		$lTimesToRepeatString = $_REQUEST["times_to_repeat_string"];
		   	}else{
		   		$lStringToRepeat = $_POST["string_to_repeat"];
		   		$lTimesToRepeatString = $_POST["times_to_repeat_string"];
		   	}//end if    	
	   		
	    	if($lEnableBufferOverflowProtection){
	   			/* NOTE: We expect total integer that is less than 134,217,728 when mutilplied 
	   			 * by length of the string.
	   			 * Validate positive integer.
	   			 * Regex pattern makes sure the user doesnt send in characters that
	   			 * are not actually digits but can be cast to digits.
	   			 */
	    		$lMaximumPHPStringBufferSize = 134217728;
	    		$lLengthOfNullTerminator = 1;
	    		$lMaximumPHPStringBufferSize = $lMaximumPHPStringBufferSize - $lLengthOfNullTerminator;
	    		$lTimesToRepeatStringIsDigits = (preg_match("/^[0-9]{1,9}$/", $lTimesToRepeatString) == 1);
	    		$lStringToRepeatIsReasonable = (preg_match("/^[A-Za-z0-9\.\!\@\#\$\%\^\&\*\(\)\{\}\,\<\.\>\/\?\=\+\-\_]{1,256}$/", $lStringToRepeat) == 1);
	    		$lErrorMessage = "See exception for error message";
	
	    		if(!$lTimesToRepeatStringIsDigits){
	    			$lErrorMessage = "The times to repeat string does not appear to be an integer.";
	    			throw new Exception($lErrorMessage);	
	    		}// end if

	    		if(!$lStringToRepeatIsReasonable){
	    			$lErrorMessage = "The string to repeat does not appear to be reasonable.";
	    			throw new Exception($lErrorMessage);	
	    		}// end if

	    		if(($lTimesToRepeatString * strlen($lStringToRepeat)) > $lMaximumPHPStringBufferSize){
	    			$lErrorMessage = "The buffer that would need to be allocated exceeds the PHP maximum string buffer size.";
	    			throw new Exception($lErrorMessage);	
	    		}// end if

	    	}// end if($lEnableBufferOverflowProtection)

	    	/* Cast second number to integer to make the hack easier to pull off. Users will be tempted
	    	 * put in a number so large, that the $lTimesToRepeatString number will overflow
	    	 * before the str_repeat function gets a chance to run.
	    	 */
 			$lBuffer = str_repeat($lStringToRepeat, (integer)$lTimesToRepeatString);
    	
	   	}//end if $lSubmitButtonClicked

	} catch(Exception $e){
		$lSubmitButtonClicked = FALSE;
		echo "<div class=\"error-message\">".$lErrorMessage."</div>";
		echo $CustomErrorHandler->FormatError($e, "Error attempting to repeat string.");
	}// end try	
?>

<script type="text/javascript">
<!--
	<?php 
		if ($lSubmitButtonClicked) {
			echo "var l_submit_occured = true;" . PHP_EOL;
		}else {
			echo "var l_submit_occured = false;" . PHP_EOL;
		}// end if

		if($lEnableJavaScriptValidation){
			echo "var lValidateInput = true" . PHP_EOL;
		}else{
			echo "var lValidateInput = false" . PHP_EOL;
		}// end if		
	?>

	function onSubmitOfRepeaterForm(/*HTMLFormElement*/ theForm){
		try{
			var lTimesToRepeatStringAcceptablePattern = RegExp("^[0-9]{1,9}$","gi");
			var lStringToRepeatAcceptablePattern = RegExp("^[A-Za-z0-9\.\!\@\#\$\%\^\&\*\(\)\{\}\,\<\.\>\/\?\=\+\-\_]{1,256}$", "gi");

			if(lValidateInput){
				
				if (theForm.string_to_repeat.value.match(lStringToRepeatAcceptablePattern) == null){
							alert('Dangerous characters detected in string to repeat. We can\'t allow these. This all powerful blacklist will stop such attempts.\n\nMuch like padlocks, filtering cannot be defeated.\n\nBlacklisting is l33t like l33tspeak.');
							return false;
					}// end if

				if (theForm.times_to_repeat_string.value.match(lTimesToRepeatStringAcceptablePattern) == null){
							alert('Times to repeat string does not appear to be a number.');
							return false;
					}// end if

			}// end if(lValidateInput)
			
			return true;
		}catch(e){
			alert("Error: " + e.message);
		}// end catch
	}// end function onSubmitOfRepeaterForm(/*HTMLFormElement*/ theForm)
//-->
</script>

<div class="page-title">Repeater</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<?php 
	if ($lCreateParameterAdditionVulnerability) {
		echo "<!-- Diagnostics: Request Parameters - ";
		echo var_dump($_REQUEST);
		echo "-->";
	}// end if
?>

<div id="id-repeater-form-div" style="text-align:center;">
	<form 	action="index.php?page=repeater.php" 
			method="post" 
			enctype="application/x-www-form-urlencoded" 
			onsubmit="return onSubmitOfRepeaterForm(this);"
			id="idRepeaterForm">
		<table>
			<tr>
				<td colspan="2" class="form-header">Please enter string to repeat</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td class="label" style="text-align: left;">String to repeat</td>
				<td style="text-align: left;">
					<input type="text" name="string_to_repeat" size="40" autofocus="autofocus"
						<?php
							if ($lEnableHTMLControls) {
								echo('minlength="1" maxlength="40" required="required"');
							}// end if
						?>
					/>
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Number of times to repeat</td>
				<td style="text-align: left;">
					<input type="text" name="times_to_repeat_string" size="30"
						<?php
							if ($lEnableHTMLControls) {
								echo('minlength="1" maxlength="30" required="required"');
							}// end if
						?>
					/>
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input name="repeater-php-submit-button" class="button" type="submit" value="Repeat String" />
				</td>
			</tr>
			<tr><td></td></tr>
		</table>
	</form>
</div>

<div id="id-repeater-output-div" style="text-align: center; display: none;">
	<table>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="hint-header"><?php echo $lBuffer; ?></td>
		</tr>
		<tr><td></td></tr>
	</table>	
</div>

<script type="text/javascript">
	if (l_submit_occured){
		document.getElementById("id-repeater-output-div").style.display="";		
	}// end if l_submit_occured	
</script>