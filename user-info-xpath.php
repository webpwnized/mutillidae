<?php 
	require_once (__ROOT__.'/classes/XMLHandler.php');

	try{
		
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure
				$lEnableHTMLControls = FALSE;
    			$lFormMethod = "GET";
				$lEnableJavaScriptValidation = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
				$lEncodeOutput = FALSE;
				$lProtectAgainstXPathInjection = FALSE;
				break;

    		case "1": // This code is insecure
				$lEnableHTMLControls = TRUE;
    			$lFormMethod = "GET";
				$lEnableJavaScriptValidation = TRUE;
				$lProtectAgainstMethodTampering = FALSE;
				$lEncodeOutput = FALSE;
				$lProtectAgainstXPathInjection = FALSE;
			break;
	    		
			case "2":
			case "3":
			case "4":
    		case "5": // This code is fairly secure
				$lEnableHTMLControls = TRUE;
    			$lFormMethod = "POST";
				$lEnableJavaScriptValidation = TRUE;
				$lProtectAgainstMethodTampering = TRUE;
				$lEncodeOutput = TRUE;
				$lProtectAgainstXPathInjection = TRUE;
			break;
    	}//end switch

    	$lFormSubmitted = FALSE;
		if (isset($_POST["user-info-php-submit-button"]) || isset($_REQUEST["user-info-php-submit-button"])) {
			$lFormSubmitted = TRUE;
		}// end if
		
		if ($lFormSubmitted){
	    	if ($lProtectAgainstMethodTampering) {
	   			$lUserInfoSubmitButton = $_POST["user-info-php-submit-button"];
				$lUsername = $_POST["username"];
				$lPassword = $_POST["password"];
	    	}else{
    			$lUserInfoSubmitButton = $_REQUEST["user-info-php-submit-button"];
				$lUsername = $_REQUEST["username"];
				$lPassword = $_REQUEST["password"];
	    	}// end if $lProtectAgainstMethodTampering
		}// end if $lFormSubmitted

    	/* ------------------------------------------
    	 * initialize XML handler
    	* ------------------------------------------ */
    	$lXMLAccountFilePath = "./data/accounts.xml";
    	$XMLHandler = new XMLHandler("owasp-esapi-php/src/", $_SESSION["security-level"]);
    	$XMLHandler->SetDataSource($lXMLAccountFilePath);
    	 
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error handled on page user-info-xpath.php");
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
			var lUnsafeCharacters = /[`~!@#$%^&*()-_=+\[\]{}\\|;':",./<>?]/;

			if(lValidateInput == "TRUE"){
				if (theForm.username.value.length > 15 || 
					theForm.password.value.length > 15){
						alert('Username too long. We dont want to allow too many characters.\n\nSomeone might have enough room to enter a hack attempt.');
						return false;
				}// end if

				if (theForm.username.value.search(lUnsafeCharacters) > -1 || 
					theForm.password.value.search(lUnsafeCharacters) > -1){
						alert('Dangerous characters detected. We can\'t allow these. This all powerful blacklist will stop such attempts.\n\nMuch like padlocks, filtering cannot be defeated.\n\nBlacklisting is l33t like l33tspeak.');
						return false;
				}// end if
			}// end if(lValidateInput)
			
			return true;
		}catch(e){
			alert("Error: " + e.message);
		}// end catch
	}// end function onSubmitOfForm(/*HTMLFormElement*/ theForm)
	
</script>

<div class="page-title">User Lookup (XPath)</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<span>
	<a style="text-decoration: none; cursor: pointer;" href="./webservices/soap/ws-user-account.php">
		<img style="vertical-align: middle;" src="./images/ajax_logo-75-79.jpg" height="75px" width="78px" />
		<span style="font-weight:bold;">Switch to SOAP Web Service version</span>
	</a>
</span>
&nbsp;&nbsp;&nbsp;
<span>
	<a style="text-decoration: none; cursor: pointer;" href="index.php?page=user-info.php">
		<img style="vertical-align: middle;" src="./images/sql-logo-64-64.png" />
		<span style="font-weight:bold;">Switch to SQL version</span>
	</a>
</span>

<form 	action="./index.php?page=user-info-xpath.php"
		method="<?php echo $lFormMethod; ?>" 
		enctype="application/x-www-form-urlencoded"
		onsubmit="return onSubmitOfForm(this);"
>
	<input type="hidden" name="page" value="user-info-xpath.php" />	
	<table>
		<tr id="id-bad-cred-tr" style="display: none;">
			<td colspan="2" class="error-message">
				Authentication Error: Bad user name or password
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">Please enter username and password<br/> to view account details</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="label">Name</td>
			<td>
				<input type="text" name="username" size="20" autofocus="autofocus" 
					<?php
						if ($lEnableHTMLControls) {
							echo('minlength="1" maxlength="20" required="required"');
						}// end if
					?>
				/>
			</td>
		</tr>
		<tr>
			<td class="label">Password</td>
			<td>
				<input type="password" name="password" size="20"
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
				<input name="user-info-php-submit-button" class="button" type="submit" value="View Account Details" />
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" style="text-align:center; font-style: italic;">
				Dont have an account? <a href="?page=register.php">Please register here</a>
			</td>
		</tr>
	</table>	
</form>

<?php
	if ($lFormSubmitted){
		try {
            
		    $LogHandler->writeToLog("Recieved request to display user information for: " . $lUsername);					
			
			if($lProtectAgainstXPathInjection){
				$lXPathUsername = $Encoder->encodeForXPath($lUsername);
				$lXPathPassword = $Encoder->encodeForXPath($lPassword);
			}else{
				$lXPathUsername = $lUsername;
				$lXPathPassword = $lPassword;
			}// end if

			if($lEncodeOutput){
				$lHTMLUsername = $Encoder->encodeForHTML($lUsername);
			}else{
				$lHTMLUsername = $lUsername;
			}// end if			
			
			$lXPathQueryString = "//Employee[UserName='{USERNAME}' and Password='{PASSWORD}']";
			$lXPathQueryString = str_replace("{USERNAME}", $lXPathUsername, $lXPathQueryString);
			$lXPathQueryString = str_replace("{PASSWORD}", $lXPathPassword, $lXPathQueryString);
			$lXMLQueryResults = $XMLHandler->ExecuteXPATHQuery($lXPathQueryString);
			
			if($lEncodeOutput){
				$lHTMLXPathQueryString = $Encoder->encodeForHTML($lXPathQueryString);
			}else{
				$lHTMLXPathQueryString = $lXPathQueryString;
			}// end if			

			echo '<br />
				  <div class="report-header">
						Results for <span style="color:#770000;">'
					.$lHTMLUsername.
				'</span></div>';		
			
			echo '<br /><span style="font-weight:bold;">Executed query:</span>&nbsp;' . $lHTMLXPathQueryString . '<br /><br />';
			if ($lXMLQueryResults){
				echo $lXMLQueryResults;
			}else{
				echo 'No Results Found<br />';
			};// end if			
			echo "<br /><input type='button' class='button' value='Click Here to View XML' onclick=\"var s=document.getElementById('xml').style; s.display=='none'?s.display='inline':s.display='none';\"><br />";
			echo "<div id=\"xml\" style=\"display: none;\"></div>";
				
    	} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Error attempting to display user information");
       	}// end try;

	}// end if (isset($_POST)) 
?>

<script>
	function decode_data() {
	    var decoded_data = window.atob('<?php echo base64_encode(htmlspecialchars(file_get_contents($lXMLAccountFilePath))); ?>');
	    document.getElementById('xml').innerHTML = '<pre>'+decoded_data+'</pre>';
	}// end function

	decode_data();
</script>
</body>