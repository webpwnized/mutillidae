<?php
	class CrossSiteRequestForgeryException extends Exception {
		public function __construct($message = "Cross Site Request Forgery attempt detected.", $code = 400, Exception $previous = null) {
			parent::__construct($message, $code, $previous);
		}// end function __construct
	}// end class CrossSiteRequestForgeryException

	require_once __SITE_ROOT__.'/classes/CSRFTokenHandler.php';
	$lCSRFTokenHandler = new CSRFTokenHandler($_SESSION["security-level"], "register-user");
	$lHTMLControls = 'minlength="1" maxlength="15" required="required"';

	switch ($_SESSION["security-level"]){
		default: // Default case: This code is insecure
		case "0": // This code is insecure
			// DO NOTHING: This is equivalent to using client side security
			$lEnableJavaScriptValidation = false;
			$lEnableHTMLControls = false;
			$lProtectAgainstMethodTampering = false;
			$lEncodeOutput = false;
			break;
	
		case "1": // This code is insecure
			// DO NOTHING: This is equivalent to using client side security
			$lEnableJavaScriptValidation = true;
			$lEnableHTMLControls = true;
			$lProtectAgainstMethodTampering = false;
			$lEncodeOutput = false;
			break;
	
		case "2":
		case "3":
		case "4":
		case "5": // This code is fairly secure
			/*
			 * Concerning SQL Injection, use parameterized stored procedures. Parameterized
			 * queries is not good enough. You cannot use least privilege with queries.
			 */
			$lEnableJavaScriptValidation = true;
			$lEnableHTMLControls = true;
			$lProtectAgainstMethodTampering = true;
			$lEncodeOutput = true;
			break;
	}// end switch

	$lNewCSRFTokenForNextRequest = $lCSRFTokenHandler->generateCSRFToken();
	$lFormSubmitted = isset($_REQUEST["register-php-submit-button"]);
?>

<div class="page-title">Register for an Account</div>

<?php include_once __SITE_ROOT__.'/includes/back-button.inc';?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

<?php
	if ($lFormSubmitted){
		
		try {
			$lValidationFailed = false;
					
			if ($lProtectAgainstMethodTampering) {
				$lUsername = $_POST["username"];
				$lPassword = $_POST["password"];
				$lConfirmedPassword = $_POST["confirm_password"];
				$lUserSignature = $_POST["my_signature"];
				$lFirstName = $_POST["firstname"];
				$lLastName = $_POST["lastname"];
				$lPostedCSRFToken = $_POST['csrf-token'];
			} else {
				$lUsername = $_REQUEST["username"];
				$lPassword = $_REQUEST["password"];
				$lConfirmedPassword = $_REQUEST["confirm_password"];
				$lUserSignature = $_REQUEST["my_signature"];
				$lFirstName = $_REQUEST["firstname"];
				$lLastName = $_REQUEST["lastname"];
				$lPostedCSRFToken = $_REQUEST['csrf-token'];
			}
	   		
	   		if ($lEncodeOutput){
	   			$lUsernameText = $Encoder->encodeForHTML($lUsername);
	   		}else{
	   			//allow XSS by not encoding
	   			$lUsernameText = $lUsername;
	   		}//end if
	   		
			$LogHandler->writeToLog("Attempting to add account for: " . $lUsername);
		   	
			if (!$lCSRFTokenHandler->validateCSRFToken($lPostedCSRFToken)){
				$lErrorMessage = "Security Violation: Cross Site Request Forgery attempt detected.";
				throw new CrossSiteRequestForgeryException($lErrorMessage, 400, null);
			}// end if

		   	if (strlen($lUsername) == 0) {
		   		$lValidationFailed = true;
				echo '<h2 class="error-message">Username cannot be blank</h2>';
		   	}// end if

			if (strlen($lPassword) == 0) {
				$lValidationFailed = true;
				echo '<h2 class="error-message">Password cannot be blank</h2>';
		 	}// end if

		   	if ($lPassword != $lConfirmedPassword ) {
				$lValidationFailed = true;
		   		echo '<h2 class="error-message">Passwords do not match</h2>';
		   	}// end if

			if (strlen($lFirstName) == 0) {
				$lValidationFailed = true;
				echo '<h2 class="error-message">First Name cannot be blank</h2>';
			}// end if

			if (strlen($lLastName) == 0) {
				$lValidationFailed = true;
				echo '<h2 class="error-message">Last Name cannot be blank</h2>';
			}// end if

		   	if (!$lValidationFailed){
		   		$lRowsAffected = $SQLQueryHandler->insertNewUserAccount($lUsername, $lPassword, $lFirstName, $lLastName, $lUserSignature);
				echo '<h2 class="success-message">Account created for ' . $lUsernameText .'. '.$lRowsAffected.' rows inserted.</h2>';
				$LogHandler->writeToLog("Added account for: " . $lUsername);
		   	}// end if (!$lValidationFailed)

		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Failed to add account");
			$LogHandler->writeToLog("Failed to add account for: " . $lUsername);
		}// end try
			
	}// end if $lFormSubmitted
