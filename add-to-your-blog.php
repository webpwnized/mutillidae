<?php
	/* Known Vulnerabilities: 
		SQL injection, 
		Cross Site Scripting, 
		Cross Site Request Forgery,
		Application Exception Output,
		HTML injection,
		Method Tampering
		Known Vulnerable Output: Name, Comment, "Add blog for" title,
	*/

	/* Instantiate CSRF Protection object */
	require_once (__ROOT__.'/classes/CSRFTokenHandler.php');
	$lCSRFTokenHandler = new CSRFTokenHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"], "register-user");
	
	if (!isSet($logged_in_user)) {
		throw new Exception("$logged_in_user is not set. Page add-to-your-blog.php requires this variable.");
	}// end if

	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   			// DO NOTHING: This is insecure		
			$lEncodeOutput = FALSE;
			$lLoggedInUser = $logged_in_user;
			$lTokenizeAllowedMarkup = FALSE;
			$lProtectAgainstSQLInjection = FALSE;
			$lEnableJavaScriptValidation = FALSE;
			$lEnableHTMLControls = FALSE;
			$lProtectAgainstMethodTampering = FALSE;
		break;	   		

   		case "1": // This code is insecure
   			// DO NOTHING: This is insecure		
			$lEncodeOutput = FALSE;
			$lLoggedInUser = $logged_in_user;
			$lTokenizeAllowedMarkup = FALSE;
			$lProtectAgainstSQLInjection = FALSE;
			$lEnableJavaScriptValidation = TRUE;
			$lEnableHTMLControls = TRUE;
			$lProtectAgainstMethodTampering = FALSE;
		break;	   		

		case "2":
		case "3":
		case "4":
		case "5": // This code is fairly secure
  			/* 
  			 * NOTE: Input validation is excellent but not enough. The output must be
  			 * encoded per context. For example, if output is placed in HTML,
  			 * then HTML encode it. Blacklisting is a losing proposition. You 
  			 * cannot blacklist everything. The business requirements will usually
  			 * require allowing dangerous charaters. In the example here, we can 
  			 * validate username but we have to allow special characters in passwords
  			 * least we force weak passwords. We cannot validate the signature hardly 
  			 * at all. The business requirements for text fields will demand most
  			 * characters. Output encoding is the answer. Validate what you can, encode it
  			 * all.
  			 */
   			// encode the output following OWASP standards
   			// this will be HTML encoding because we are outputting data into HTML
			$lEncodeOutput = TRUE;
			
			/* Business Problem: Sometimes the business requirements define that users
			 * should be allowed to use some HTML  markup. If unneccesary, this is a
			 * bad idea. Output encoding will naturally kill any users attempt to use HTML
			 * in their input, which is exactly why we use output encoding. 
			 * 
			 * If the business process allows some HTML, then those HTML items are elevated
			 * from "mallicious input" to "direct object refernces" (a resource to be enjoyed).
			 * When we want to restrict a user to using to "direct object refernces" (a 
			 * resource to be enjoyed) responsibly, we use mapping. Mapping allows the user
			 * to chose from a "system generated" (that's us programmers) set of tokens
			 * to pick from. We need to assure that the user either chooses one of the tokens
			 * we offer, or our system rejects the request. To put it bluntly, either the user
			 * follows the rules, or their output is encoded. Period.
			 */
			$lTokenizeAllowedMarkup = TRUE;
			
			/* If we are in secure mode, we need to protect against SQLi */
			$lProtectAgainstSQLInjection = TRUE;
				
			/* Note that $MySQLHandler->escapeDangerousCharacters is ok but not the best defense. Stored
			 * Procedures are a much more powerful defense, run much faster, can be
			 * trapped in a schema, can run on the database, and can be called from
			 * any number of web applications. Stored procs are the true anti-pwn.
			 * There are 3 ways that stored procs can be made vulenrable by developers,
			 * but they are safe by default. Queries are vulnerable by default.
			 */
			$lLoggedInUser = $MySQLHandler->escapeDangerousCharacters($logged_in_user);

			/* 
			 * There is no security in JS validation. You must validate on the server.
			 * JS is easy to bypass.
			 */
			$lEnableJavaScriptValidation = TRUE;
			$lEnableHTMLControls = TRUE;
			$lProtectAgainstMethodTampering = TRUE;
   		break;
   	}// end switch
   	
	$lNewCSRFTokenForNextRequest = $lCSRFTokenHandler->generateCSRFToken();
   	$lFormSubmitted = isSet($_POST["add-to-your-blog-php-submit-button"]);
	/* ----------------------------------------
	 * Insert user's new blog entry 
	 * ----------------------------------------
	 * precondition: $logged_in_user is not null 
	 */
	if($lFormSubmitted){
		try {
			
			if ($lProtectAgainstMethodTampering) {
				$lPostedCSRFToken = $_POST['csrf-token'];
			}else{
				$lPostedCSRFToken = $_REQUEST['csrf-token'];
			}//end if
					
			if (!$lCSRFTokenHandler->validateCSRFToken($lPostedCSRFToken)){
				throw (new Exception("Security Violation: Cross Site Request Forgery attempt detected.", 500));
			}// end if
						
			// Grab inputs
			if ($lProtectAgainstSQLInjection){
				// This might prevent SQL injection on the insert.
				$lBlogEntry = $MySQLHandler->escapeDangerousCharacters($_POST["blog_entry"]);
			}else{
				$lBlogEntry = $_REQUEST["blog_entry"];
			}// end if

			/* Some dangerous markup allowed. Here we tokenize it for storage. */
			if ($lTokenizeAllowedMarkup){
				$lBlogEntry = str_ireplace('<b>', BOLD_STARTING_TAG, $lBlogEntry);
				$lBlogEntry = str_ireplace('</b>', BOLD_ENDING_TAG, $lBlogEntry);
				$lBlogEntry = str_ireplace('<i>', ITALIC_STARTING_TAG, $lBlogEntry);
				$lBlogEntry = str_ireplace('</i>', ITALIC_ENDING_TAG, $lBlogEntry);
				$lBlogEntry = str_ireplace('<u>', UNDERLINE_STARTING_TAG, $lBlogEntry);
				$lBlogEntry = str_ireplace('</u>', UNDERLINE_ENDING_TAG, $lBlogEntry);				
			}// end if $lTokenizeAllowedMarkup			
			
			// weak server-side input validation. not good enough.
			if(strlen($lBlogEntry) > 0){
				$lValidationFailed = FALSE;
				
				try {
					$SQLQueryHandler->insertBlogRecord($lLoggedInUser, $lBlogEntry);	
				} catch (Exception $e) {
					echo $CustomErrorHandler->FormatError($e, "Error inserting blog for " . $lLoggedInUser);
				}//end try
				
				try {
					$LogHandler->writeToLog("Blog entry added by: " . $lLoggedInUser);	
				} catch (Exception $e) {
					// do nothing
				}//end try
				
			}else{
				$lValidationFailed = TRUE;
			}// end if(strlen($lBlogEntry) > 0)
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Error inserting blog");
		}// end try
	}else {
		$lValidationFailed = FALSE;
	}// end if isSet($_POST["add-to-your-blog-php-submit-button"])
