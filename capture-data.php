<?php
	 /* Known Vulnerabilities
	 * 
	 * SQL Injection, (Fix: Use Schematized Stored Procedures)
	 * Cross Site Scripting, (Fix: Encode all output)
	 * Cross Site Request Forgery, (Fix: Tokenize transactions)
	 * Denial of Service, (Fix: Truncate Log Queries)
	 * Improper Error Handling, (Fix: Employ custom error handler)
	 * SQL Exception, (Fix: Employ custom error handler)
	 */

	/* We use the session on this page */
	if (!isset($_SESSION["security-level"])){
		session_start();	
	}// end if
	
	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	require_once ('./includes/constants.php');
	require_once(__ROOT__.'/includes/minimum-class-definitions.php');
	
	/* ------------------------------------------
 	* initialize balloon-hint handler
 	* ------------------------------------------ */
	require_once (__ROOT__.'/classes/BubbleHintHandler.php');
	$BubbleHintHandler = new BubbleHintHandler(__ROOT__."/owasp-esapi-php/src/", $_SESSION["security-level"]);
		
	/* ------------------------------------------
 	* initialize Client Information Handler
 	* ------------------------------------------ */
	require_once (__ROOT__.'/classes/ClientInformationHandler.php');
	$lClientInformationHandler = new ClientInformationHandler();

	try {	    	
		switch ($_SESSION["security-level"]){
	   		case "0": // this code is insecure
	   		case "1": // this code is insecure 
				$lProtectAgainstSQLInjection = FALSE;
	   		break;//case "0"
	    		
	   		case "2":
	   		case "3":
	   		case "4":	
	   		case "5": // This code is fairly secure
				$lProtectAgainstSQLInjection = TRUE;
	   		break;//case "5"
	   	}// end switch ($_SESSION["security-level"])
			
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
	}// end try		
	
	try {
		/* Grab as much information about visiting browser as possible. Most of this
		 * is available in the HTTP request header.
		 */
		$lClientHostname = $lClientInformationHandler->getClientHostname();
		$lClientIP = $lClientInformationHandler->getClientIP();
		$lClientUserAgentString = $lClientInformationHandler->getClientUserAgentString();
		$lClientReferrer = $lClientInformationHandler->getClientReferrer();
		$lClientPort = $lClientInformationHandler->getClientPort();
		
		if ($lProtectAgainstSQLInjection) {
			$lClientHostname = $MySQLHandler->escapeDangerousCharacters($lClientHostname);
			$lClientUserAgentString = $MySQLHandler->escapeDangerousCharacters($lClientUserAgentString);
			$lClientReferrer = $MySQLHandler->escapeDangerousCharacters($lClientReferrer);
		}// end if $lProtectAgainstSQLInjection	
		
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
	}// end try
		
	try {	    	
	   	// Declare a temp varaible to hold our collected data
	   	$lCapturedData = "";

	   	// Capture GET parameters
		foreach ( $_GET as $k => $v ) {
			$lCapturedData .= "$k = $v" . PHP_EOL;
		}// end for each

		// Capture POST parameters
		foreach ( $_POST as $k => $v ) {
			$lCapturedData .= "$k = $v" . PHP_EOL;
		}// end for each
		
		//Capture cookies
		foreach ( $_COOKIE as $k => $v ) {
			$lCapturedData .= "$k = $v" . PHP_EOL;
		}// end for each
		
		$SQLQueryHandler->insertCapturedData($lClientIP, $lClientHostname, $lClientPort, $lClientUserAgentString, $lClientReferrer, $lCapturedData);
		
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
	}// end try		

	$lFilename = "captured-data.txt";
	$lFilepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.$lFilename;
	try{
		$lmDateTime = new DateTime();
		$lCurrentDateTimeArray = getdate();
		$lCurrentDateTime = date('m-d-Y H:i:s', mktime($lCurrentDateTimeArray['hours'], $lCurrentDateTimeArray['minutes'], $lCurrentDateTimeArray['seconds'], $lCurrentDateTimeArray['mon'], $lCurrentDateTimeArray['mday'], $lCurrentDateTimeArray['year']));
		$lFileHandle = fopen($lFilepath, "a");
		if($lFileHandle){
			fwrite($lFileHandle, PHP_EOL);
			fwrite($lFileHandle, "--------------------------------------------------".PHP_EOL);
			fwrite($lFileHandle, "Client IP: ".$lClientIP.PHP_EOL);
			fwrite($lFileHandle, "Timestamp: ".$lCurrentDateTime." GMT".PHP_EOL);
			fwrite($lFileHandle, "--------------------------------------------------".PHP_EOL);
			fwrite($lFileHandle, "Client Hostname: ".$lClientHostname.PHP_EOL);
			fwrite($lFileHandle, "Client User Agent: ".$lClientUserAgentString.PHP_EOL);
			fwrite($lFileHandle, "Client Referrer: ".$lClientReferrer.PHP_EOL);
			fwrite($lFileHandle, "Client Port: ".$lClientPort.PHP_EOL);
			fwrite($lFileHandle, "Captured Data: ".$lCapturedData);
			fwrite($lFileHandle, "--------------------------------------------------".PHP_EOL);
			fwrite($lFileHandle, PHP_EOL);
			fclose($lFileHandle);
		}else{
			throw new Exception("Error attempting to record captured data to file " . $lFilepath . ". " . print_r(error_get_last()));
		}
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error trying to save captured data from capture.php into file ");
	}// end try
	
	try {
		$LogHandler->writeToLog("Captured user data");
		$LogHandler->writeToLog("Captured Client IP: ".$lClientIP);
		$LogHandler->writeToLog("Captured Client Hostname: ".$lClientHostname);
		$LogHandler->writeToLog("Captured Client User Agent: ".$lClientUserAgentString);
		$LogHandler->writeToLog("Captured Client Referrer: ".$lClientReferrer);
		$LogHandler->writeToLog("Captured Client Port: ".$lClientPort);
		$LogHandler->writeToLog("Captured Data: ".$lCapturedData);		
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $query);
	}// end try
	
    /* ------------------------------------------
     * LOG USER VISIT TO PAGE
     * ------------------------------------------ */
	include_once(__ROOT__."/includes/log-visit.php");
