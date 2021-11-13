<?php

	function PrettyPrintStringtoHex($lString) {
		$lHexText = "";
		for($i=0;$i<strlen($lString);$i++){
			$lHexText .= "0X" . str_pad(dechex(ord($lString[$i])), 2, "0", STR_PAD_LEFT) . " ";
		}//end for
		return $lHexText;
	}

   	function __xor($lHexString1, $lHexString2) {
		$lBlocksize = 16;
   		$lResult = "";
		for($i=0;$i<$lBlocksize*2;$i+=2){
			$lResult .= str_pad(dechex(hexdec(substr($lHexString1,$i,2)) ^ hexdec(substr($lHexString2,$i,2))), 2, "0", STR_PAD_LEFT);
		}//end for
		return $lResult;
   	}// end function

   	try {
   		if(!(isset($_REQUEST["iv"]) || isset($_GET["iv"]))){
   			//header("Location: index.php?page=view-user-privilege-level.php&iv=6bc24fc1ab650b25b4114e93a98f1eba", true, 302);
			echo "<meta http-equiv=\"refresh\" content=\"0;URL='index.php?page=view-user-privilege-level.php&iv=6bc24fc1ab650b25b4114e93a98f1eba'\">";
   		}//end if
   	} catch (Exception $e) {
		// oh well, keep going
   	}//end try

	try{
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure.
				$lEnableJavaScriptValidation = FALSE;
				$lEnableBufferOverflowProtection = FALSE;
				$lProtectAgainstMethodSwitching = FALSE;
				$lCreateParameterAdditionVulnerability = TRUE;
				$lLeakIVToBrowser = TRUE;
				$lIgnoreUserInfluence = FALSE;
    			$lUserID = "100";
				$lUserGroupID = "100";
			break;

    		case "1": // This code is insecure.
				$lEnableJavaScriptValidation = TRUE;
				$lEnableBufferOverflowProtection = FALSE;
				$lProtectAgainstMethodSwitching = FALSE;
				$lCreateParameterAdditionVulnerability = TRUE;
				$lLeakIVToBrowser = TRUE;
				$lIgnoreUserInfluence = FALSE;
    			$lUserID = "174";
				$lUserGroupID = "235";
			break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lEnableJavaScriptValidation = TRUE;
				$lEnableBufferOverflowProtection = TRUE;
				$lProtectAgainstMethodSwitching = TRUE;
				$lCreateParameterAdditionVulnerability = FALSE;
				$lLeakIVToBrowser = FALSE;
				$lIgnoreUserInfluence = TRUE;
    			$lUserID = "999";
				$lUserGroupID = "999";
			break;
    	}// end switch

		// if we want to enforce POST method, we need to be careful to specify $_POST
    	if(!$lProtectAgainstMethodSwitching){
	   		$lInitializationVector = $_REQUEST["iv"];
	   	}else{
	   		$lInitializationVector = $_GET["iv"];
	   	}//end if

	   	$lApplicationID = "A1B2";
    	$lDefaultInitializationVector = "6bc24fc1ab650b25b4114e93a98f1eba";
    	$lCryptoKey = MD5("SecretSauce12345");
		$lPlaintext = $lApplicationID . $lUserID . $lUserGroupID . "000000";
		$lBlocksize = 16;

		// in case IV is corrupt
		if (strlen($lInitializationVector) != $lBlocksize*2){
	   		$lInitializationVector = $lDefaultInitializationVector;
	   	}//end if

	   	// if site is secure, ignore user input
		if ($lIgnoreUserInfluence){
	   		$lInitializationVector = $lDefaultInitializationVector;
	   	}//end if

		if ($lLeakIVToBrowser){
			$lInitializationVectorValue = $lInitializationVector;
		}else{
			$lInitializationVectorValue = "Undisclosed";
		}//end if

	   	/* ******************************
	   	 * CONVERT PLAINTEXT INTO HEX
	   	 ******************************** */
		$lHexText = "";
		for($i=0;$i<$lBlocksize;$i++){
			$lHexText .= str_pad(dechex(ord($lPlaintext[$i])), 2, "0", STR_PAD_LEFT);
		}//end for

	   	/* **********
	   	 * ENCRYPTION
	   	 ************ */
		$lCiphertext = __xor($lHexText, $lCryptoKey);
		$lChainedCipherBlock = __xor($lDefaultInitializationVector, $lCiphertext);

	   	/* **********
	   	 * DECRYPTION
	   	 ************ */
		$lUnchainedCiphertext = __xor($lInitializationVector, $lChainedCipherBlock);
		$lUnchainedHexText = __xor($lUnchainedCiphertext, $lCryptoKey);

		/* ******************************
	   	 * CONVERT HEX TO PLAINTEXT
	   	 ******************************** */
		$lUnchainedPlaintext = "";
		for($i=0;$i<$lBlocksize*2;$i+=2){
			$lUnchainedPlaintext .= chr(hexdec(substr($lUnchainedHexText,$i,2)));
		}//end for

		$lApplicationIDValue = substr($lUnchainedPlaintext,0,4);
		$lUserIDValue = substr($lUnchainedPlaintext,4,3);
		$lUserGroupIDValue = substr($lUnchainedPlaintext,7,3);

		$lUserIsRoot = FALSE;
		if ($lUserIDValue == "000" && $lUserGroupIDValue == "000"){
			$lUserIsRoot = TRUE;
		}// end if

	} catch(Exception $e){
		//$lSubmitButtonClicked = FALSE;
		echo "<div class=\"error-message\">".$lErrorMessage."</div>";
		echo $CustomErrorHandler->FormatError($e, "Error attempting to repeat string.");
	}// end try
