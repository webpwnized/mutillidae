<?php 
	/* Known Vulnerabilities
	 * SQL Injection, (Fix: Use Schematized Stored Procedures)
	 * Cross Site Scripting, (Fix: Encode all output)
	 * Cross Site Request Forgery, (Fix: Tokenize transactions)
	 * Insecure Direct Object Reference, (Fix: Tokenize Object References)
	 * Denial of Service, (Fix: Truncate Log Queries)
	 * Loading of Local Files, (Fix: Tokenize Object Reference - Filename references in this case)
	 * Improper Error Handling, (Fix: Employ custom error handler)
	 * SQL Exception, (Fix: Employ custom error handler)
	 * HTTP Parameter Pollution (Fix: Scope request variables)
	 * Method Tampering
	 */
	try {	    	
		switch ($_SESSION["security-level"]){
   			case "0": // This code is insecure
				$lEnableHTMLControls = FALSE;
   				$lUseTokenization = FALSE;
				$lEncodeOutput = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
			break;

   			case "1": // This code is insecure
				$lEnableHTMLControls = TRUE;
   				$lUseTokenization = FALSE;
				$lEncodeOutput = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
			break;

			case "2":
			case "3":
			case "4":
	   		case "5": // This code is fairly secure
				$lEnableHTMLControls = TRUE;
	   			$lUseTokenization = TRUE;
				$lEncodeOutput = TRUE;
				$lProtectAgainstMethodTampering = TRUE;
			break;
	   	}// end switch ($_SESSION["security-level"])

	   	if ($lEnableHTMLControls) {
	   		$lHTMLControlAttributes='required="required"';
	   	}else{
	   		$lHTMLControlAttributes="";
	   	}// end if

	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error in text file viewer. Cannot load file.");
	}// end try
?>

<div class="page-title">Hacker Files of Old</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<form 	action="index.php?page=text-file-viewer.php" 
		method="post" 
		enctype="application/x-www-form-urlencoded">
		
	<table>
		<tr id="id-bad-cred-tr" style="display: none;">
			<td colspan="2" class="error-message">
				Validation Error: Bad Selection
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" class="form-header">Take the time to read some of these great old school hacker text files.<br />Just choose one form the list and submit.</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="label">Text File Name</td>
			<td>
				<select size="1" name="textfile" id="id_textfile_select" autofocus="autofocus" <?php echo $lHTMLControlAttributes ?>>
					<option value="<?php if ($lUseTokenization){echo 1;}else{echo 'http://www.textfiles.com/hacking/auditool.txt';}?>">Intrusion Detection in Computers by Victor H. Marshall (January 29, 1991)</option>
					<option value="<?php if ($lUseTokenization){echo 2;}else{echo 'http://www.textfiles.com/hacking/atms';}?>">An Overview of ATMs and Information on the Encoding System</option>
					<option value="<?php if ($lUseTokenization){echo 3;}else{echo 'http://www.textfiles.com/hacking/backdoor.txt';}?>">How to Hold Onto UNIX Root Once You Have It</option>
					<option value="<?php if ($lUseTokenization){echo 4;}else{echo 'http://www.textfiles.com/hacking/hack1.hac';}?>">The Basics of Hacking, by the Knights of Shadow (Intro)</option>
					<option value="<?php if ($lUseTokenization){echo 5;}else{echo 'http://www.textfiles.com/hacking/hacking101.hac';}?>">HACKING 101 - By Johnny Rotten - Course #1 - Hacking, Telenet, Life</option>
				</select>
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input name="text-file-viewer-php-submit-button" class="button" type="submit" value="View File" />
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td class="label" colspan="2">For other great old school hacking texts, check out 
			<a href="http://www.textfiles.com/" target="_blank">
				http://www.textfiles.com/
			</a>.</td></tr>
			<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
</form>