?>

<!-- Bubble hints code -->
<?php 
	try{
   		$lReflectedXSSExecutionPointBallonTip = $BubbleHintHandler->getHint("ReflectedXSSExecutionPoint");
   		$lSQLInjectionPointBallonTip = $BubbleHintHandler->getHint("SQLInjectionPoint");
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error attempting to execute query to fetch bubble hints.");
	}// end try	
?>

<script type="text/javascript">
	$(function() {
		$('[ReflectedXSSExecutionPoint]').attr("title", "<?php echo $lReflectedXSSExecutionPointBallonTip; ?>");
		$('[ReflectedXSSExecutionPoint]').balloon();
		$('[SQLInjectionPoint]').attr("title", "<?php echo $lSQLInjectionPointBallonTip; ?>");
		$('[SQLInjectionPoint]').balloon();
	});
</script>

<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
<div class="page-title">Capture Data</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<!-- BEGIN HTML OUTPUT  -->

<div>
	<a href="./index.php?page=captured-data.php" style="text-decoration: none;">
	<img style="vertical-align: middle;" src="./images/cage-48-48.png" />
	<span style="font-weight:bold; cursor: pointer;">&nbsp;View Captured Data</span>
	</a>
</div>
<table style="margin-left:auto; margin-right:auto; width: 650px;">
	<tr>
		<td class="form-header">Data Capture Page</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td SQLInjectionPoint="1">
			This page is designed to capture any parameters sent and store them in a file and a database table. It loops through
			the POST and GET parameters and records them to a file named <span class="label"><?php print $lFilename; ?></span>. On this system, the 
			file should be found at <span class="label"><?php print $lFilepath; ?></span>. The page
			also tries to store the captured data in a database table named captured_data and <a href="./index.php?page=show-log.php">logs</a> the captured data. There is another page named
			<a href="index.php?page=captured-data.php">captured-data.php</a> that attempts to list the contents of this table.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<th ReflectedXSSExecutionPoint="1">
			The data captured on this request is: <?php print $lCapturedData; ?>
		</th>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td style="text-align:center;">
			Would it be possible to hack the hacker? Assume the hacker will view the captured requests with a web browser.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>