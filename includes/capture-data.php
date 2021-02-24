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

    if(isset($_SESSION["security-level"])){
        $lSecurityLevel = $_SESSION["security-level"];
    }else{
        $lSecurityLevel = 0;
    }

	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
    require_once(dirname(__FILE__).'/constants.php');
	require_once(__ROOT__.'/includes/minimum-class-definitions.php');

	/* ------------------------------------------
 	* initialize Client Information Handler
 	* ------------------------------------------ */
	require_once (__ROOT__.'/classes/ClientInformationHandler.php');
	$lClientInformationHandler = new ClientInformationHandler();

	try {
	    switch ($lSecurityLevel){
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
		    $lClientHostname = $SQLQueryHandler->escapeDangerousCharacters($lClientHostname);
		    $lClientUserAgentString = $SQLQueryHandler->escapeDangerousCharacters($lClientUserAgentString);
		    $lClientReferrer = $SQLQueryHandler->escapeDangerousCharacters($lClientReferrer);
		}// end if $lProtectAgainstSQLInjection

	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
	}// end try

	$lCapturedData = "";

	try {
	   	// Declare a temp varaible to hold our collected data

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

	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
	}// end try

	try {
	    //Capture any JSON, XML or other non-name-value pair input
	    $lCapturedData .= file_get_contents('php://input');
	} catch (Exception $e) {
	    echo $CustomErrorHandler->FormatError($e, $lQueryString);
	}// end try

	try {
	    if (!empty($lCapturedData)) {
	        $SQLQueryHandler->insertCapturedData($lClientIP, $lClientHostname, $lClientPort, $lClientUserAgentString, $lClientReferrer, $lCapturedData);
	    }
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