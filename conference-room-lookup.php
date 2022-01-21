<?php
	/* LDAP Injection
	 * Method Tampering
	 */

	function encodeForLDAP(/*string*/ $pString) {
		/*const string*/ $cBACKSLASH = "\\";
		/*int*/ $lStrLen = strlen($pString);
		/*string*/ $EncodedString = "";
		for ($i = 0; $i < $lStrLen; $i++){
			if (ctype_alnum($pString[$i])){
				$EncodedString.=$pString[$i];
			}else{
				$EncodedString.=$cBACKSLASH.strval(bin2hex($pString[$i]));
			}
		}
		return $EncodedString;
	}

	try {
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure. No input validation is performed.
				$lEnableJavaScriptValidation = FALSE;
				$lEnableHTMLControls = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
				$lProtectAgainstLDAPInjection=FALSE;
    		break;

    		case "1": // This code is insecure. No input validation is performed.
				$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
				$lProtectAgainstMethodTampering = FALSE;
				$lProtectAgainstLDAPInjection=FALSE;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lProtectAgainstLDAPInjection=TRUE;
				$lEnableHTMLControls = TRUE;
    			$lEnableJavaScriptValidation = TRUE;
   				$lProtectAgainstMethodTampering = TRUE;
    		break;
    	}// end switch

    	$lFormSubmitted = FALSE;
		if (isset($_POST["default_room_common_name"]) || isset($_REQUEST["default_room_common_name"])) {
			$lFormSubmitted = TRUE;
		}// end if

		if ($lFormSubmitted){

			$lProtectAgainstMethodTampering?$lRoomCommonName = $_POST["default_room_common_name"]:$lRoomCommonName = $_REQUEST["default_room_common_name"];


	    	if ($lProtectAgainstLDAPInjection) {
    			/* Protect against LDAP Injection by encoding */
    			$lRoomCommonNameText = encodeForLDAP($lRoomCommonName);
	    	}else{
			/* allow LDAP Injection by not encoding output */
			$lRoomCommonNameText = $lRoomCommonName;
	    	}//end if

		}// end if $lFormSubmitted

	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page conference-lookup.php");
	}// end try
?>

<div class="page-title">Conference Room Lookup</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<!-- BEGIN HTML OUTPUT  -->
<script type="text/javascript">
	var onSubmitOfForm = function(/* HTMLForm */ theForm){

		<?php
		if($lEnableJavaScriptValidation){
			echo "var lOSLDAPInjectionPattern = /[;&\*]/;";
			echo "var lCrossSiteScriptingPattern = /[<>=()]/;";
		}else{
			echo "var lOSLDAPInjectionPattern = /[]/;";
			echo "var lCrossSiteScriptingPattern = /[]/;";
		}// end if
		?>

		if(theForm.default_room_common_name.value.search(lOSLDAPInjectionPattern) > -1){
			alert("Malicious characters are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;
		}else if(theForm.default_room_common_name.value.search(lCrossSiteScriptingPattern) > -1){
			alert("Characters used in cross-site scripting are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;
		}else{
			return true;
		}// end if
	};// end JavaScript function onSubmitOfForm()
</script>

<form 	action="index.php?page=conference-room-lookup.php"
			method="post"
			enctype="application/x-www-form-urlencoded"
			onsubmit="return onSubmitOfForm(this);"
			id="idConferenceRoomLookupForm">
	<table>
		<tr id="id-bad-cred-tr" style="display: none;">
			<td colspan="2" class="error-message">
				Error: Invalid Input
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="form-header">Available Conference Room Lookup</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td>
				<input 	type="hidden" id="idDefaultRoomCommonNameInput" name="default_room_common_name" value="1F104"
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
			<td style="text-align:center;">
				<input name="conference-lookup-php-submit-button" class="button" type="submit" value="Find Available Rooms" />
			</td>
		</tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
	</table>
</form>

<?php
/* Output results of shell LDAP sent to operating system */
if ($lFormSubmitted){
	try{
		require_once(__SITE_ROOT__ . '/includes/ldap-config.inc');

		$ldapconn=ldap_connect(LDAP_HOST, LDAP_PORT);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_bind($ldapconn, LDAP_BIND_DN, LDAP_BIND_PASSWORD);

		$filter="(|(cn=2F204)(cn=".$lRoomCommonNameText."))";
		$sr=ldap_search($ldapconn, LDAP_BASE_DN, $filter);
		$info = ldap_get_entries($ldapconn, $sr);
		$entries = ldap_get_entries($ldapconn, $sr);

		echo '<table><tr><th>These rooms are available</th></tr>';
		foreach ($entries as $key) {
			if ( is_array($key) && isset($key['cn']) ){
			    echo '<tr colspan="1"><td>';
			    echo $key['cn'][0];
			    echo '</td></tr>';
			} // end if
		} // end for each
		echo '</table></div>';

		$LogHandler->writeToLog("Executed LDAP search on: " . $lRoomCommonNameText);

	}catch(Exception $e){
        echo $CustomErrorHandler->FormatError($e, "Input: " . $lRoomCommonNameText);
	}// end try

}// end if ($lFormSubmitted)
?>