<?php
	try {	    	
		if (isset($_POST['text-file-viewer-php-submit-button'])){

			/********************************************
			 * Protect against Method Tampering in security level 5
			 *********************************************/
			if ($lProtectAgainstMethodTampering){
				$pTextFile=$_POST["textfile"];
			}else{
				/* insecure: $_REQUEST would take input from GET or POST. 
				 * This can result in an HTTP Parameter Polution
	   			 * attack. If a site uses POST, then grab input from _POST. Use _GET for gets. HPP can
	   			 * occur more easily when input is ambiguous.
	   			 */
				$pTextFile = $_REQUEST['textfile'];		
			}//end if

			/********************************************
			 * Protect against IDOR in security level 5
			 *********************************************/
			$lURL = "";
			if ($lUseTokenization) {
		   			/* Direct object references in the form of the "textfile"
		   			 parameter give the user complete control of the input. Contrary to popular belief, 
		   			 input validation, blacklisting, etc is not the best defense. The best defenses are 
		   			 provably secure 100% of the time. For direct object references, there are two defenses.
		   			 Authorization via ACL or Entitlements is used when transaction requires authentication.
		   			 This transaction (forwarding URL) does not require authentication so the other method is used;
		   			 mapping. Mapping substitutes a harmless token for the direct object. The direct object in 
		   			 this case is the page the user is being forwarded to. We will use mapping to secure this code.
		   			 
		   			 Note: Some sites try to use validation to defend against Insecure Direct Object References.
		   			 Validation fails in many cases due to weak validators.
		   			
		   			 Note: For static links, the best defense is to simply hardcode the links in an anchor tag.
		   			 This exercise will use mapping to show how it works, but it should be recognized that 
		   			 for giving the user links to click, hardcoding is the best defense.
 
		   			 * Also, the web is weakly typed. All data is strings. It doesnt matter what the developers
		   			 * thinks the input is (int, string, char, etc.). The fact is that HTTP is text. if the 
		   			 * "textfile" is expected to be integer, it should be validated as such. If string, then 
		   			 * validate as string.
		   			 * 
		   			 *  Definition of validation. Perform all of:
		   			 *  
		   			 *  check data type
		   			 *  check data length
		   			 *  check character set
		   			 *  check pattern
		   			 *  check range
		   			 
		   			 * The "textfile" is expected to be integer, so validate as such. Also,
		   			 * dont use _REQUEST as this would allow a POSTed "textfile" to be sent 
		   			 * along with a URL query parameter "textfile" as well. This type of sloppy
		   			 * variable fetching can result in HTTP Parameter Pollution. 
		   			 */
		
		   			/* We expect small int. validate positive integer between 0-9.
		   			 * Regex pattern makes sure the user doesnt send in characters that
		   			 * are not actually digits but can be cast to digits.
		   			 */	
		   			$isDigits = (preg_match("/\d{1,2}/", $pTextFile) == 1);    			
		   			if ($isDigits && $pTextFile > 0 && $pTextFile < 11){
						/* Insecure Direct Object References are patched
						 * by removing the direct object reference all together.
						 * Web applications are "fronts" for services. Some web
						 * sites offer web pages, some offer XML, SOAP, or other
						 * services. In any case, the web site should not "give away"
						 * information about internal objects such as database IDs,
						 * redirection URLs, system file names, or application
						 * paths/configuration.
						 * 
						 * Offer the user harmless tokens instead of actual 
						 * objects. In this case, we use integers to map to
						 * the direct object, which is the forwarding URL.
						 */ 
		   				switch($pTextFile){
		   					case 1: $lURL = "http://www.textfiles.com/hacking/auditool.txt";break;
		   					case 2: $lURL = "http://www.textfiles.com/hacking/atms";break;
		   					case 3: $lURL = "http://www.textfiles.com/hacking/backdoor.txt";break;
		   					case 4: $lURL = "http://www.textfiles.com/hacking/hack1.hac";break;
		   					case 5: $lURL = "http://www.textfiles.com/hacking/hacking101.hac";break;
		   				}// end switch($pTextFile)

		   			}else{
		   				throw(new Exception("Expected integer input. Cannot process request. Support team alerted."));
		   			}// end if
			} else {
				$lURL = $pTextFile;
			}// end if $lUseTokenization

			/********************************************
			 * Protect against XSS in security level 5
			 *********************************************/
			if ($lEncodeOutput){
				$lTextFileDescription = $Encoder->encodeForHTML($lURL);
			} else {
				$lTextFileDescription = $lURL;
			}// end if $lEncodeOutput

			/********************************************
			 * Log file description
			 *********************************************/
			try {
				$LogHandler->writeToLog("Using URL: " . $lTextFileDescription . " based on user choice.");	
			} catch (Exception $e) {
				//Do nothing. Do not interrupt page for failed log attempt.
			}//end try

			/********************************************
			 * Open file and display contents
			 *********************************************/
			try{				
			    // open file handle
				$handle = fopen($lURL, "r");
	   			echo '<span class="label">File: '.$lTextFileDescription.'</span>';
	   			echo '<pre>';
	   			echo stream_get_contents($handle);
				echo '</pre>';
				fclose($handle);

				try {
					$LogHandler->writeToLog("Displayed contents of URL: " . $lTextFileDescription);	
				} catch (Exception $e) {
					//Do nothing. Do not interrupt page for failed log attempt.
				}//end try
				
			}catch(Exception $e){
				echo $CustomErrorHandler->FormatError($e, "Error opening file stream. Cannot load file.");
			}// end try		   	
		   				
		}// end if (isset($_POST['text-file-viewer-php-submit-button']))
	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error in text file viewer. Cannot load file.");
	}// end try
?>