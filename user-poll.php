
<?php
	/* Known Vulnerabilities: 
		Cross Site Scripting, 
		Cross Site Request Forgery,
		Application Exception Output,
		HTML injection,
		HTTP Parameter Pollution
		SQL Injection
	*/

	require_once (__ROOT__.'/classes/CSRFTokenHandler.php');
	$lCSRFTokenHandler = new CSRFTokenHandler("owasp-esapi-php/src/", $_SESSION["security-level"], "register-user");

	if (!isSet($logged_in_user)) {
		throw new Exception("$logged_in_user is not set. Page add-to-your-blog.php requires this variable.");
	}// end if
	
	function isParameterPollutionDetected(/*String*/ $pQueryString){
		
		try {
			// Detect multiple params with same name (HTTP Parameter Pollution)
			$lQueryString  = explode('&', $pQueryString);
			$lKeys = array();
			$lPair = array();
			$lParameter = "";
			$lCountUnique = 0;
			$lCountTotal = 0;
				
			foreach ($lQueryString as $lParameter){
				$lPair = explode('=', $lParameter);
				array_push($lKeys, $lPair[0]);
			}//end for each
			
			$lCountUnique = count(array_unique($lKeys));
			$lCountTotal = count($lKeys);
				
			return ($lCountUnique < $lCountTotal);

		} catch (Exception $e) {
				return FALSE;
		}//end catch
				
	}//end function isParameterPollutionDetected()
	
	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   			$lEnableHTMLControls = FALSE;
   			$lEncodeOutput = FALSE;
   			$lProtectAgainstMethodTampering = FALSE;
   			$lHTTPParameterPollutionDetected = FALSE;
   			$lLoggedInUser = $logged_in_user;
   		break;
   			   			
   		case "1": // This code is insecure
   			// DO NOTHING: This is insecure		
			$lEnableHTMLControls = TRUE;
   			$lEncodeOutput = FALSE;
			$lProtectAgainstMethodTampering = FALSE;
			$lHTTPParameterPollutionDetected = FALSE;
			$lLoggedInUser = $logged_in_user;
		break;
	    		
		case "2":
		case "3":
		case "4":
		case "5": // This code is fairly secure
			$lEnableHTMLControls = TRUE;
			$lEncodeOutput = TRUE;
			$lProtectAgainstMethodTampering = TRUE;
			$lHTTPParameterPollutionDetected = isParameterPollutionDetected($_SERVER['QUERY_STRING']);
			$lLoggedInUser = $MySQLHandler->escapeDangerousCharacters($logged_in_user);
   		break;
   	}// end switch		

   	if ($lEnableHTMLControls) {
   		$lHTMLControlAttributes='required="required"';
   	}else{
   		$lHTMLControlAttributes="";
   	}// end if
   	   	
   	$lNewCSRFTokenForNextRequest = $lCSRFTokenHandler->generateCSRFToken();
   	   	
   	// initialize message
   	$lUserChoiceMessage = "No choice selected";
   	$lUserInitials ="";

   	// determine if user clicked the submit buttton
   	if(!$lProtectAgainstMethodTampering){
   		$lFormSubmitted = isSet($_REQUEST["user-poll-php-submit-button"]);
   	}else{
   		$lFormSubmitted = isSet($_GET["user-poll-php-submit-button"]);
   	}//end if   

   	// if user clicked submit button, process input parameters
   	if($lFormSubmitted){
   		try{
	   		// if we want to enforce GET method, we need to be careful to specify $_GET
		   	if(!$lProtectAgainstMethodTampering){
		   		$lUserChoice = $_REQUEST["choice"];
		   		$lUserInitials = $_REQUEST["initials"];
				$lPostedCSRFToken = $_REQUEST["csrf-token"];
		   	}else{
		   		$lUserChoice = $_GET["choice"];
		   		$lUserInitials = $_GET["initials"];
		   		$lPostedCSRFToken = $_GET["csrf-token"];
		   	}//end if

		   	if (!$lCSRFTokenHandler->validateCSRFToken($lPostedCSRFToken)){
		   		throw (new Exception("Security Violation: Cross Site Request Forgery attempt detected.", 500));
		   	}// end if

			// if parameter pollution is not detected, print user choice 
		   	if (!$lHTTPParameterPollutionDetected){
				$lUserChoiceMessage = "Your choice was {$lUserChoice}";
				$LogHandler->writeToLog("User voted for {$lUserChoice}");
		   	}// end if

		   	// Encode output to protect against cross site scripting
		   	if ($lEncodeOutput){
		   		$lUserInitials = $Encoder->encodeForHTML($lUserInitials);
		   		$lUserChoice = $Encoder->encodeForHTML($lUserChoice);
	   			$lUserChoiceMessage = $Encoder->encodeForHTML($lUserChoiceMessage);
		   	}// end if
		   	
		   	//Insert vote into database
		   	try {
		   		$SQLQueryHandler->insertVoteIntoUserPoll($lUserChoice, $lLoggedInUser);
		   	} catch (Exception $e) {
		   		echo $CustomErrorHandler->FormatError($e, "Error inserting user vote for " . $lLoggedInUser);
		   	}//end try

	   	} catch (Exception $e) {
	   		echo $CustomErrorHandler->FormatError($e, "Vote was not counted");
	   	}// end try

   	}//end if lFormSubmitted