?>

<!-- BEGIN HTML OUTPUT  -->
<script type="text/javascript">
	var onSubmitBlogEntry = function(/* HTMLForm */ theForm){

		<?php 
			if($lEnableJavaScriptValidation){
				echo "var lInvalidBlogPattern = /\'/;";
			}else{
				echo "var lInvalidBlogPattern = /[]/;";
			}// end if
		?>

		if(theForm.blog_entry.value.search(lInvalidBlogPattern) > -1){
			alert('Single-quotes are not allowed. Dont listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.');
			return false;
		}
	};// end JavaScript function onSubmitBlogEntry()
</script>

<div class="page-title">Welcome To The Blog</div>

<?php include_once (__ROOT__.'/includes/back-button.inc'); ?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<fieldset>
	<legend>Add New Blog Entry</legend>
	<form 	action="index.php?page=add-to-your-blog.php" 
			method="post" 
			enctype="application/x-www-form-urlencoded" 
			onsubmit="return onSubmitBlogEntry(this);"
			id="idBlogForm"
			>
		<input name="csrf-token" type="hidden" value="<?php echo $lNewCSRFTokenForNextRequest; ?>" />
		<span>
			<a href="./index.php?page=view-someones-blog.php" style="text-decoration: none;">
			<img style="vertical-align: middle;" src="./images/magnifying-glass-icon.jpeg" height="32px" width="32px" />
			<span style="font-weight:bold;">&nbsp;View Blogs</span>
			</a>
		</span>
		<table>
			<tr id="id-bad-blog-entry-tr" style="display: none;">
				<td class="error-message">
					Validation Error: Blog entry cannot be blank
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td id="id-blog-form-header-td" class="form-header">
					Add blog for <?php echo $lLoggedInUser?>
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td class="report-header">
					Note: &lt;b&gt;,&lt;i&gt; and &lt;u&gt; are now allowed in blog entries
				</td>
			</tr>
			<tr>
				<td>
					<textarea 	name="blog_entry" rows="8" cols="65"
								autofocus="autofocus"
						<?php 
							if ($lEnableHTMLControls) {
								echo('minlength="1" maxlength="100" required="required"');
							}// end if
						?>
					></textarea>
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td style="text-align:center;">
					<input name="add-to-your-blog-php-submit-button" XSRFVulnerabilityArea="1" class="button" type="submit" value="Save Blog Entry" />
				</td>
			</tr>
			<tr><td></td></tr>
		</table>
	</form>
