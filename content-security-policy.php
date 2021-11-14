<?php
/* Command Injection
 * Method Tampering
 * Cross Site Scripting
 * HTML Injection */

try {
    switch ($_SESSION["security-level"]){
        case "0": // This code is insecure. No input validation is performed.
            $lEnableJavaScriptValidation = FALSE;
            $lEnableHTMLControls = FALSE;
            $lProtectAgainstMethodTampering = FALSE;
            $lProtectAgainstCommandInjection=FALSE;
            $lProtectAgainstXSS = FALSE;
            break;

        case "1": // This code is insecure. No input validation is performed.
            $lEnableJavaScriptValidation = TRUE;
            $lEnableHTMLControls = TRUE;
            $lProtectAgainstMethodTampering = FALSE;
            $lProtectAgainstCommandInjection=FALSE;
            $lProtectAgainstXSS = FALSE;
            break;

        case "2":
        case "3":
        case "4":
        case "5": // This code is fairly secure
            $lProtectAgainstCommandInjection=TRUE;
            $lEnableHTMLControls = TRUE;
            $lEnableJavaScriptValidation = TRUE;
            $lProtectAgainstMethodTampering = TRUE;
            $lProtectAgainstXSS = TRUE;
            break;
    }// end switch

    $lFormSubmitted = FALSE;
    if (isset($_POST["message"]) || isset($_REQUEST["message"])) {
        $lFormSubmitted = TRUE;
    }// end if

    if ($lFormSubmitted){

        $lProtectAgainstMethodTampering?$lMessage = $_POST["message"]:$lMessage = $_REQUEST["message"];

        if ($lProtectAgainstXSS) {
            /* Protect against XSS by output encoding */
            $lMessageText = $Encoder->encodeForHTML($lMessage);
        }else{
            $lMessageText = $lMessage; 		//allow XSS by not encoding output
        }//end if

    }// end if $lFormSubmitted

}catch(Exception $e){
    echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page content-security-policy.php");
}// end try
?>

<script src="javascript/on-page-scripts/content-security-policy.js"></script>
<div class="page-title">Content Security Policy (CSP)</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<a href="index.php?page=echo.php">
    <img src="images/malware-icon-75-75.png" />
    <span class="label">Switch to Cross-Site Scripting (XSS)</span>
</a>
<span class="buffer"></span>
<a href="index.php?page=cors.php">
    <img src="images/cors-icon-75-75.png" />
    <span class="label">Switch to Cross-Origin Resource Sharing (CORS)</span>
</a>

<form action="index.php?page=content-security-policy.php"
	  method="post"
	  enctype="application/x-www-form-urlencoded"
	  id="idCSPForm">
	<table>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">Abandon Hope All Ye Who Enter XSS Here</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="label">Message</td>
			<td>
				<input 	type="text" id="idMessageInput" name="message" size="20" autofocus="autofocus"
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
				<input name="content-security-policy-php-submit-button" class="button" type="submit" value="Submit" />
			</td>
		</tr>
	</table>
</form>

<?php
/* Output results of shell command sent to operating system */
if ($lFormSubmitted){
    try{
        echo '<div>&nbsp;</div>';
        echo '<div class="report-header">Results for '.$lMessageText.'</div>';

        if ($lProtectAgainstCommandInjection) {
            echo '<pre class="output">'.$lMessageText.'</pre>';
            $LogHandler->writeToLog("Executed PHP command: echo " . $lMessageText);
        }else{
            echo '<pre class="output">'.shell_exec("echo -n " . $lMessage).'</pre>';
            $LogHandler->writeToLog("Executed operating system command: echo " . $lMessageText);
        }//end if

	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Input: " . $lMessage);
	}// end try

}// end if
?>

<br/>
<fieldset>
    <legend>Current Content Security Policy (CSP) Report To Endpoints</legend>
    <?php echo $lReportToHeader ?>
</fieldset>
<br/>
<fieldset>
    <legend>Current Content Security Policy (CSP)</legend>
    <?php
        $l_string = str_replace(";", ";<br />", $lCSP);
        $l_string = str_replace(": ", ": <br />", $l_string);
        echo $l_string;
    ?>
</fieldset>
<br />

<script nonce="<?php echo $CSPNonce; ?>">
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('idCSPForm').addEventListener('submit',
            function(event){
                <?php
                    if($lEnableJavaScriptValidation){
            	         echo "if(!onSubmitOfForm(this)){event.preventDefault()}";
                    }else{
                         echo "return true;";
                    }
            	?>
    		});
	});
</script>