?>

<div class="page-title">View User Privilege Level</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<?php
	if ($lCreateParameterAdditionVulnerability) {
		echo "<!-- Diagnostics: Request Parameters - ";
		echo var_dump($_REQUEST);
		echo "-->";
	}// end if
?>

<div id="id-view-user-privilege-level-form-div" style="text-align:center;">
	<table>
		<tr id="id-user-privilege-message" style="display: none;">
			<td colspan="2" class="error-message">
				User is root!
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">User Privilege Level</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="label" style="text-align: left;">Application ID</td>
			<td style="text-align: left;"><?php echo $lApplicationIDValue; ?></td>
		</tr>
		<tr>
			<td class="label" style="text-align: left;">User ID</td>
			<td style="text-align: left;"><?php echo $lUserIDValue . " ( Hint: " . PrettyPrintStringtoHex($lUserIDValue) . ")"; ?></td>
		</tr>
		<tr>
			<td class="label" style="text-align: left;">Group ID</td>
			<td style="text-align: left;"><?php echo $lUserGroupIDValue . " ( Hint: " . PrettyPrintStringtoHex($lUserGroupIDValue) . ")"; ?></td>
		</tr>
		<tr><td></td></tr>
		<tr><td class="label" colspan="2">Note: UID/GID "000" is root.<br />You need to make User ID and Group ID equal to<br />"000" to become root user.</td></tr>
		<tr><td></td></tr>
		<tr><td class="label" colspan="2">Security level 1 requires three times more work<br />but is not any harder to solve.</td></tr>
	</table>
</div>

<div id="id-view-user-privilege-level-output-div" style="text-align: center; display: none;">
	<table>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="hint-header"><?php echo $lBuffer; ?></td>
		</tr>
		<tr><td></td></tr>
	</table>
</div>

<script type="text/javascript">
<?php
	if ($lUserIsRoot) {
		echo "var l_user_is_root = true;" . PHP_EOL;
	}else {
		echo "var l_user_is_root = false;" . PHP_EOL;
	}// end if
?>
	if (l_user_is_root){
		document.getElementById("id-user-privilege-message").style.display="";
	}// end if l_user_is_root
</script>