?>

<script type="text/javascript">
<!--
	<?php
		echo "var lValidateInput = \"" . ($lEnableJavaScriptValidation ? "TRUE" : "FALSE") . "\"" . PHP_EOL;
	?>

	function onSubmitOfForm(/*HTMLFormElement*/ theForm){
		try{
			if(lValidateInput == "TRUE"){
				var lUnsafeCharacters = /[\W]/g;
				if (theForm.username.value.length > 15 || 
					theForm.password.value.length > 15){
						alert('Username too long. We dont want to allow too many characters.\n\nSomeone might have enough room to enter a hack attempt.');
						return false;
				};// end if
				
				if (theForm.username.value.search(lUnsafeCharacters) > -1){
						alert('Dangerous characters detected in username. We can\'t allow these. This all powerful blacklist will stop such attempts.\n\nMuch like padlocks, filtering cannot be defeated.\n\nBlacklisting is l33t like l33tspeak.');
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
		<img style="vertical-align: middle;" src="./images/ajax_logo-75-79.jpg" alt="AJAX" />
		<span style="font-weight:bold;">Switch to RESTful Web Service Version of this Page</span>
	</a>
</span>

<div id="id-registration-form-div">
	<form	action="index.php?page=register.php" method="post" enctype="application/x-www-form-urlencoded"
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
						<?php if ($lEnableHTMLControls) { echo $lHTMLControls; }?>
					/>
				</td>
			</tr>
			<tr>
				<td class="label">Password</td>
				<td>
					<input type="password" name="password" size="15" 
						<?php if ($lEnableHTMLControls) { echo $lHTMLControls; }?>
					/>
					&nbsp;
					<a href="index.php?page=password-generator.php&username=<?php echo $logged_in_user ?>" target="_blank">Password Generator</a>
				</td>
			</tr>
			<tr>
				<td class="label">Confirm Password</td>
				<td>
					<input type="password" name="confirm_password" size="15"
						<?php if ($lEnableHTMLControls) { echo $lHTMLControls; }?>
					/>
				</td>
			</tr>
			<tr>
                <td class="label">First Name</td>
                <td>
                    <input type="text" name="firstname" size="15"
                        <?php if ($lEnableHTMLControls) { echo $lHTMLControls; }?> 
                    />
                </td>
            </tr>
            <tr>
                <td class="label">Last Name</td>
                <td>
                    <input type="text" name="lastname" size="15"
                        <?php if ($lEnableHTMLControls) { echo $lHTMLControls; }?> 
                    />
                </td>
            </tr>
			<tr>
				<td class="label">Signature</td>
				<td>
					<textarea rows="3" cols="50" name="my_signature"
						<?php if ($lEnableHTMLControls) {echo $lHTMLControls;} ?>
					></textarea>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input name="register-php-submit-button" class="button" type="submit" value="Create Account" />
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</form>
</div>

<?php
	if ($lFormSubmitted) {
		echo $lCSRFTokenHandler->generateCSRFHTMLReport();
	}// end if
?>
