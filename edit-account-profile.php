<?php
    require_once (__ROOT__.'/classes/CSRFTokenHandler.php');
    $lCSRFTokenHandler = new CSRFTokenHandler("owasp-esapi-php/src/", $_SESSION["security-level"], "edit-account-profile");
    
    switch ($_SESSION["security-level"]){
        case "0": // This code is insecure
            // DO NOTHING: This is equivalent to using client side security
            $lEnableJavaScriptValidation = FALSE;
            $lEnableHTMLControls = FALSE;
            $lProtectAgainstMethodTampering = FALSE;
            $lProtectAgainstIDOR = FALSE;
            $lProtectAgainstPasswordLeakage = FALSE;
            $lEncodeOutput = FALSE;
            break;
            
        case "1": // This code is insecure
            // DO NOTHING: This is equivalent to using client side security
            $lEnableJavaScriptValidation = TRUE;
            $lEnableHTMLControls = TRUE;
            $lProtectAgainstMethodTampering = FALSE;
            $lProtectAgainstIDOR = FALSE;
            $lProtectAgainstPasswordLeakage = FALSE;
            $lEncodeOutput = FALSE;
            break;
            
        case "2":
        case "3":
        case "4":
        case "5": // This code is fairly secure
            /*
             * Concerning SQL Injection, use parameterized stored procedures. Parameterized
             * queries is not good enough. You cannot use least privilege with queries.
             */
            $lEnableJavaScriptValidation = TRUE;
            $lEnableHTMLControls = TRUE;
            $lProtectAgainstMethodTampering = TRUE;
            $lProtectAgainstIDOR = TRUE;
            $lProtectAgainstPasswordLeakage = TRUE;
            $lEncodeOutput = TRUE;
            break;
    }// end switch
    
    $lNewCSRFTokenForNextRequest = $lCSRFTokenHandler->generateCSRFToken();
    $lFormSubmitted = isset($_REQUEST["edit-account-profile-php-submit-button"]);
?>

<div class="page-title">Edit Profile</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<?php

	if ($lFormSubmitted){
		
		try {					
			$lValidationFailed = false;
					
	   		if ($lProtectAgainstMethodTampering) {
   				$lUsername = $_POST["username"];
				$lPassword = $_POST["password"];
				$lConfirmedPassword = $_POST["confirm_password"];
				$lUserSignature = $_POST["my_signature"];
				$lPostedCSRFToken = $_POST['csrf-token'];
	   		}else{
	   			$lUsername = $_REQUEST["username"];
				$lPassword = $_REQUEST["password"];
				$lConfirmedPassword = $_REQUEST["confirm_password"];
				$lUserSignature = $_REQUEST["my_signature"];
				$lPostedCSRFToken = $_REQUEST['csrf-token'];
	   		}//end if
	   		
	   		if ($lEncodeOutput){
	   			$lUsernameText = $Encoder->encodeForHTML($lUsername);
	   		}else{
	   			//allow XSS by not encoding
	   			$lUsernameText = $lUsername;
	   		}//end if
	   		
			$LogHandler->writeToLog("Attempting to add account for: " . $lUsername);				
		   	
			if (!$lCSRFTokenHandler->validateCSRFToken($lPostedCSRFToken)){
				throw (new Exception("Security Violation: Cross Site Request Forgery attempt detected.", 500));
			}// end if
					
		   	if (strlen($lUsername) == 0) {
		   		$lValidationFailed = TRUE;
				echo '<h2 class="error-message">Username cannot be blank</h2>';
		   	}// end if
					
		   	if ($lPassword != $lConfirmedPassword ) {
				$lValidationFailed = TRUE;
		   		echo '<h2 class="error-message">Passwords do not match</h2>';
		   	}// end if
						   	
		   	if (!$lValidationFailed){					
		   		$lRowsAffected = $SQLQueryHandler->updateUserAccount($lUsername, $lPassword, $lUserSignature);
				echo '<div class="success-message">Profile updated for ' . $lUsernameText . '</div>';
				$LogHandler->writeToLog("Profile updated for: " . $lUsername);
		   	}// end if (!$lValidationFailed)
			
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Failed to add account");
			$LogHandler->writeToLog("Failed to update profile for: " . $lUsername);			
		}// end try
			
	}// end if $lFormSubmitted
	
	if($lProtectAgainstIDOR){
	    if(isset($_SESSION['uid'])){
	       $lUserUID = $_SESSION['uid'];
	    }else{
	        $lUserUID = NULL;
	    } // if isset
	}else{
	    if(isset($_REQUEST['uid'])){
	        $lUserUID = $_REQUEST['uid'];
	    }else{
	        if(isset($_COOKIE['uid'])){
	            $lUserUID = $_COOKIE['uid'];
	        }else{
	            $lUserUID = NULL;
	        } // if isset
	    } // if isset
	} // $lProtectAgainstIDOR
	
	$lUserLoggedIn = !(is_null($lUserUID));

	$lUsername = "";
	$lPassword = "";
	$lSignature = "";
	$lResultsFound = FALSE;
	
	if($lUserLoggedIn){
	    try {
	           $lQueryResult = $SQLQueryHandler->getUserAccountByID($lUserUID);
	           $LogHandler->writeToLog("Got account with UID : " . $lUserUID);
	           
	           if (isset($lQueryResult->num_rows)){
	               if ($lQueryResult->num_rows > 0) {
	                   $lResultsFound = TRUE;
	               }//end if
	           }//end if

	           if($lResultsFound){
	               $row = $lQueryResult->fetch_object();
	               
	               if(!$lEncodeOutput){
	                   $lUsername = $row->username;
	                   if (!$lProtectAgainstPasswordLeakage){
	                       $lPassword = $row->password;
	                   }
	                   $lSignature = $row->mysignature;
	               }else{
	                   $lUsername = $Encoder->encodeForHTML($row->username);
	                   if (!$lProtectAgainstPasswordLeakage){
	                       $lPassword = $Encoder->encodeForHTML($row->password);
	                   }
	                   $lSignature = $Encoder->encodeForHTML($row->mysignature);
	               }// end if
	           }
	           
	    } catch (Exception $e) {
	        echo $CustomErrorHandler->FormatError($e, "Failed to get account");
	        $LogHandler->writeToLog("Failed to get account with UID : " . $lUserUID);
	    }// end try
	} // if $lUserLoggedIn
