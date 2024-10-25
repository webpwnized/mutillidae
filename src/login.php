<?php
	try {
		$lHTMLControls = 'minlength="1" maxlength="20" required="required"';

		switch ($_SESSION["security-level"]){
			default: // Default case: This code is insecure
			case "0": // This code is insecure.
			case "1": // This code is insecure.
				$lEnableJavaScriptValidation = false;
				$lEnableHTMLControls = false;
				$lEncodeOutput = false;
			break;
		
			case "2":
			case "3":
			case "4":
			case "5": // This code is fairly secure
				$lEnableJavaScriptValidation = true;
				$lEnableHTMLControls = true;
				$lEncodeOutput = true;
			break;
		}// end switch

		$lRedirectPage = isset($_GET['redirectPage']) ? $_GET['redirectPage'] : '';
		
		if ($lEncodeOutput) {
			$lRedirectPage = $Encoder->encodeForHTML($lRedirectPage);
		}

	} catch(Exception $e){
		$lErrorMessage = "Error setting up configuration.";
		echo $CustomErrorHandler->FormatError($e, $lErrorMessage);
	}// end try
?>

<script type="text/javascript">
<!--
	<?php
		if (isset($_SESSION["user_is_logged_in"]) && $_SESSION["user_is_logged_in"]) {
			echo "var l_loggedIn = true;" . PHP_EOL;
		}else {
			echo "var l_loggedIn = false;" . PHP_EOL;
		}// end if

		if (isset($lAuthenticationAttemptResult)){
			echo "var lAuthenticationAttemptResultFlag = {$lAuthenticationAttemptResult};" . PHP_EOL;
		}else{
			echo "var lAuthenticationAttemptResultFlag = -1;".PHP_EOL;
		}// end if

		if($lEnableJavaScriptValidation){
			echo "var lValidateInput = \"TRUE\"" . PHP_EOL;
		}else{
			echo "var lValidateInput = \"FALSE\"" . PHP_EOL;
		}// end if
	?>

	function onSubmitOfLoginForm(/*HTMLFormElement*/ theForm){
		try{
			if(lValidateInput == "TRUE"){
				var lUnsafeCharacters = /[\W]/g;
				if (theForm.username.value.length > 20){
						alert('Username too long. We dont want to allow too many characters.\n\nSomeone might have enough room to enter a hack attempt.');
						return false;
				};// end if

				if (theForm.username.value.search(lUnsafeCharacters) > -1){
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

<div class="page-title">Login</div>

<?php include_once __SITE_ROOT__.'/includes/back-button.inc';?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

<div id="id-log-in-form-div" style="display: none; text-align:center;">
	<form 	action="index.php?page=login.php"
			method="post"
			enctype="application/x-www-form-urlencoded"
			onsubmit="return onSubmitOfLoginForm(this);"
			id="idLoginForm">
		<input type="hidden" name="redirectPage" value="<?php echo $lRedirectPage; ?>">
		<table>
			<tr id="id-authentication-failed-tr" style="display: none;">
				<td id="id-authentication-failed-td" colspan="2" class="error-message"></td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td colspan="2" class="form-header">Please sign-in</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td class="label">Username</td>
				<td>
					<input	type="text" name="username" size="20"
							autofocus="autofocus"
							<?php if ($lEnableHTMLControls) { echo $lHTMLControls; } ?>
					/>
				</td>
			</tr>
			<tr>
				<td class="label">Password</td>
				<td>
					<input type="password" name="password" size="20"
					<?php if ($lEnableHTMLControls) { echo $lHTMLControls; } ?>
					/>
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input name="login-php-submit-button" class="button" type="submit" value="Login" />
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td colspan="2" style="text-align:center; font-style: italic;">
					Dont have an account? <a href="index.php?page=register.php">Please register here</a>
				</td>
			</tr>
		</table>
	</form>
</div>

<div id="id-log-out-div" style="text-align: center; display: none;">
	<table>
		<tr>
			<td colspan="2" class="hint-header">You are logged in as <?php echo $_SESSION["logged_in_user"]; ?></td>
		</tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input class="button" type="button" value="Logout" onclick="document.location='index.php?do=logout'" />
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
	var cUNSURE = -1;
   	var cACCOUNT_DOES_NOT_EXIST = 0;
   	var cPASSWORD_INCORRECT = 1;
   	var cNO_RESULTS_FOUND = 2;
   	var cAUTHENTICATION_SUCCESSFUL = 3;
   	var cAUTHENTICATION_EXCEPTION_OCCURED = 4;
   	var cUSERNAME_OR_PASSWORD_INCORRECT = 5;

   	var lMessage = "";
   	var lAuthenticationFailed = "FALSE";

	switch(lAuthenticationAttemptResultFlag){
   		case cACCOUNT_DOES_NOT_EXIST:
   	   		lMessage="Account does not exist"; lAuthenticationFailed = "TRUE";
   	   		break;
   		case cPASSWORD_INCORRECT:
   	   		lMessage="Password incorrect"; lAuthenticationFailed = "TRUE";
   	   		break;
   		case cNO_RESULTS_FOUND:
   	   		lMessage="No results found"; lAuthenticationFailed = "TRUE";
   	   		break;
   		case cAUTHENTICATION_EXCEPTION_OCCURED:
   	   		lMessage="Exception occurred"; lAuthenticationFailed = "TRUE";
   		break;
   		case cUSERNAME_OR_PASSWORD_INCORRECT:
   	   		lMessage="Username or password incorrect"; lAuthenticationFailed = "TRUE";
   		break;
   	};

	if(lAuthenticationFailed=="TRUE"){
		document.getElementById("id-authentication-failed-tr").style.display="";
		document.getElementById("id-authentication-failed-td").innerHTML=lMessage;
	}// end if AuthenticationAttemptResultFlag

	if (!l_loggedIn){
		document.getElementById("id-log-in-form-div").style.display="";
		document.getElementById("id-log-out-div").style.display="none";
	}else{
		document.getElementById("id-log-in-form-div").style.display="none";
		document.getElementById("id-log-out-div").style.display="";
	}// end if l_loggedIn
</script>