</fieldset>

<?php
	if ($lValidationFailed) {
		echo '<script>document.getElementById("id-bad-blog-entry-tr").style.display="";</script>'; 
	}// end if ($lValidationFailed)
?>

<?php
	/* Display current user's blog entries */
	try {		

		try {
			/* Note that the logged in user could be used for SQL injection */
			$lQueryResult = $SQLQueryHandler->getBlogRecord($lLoggedInUser);
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Error selecting blog entries for " . $lLoggedInUser . ": " . $lQuery);
		}//end try

		try {
			$LogHandler->writeToLog("Selected blog entries for " . $lLoggedInUser);	
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Error writing selected blog entries to log");
		}// end try
				
		echo '<div>&nbsp;</div>
				<span>
					<a href="./index.php?page=view-someones-blog.php">
						<img style="vertical-align: middle;" src="./images/magnifying-glass-icon.jpeg" height="32px" width="32px" />
						<span style="font-weight:bold; text-decoration: none;">View Blogs</span>
					</a>
				</span>';
		echo '<table border="1px" width="90%" class="results-table">';
	    echo ' 	<tr class="report-header">
		    		<td colspan="4">'.$lQueryResult->num_rows.' Current Blog Entries</td>
		    	</tr>
		    	<tr class="report-header">
		    		<td>&nbsp;</td>
				    <td>Name</td>
				    <td>Date</td>
				    <td>Comment</td>
			    </tr>';

	    $lRowNumber = 0;
	    while($lRecord = $lQueryResult->fetch_object()){
	    	
	    	$lRowNumber++;
	    	
			if(!$lEncodeOutput){
				$lBloggerName = $lRecord->blogger_name;
				$lDate = $lRecord->date;
				$lComment = $lRecord->comment;
			}else{
				$lBloggerName = $Encoder->encodeForHTML($lRecord->blogger_name);
				$lDate = $Encoder->encodeForHTML($lRecord->date);
				$lComment = $Encoder->encodeForHTML($lRecord->comment);
			}// end if

			/* Some dangerous markup allowed. Here we restore the tokenized output. 
			 * Note that using GUIDs as tokens works well because they are 
			 * fairly unique plus they encode to the same value. 
			 * Encoding wont hurt them.
			 * 
			 * Note: Mutillidae is weird. It has to be broken and unbroken at the same time.
			 * Here we un-tokenize our output no matter if we are in secure mode or not.
			 */
			$lComment = str_ireplace(BOLD_STARTING_TAG, '<span style="font-weight:bold;">', $lComment);
			$lComment = str_ireplace(BOLD_ENDING_TAG, '</span>', $lComment);
			$lComment = str_ireplace(ITALIC_STARTING_TAG, '<span style="font-style: italic;">', $lComment);
			$lComment = str_ireplace(ITALIC_ENDING_TAG, '</span>', $lComment);
			$lComment = str_ireplace(UNDERLINE_STARTING_TAG, '<span style="border-bottom: 1px solid #000000;">', $lComment);
			$lComment = str_ireplace(UNDERLINE_ENDING_TAG, '</span>', $lComment);

			echo "<tr>
					<td>{$lRowNumber}</td>
					<td>{$lBloggerName}</td>
					<td>{$lDate}</td>
					<td>{$lComment}</td>
				</tr>\n";
		}//end while $lRecord
		echo "</table><div>&nbsp;</div>";		

	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQuery);
	}// end try	
?>

<?php 
	if ($lFormSubmitted) {
		echo $lCSRFTokenHandler->generateCSRFHTMLReport();
	}// end if
?>