?>

<script type="text/javascript">
<!--
	<?php 
		if($lEnableJavaScriptValidation){
			echo "var lValidateInput = \"TRUE\"" . PHP_EOL;
		}else{
			echo "var lValidateInput = \"FALSE\"" . PHP_EOL;
		}// end if		
	?>

	function onSubmitOfForm(/*HTMLFormElement*/ theForm){
		try{
			if(lValidateInput == "TRUE"){
				var lUnsafeCharacters = /[`~!@#$%^&*()-_=+\[\]{}\\|;':",./<>?]/;
				if (theForm.username.value.length > 15 || 
					theForm.password.value.length > 15){
						alert('Username too long. We dont want to allow too many characters.\n\nSomeone might have enough room to enter a hack attempt.');
						return false;
				};// end if
				
				if (theForm.username.value.search(lUnsafeCharacters) > -1 || 
					theForm.password.value.search(lUnsafeCharacters) > -1){
						alert('Dangerous characters detected. We can\'t allow these. This all powerful blacklist will stop such attempts.\n\nMuch like padlocks, filtering cannot be defeated.\n\nBlacklisting is l33t like l33tspeak.');
						return false;
				};// end if
			};// end if(lValidateInput)
			
			return true;
		}catch(e){
			alert("Error: " + e.message);
		};// end catch
	};// end function onSubmitOfLoginForm(/*HTMLFormElement*/ theForm)
//-->
</script>

<span>
	<a style="text-decoration: none; cursor: pointer;" href="./webservices/rest/ws-user-account.php">
		<img style="vertical-align: middle;" src="./images/ajax_logo-75-79.jpg" height="75px" width="78px" />
		<span style="font-weight:bold;">Switch to RESTful Web Service Version of this Page</span>
	</a>
</span>

<div id="id-edit-account-profile-form-div" style="display: hidden;">
	<form	action="index.php?page=edit-account-profile.php" method="post" enctype="application/x-www-form-urlencoded"
			onsubmit="return onSubmitOfForm(this);"
			>
		<input name="csrf-token" type="hidden" value="<?php echo $lNewCSRFTokenForNextRequest; ?>" />
		<table>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan="2" class="form-header">Please choose your username, password and signature</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td class="label">Username</td>
				<td>
					<input type="text" name="username" size="15" autofocus="autofocus"
						<?php
							if ($lEnableHTMLControls) {
								echo('minlength="1" maxlength="15" required="required"');
							}// end if
						    echo('value="' . $lUsername . '"');
						?>
					/>
				</td>
			</tr>
			<tr>
				<td class="label">Password</td>
				<td>
					<input type="password" name="password" size="15" 
						<?php
							if ($lEnableHTMLControls) {
								echo('minlength="1" maxlength="15" required="required"');
							}// end if
						    echo('value="' . $lPassword . '"');
						?>
					/>
					&nbsp;
					<a href="index.php?page=password-generator.php&username=<?php echo $logged_in_user ?>" target="_blank">Password Generator</a>
				</td>
			</tr>
			<tr>
				<td class="label">Confirm Password</td>
				<td>
					<input type="password" name="confirm_password" size="15"
						<?php
							if ($lEnableHTMLControls) {
								echo('minlength="1" maxlength="15" required="required"');
							}// end if
							echo('value="' . $lPassword . '"');
						?>
					/>
				</td>
			</tr>
			<tr>
				<td class="label">Signature</td>
				<td>
					<textarea rows="3" cols="50" name="my_signature"
						<?php
							if ($lEnableHTMLControls) {
								echo('minlength="1" maxlength="100" required="required"');
							}// end if
						?>
					><?php echo $lSignature; ?></textarea>
				</td>
			</tr>			
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input name="edit-account-profile-php-submit-button" class="button" type="submit" value="Update Profile" />
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</form>
</div>

<div id="id-profile-not-found-div" style="text-align: center; display: none;">
	<table>
		<tr>
			<td class="label">User profile not found. You may <a href="index.php?page=login.php">login here</a></td>
		</tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr>
			<td style="text-align:center; font-style: italic;">
				Dont have an account? <a href="index.php?page=register.php">Please register here</a>
			</td>
		</tr>
	</table>	
</div>

<script>
	var lResultsFound = <?php echo $lResultsFound?"true":"false"; ?>;
	if (lResultsFound){
		document.getElementById("id-edit-account-profile-form-div").style.display="";
		document.getElementById("id-profile-not-found-div").style.display="none";
	}else{
		document.getElementById("id-edit-account-profile-form-div").style.display="none";
		document.getElementById("id-profile-not-found-div").style.display="";		
	}// end if lResultsFound	
</script>
	
<?php
	if ($lFormSubmitted) {
		echo $lCSRFTokenHandler->generateCSRFHTMLReport();
	}// end if
?>