?>

<div class="page-title">User Poll</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<fieldset>
	<legend>User Poll</legend>
	<form 	action="index.php" 
			method="GET"
			enctype="application/x-www-form-urlencoded" 
			id="idPollForm">
		<input type="hidden" name="page" value="user-poll.php" />
		<input name="csrf-token" type="hidden" value="<?php echo $lNewCSRFTokenForNextRequest; ?>" />
		<table>
			<tr id="id-bad-vote-tr" style="display: none;">
				<td class="error-message">
					Validation Error: HTTP Parameter Pollution Detected. Vote cannot be trusted.
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td id="id-poll-form-header-td" class="form-header">Choose Your Favorite Security Tool</td>
			</tr>
			<tr><td></td></tr>
			<tr><th class="label">Initial your choice to make your vote count</th></tr>
			<tr><td></td></tr>
			<tr>
				<td>
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="nmap" checked="checked" />&nbsp;&nbsp;nmap<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="wireshark" />&nbsp;&nbsp;wireshark<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="tcpdump" />&nbsp;&nbsp;tcpdump<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="netcat" />&nbsp;&nbsp;netcat<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="metasploit" />&nbsp;&nbsp;metasploit<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="kismet" />&nbsp;&nbsp;kismet<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="Cain" />&nbsp;&nbsp;Cain<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="Ettercap" />&nbsp;&nbsp;Ettercap<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="Paros" />&nbsp;&nbsp;Paros<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="Burp Suite" />&nbsp;&nbsp;Burp Suite<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="Sysinternals" />&nbsp;&nbsp;Sysinternals<br />
					<input name="choice" id="id_choice" type="radio" <?php echo $lHTMLControlAttributes ?> value="inSIDDer" />&nbsp;&nbsp;inSIDDer
				</td>
			</tr>
			<tr>
				<td class="label">
					Your Initials:<input type="text" name="initials" <?php echo $lHTMLControlAttributes ?> value="<?php echo $lUserInitials; ?>"/>
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td style="text-align:center;">
					<input name="user-poll-php-submit-button" class="button" type="submit" value="Submit Vote" />
				</td>
			</tr>
			<tr><td></td></tr>
			<tr><td></td></tr>
			<tr>
				<td class="report-header">
				<?php echo $lUserChoiceMessage; ?>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<?php
	try{// to draw table
		//Get votes from database
		try {
			$lQueryResult = $SQLQueryHandler->getUserPollVotes();
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Error getting user votes");
		}//end try
		
		if($lQueryResult->num_rows > 0){

			// we have rows. Begin drawing output.
			echo '<br/>';
			echo '<fieldset>';
			echo '<legend>Poll Results</legend>';
			echo '<table style="width:50%;" class="results-table">';
			echo '<tr class="report-header"><th class="report-label" colspan="2">'.$lQueryResult->num_rows.' Records Found</th></tr>';
		    echo '<tr class="report-header">
				    <th class="report-label">Tool</td>
				    <th class="report-label">Votes</td>
			    </tr>';
	
		    $lRowNumber = 0;
		    while($row = $lQueryResult->fetch_object()){
		    	$lRowNumber++;
			
				if(!$lEncodeOutput){
					$lToolName = $row->tool_name;
					$lToolCount = $row->tool_count;
				}else{
					$lToolName = $Encoder->encodeForHTML($row->tool_name);
					$lToolCount = $Encoder->encodeForHTML($row->tool_count);
				}// end if
								
				echo "<tr>
						<th class=\"report-label\">{$lToolName}</th>
						<td class=\"report-data\">{$lToolCount}</td>
					</tr>\n";
			}//end while $row
			echo '</table>';
			echo '</fieldset>';
		}//end if
		
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error writing rows.");
	}// end try;
?>

<script type="text/javascript">
	try{
		document.getElementById("id_choice").focus();
	}catch(e){
		alert('Error trying to set focus on field choice: ' + e.message);
	}// end try
</script>

<?php
	if ($lHTTPParameterPollutionDetected) {
		echo '<script>document.getElementById("id-bad-vote-tr").style.display="";</script>'; 
	}// end if ($lHTTPParameterPollutionDetected)
?>

<?php
	if ($lFormSubmitted) {
		echo $lCSRFTokenHandler->generateCSRFHTMLReport();
	}// end if
?>