<?php
	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	include_once('../includes/constants.php');

	/* ------------------------------------------------------
	 * INCLUDE CLASS DEFINITION PRIOR TO INITIALIZING SESSION
	 * ------------------------------------------------------ */
	require_once (__ROOT__ . '/classes/CustomErrorHandler.php');
	require_once (__ROOT__ . '/classes/LogHandler.php');
	require_once (__ROOT__ . '/classes/SQLQueryHandler.php');
	require_once (__ROOT__ . '/classes/JWT.php');

    /* ------------------------------------------
     * INITIALIZE SESSION
     * ------------------------------------------ */
    if(strlen(session_id()) == 0){
    	session_start();
    }// end if

	/* ------------------------------------------
	 * initialize custom error handler
	 * ------------------------------------------ */
	$CustomErrorHandler = new CustomErrorHandler("../owasp-esapi-php/src/", $_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize log handler
 	* ------------------------------------------ */
	$LogHandler = new LogHandler("../owasp-esapi-php/src/", $_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize SQLQuery handler
 	* ------------------------------------------ */
	$SQLQueryHandler = new SQLQueryHandler("../owasp-esapi-php/src/", $_SESSION["security-level"]);

	try {
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure.
				$lEnableSignatureValidation = FALSE;
				$lKey = 'Butterfly';
				break;
    		case "1": // This code is insecure.
				$lEnableSignatureValidation = TRUE;
				$lKey = 'Butterfly';
				break;
   		case "2":
   		case "3":
   		case "4":
    		case "5": // This code is fairly secure
				$lEnableSignatureValidation = TRUE;
				$lKey = 'MIIBPAIBAAJBANBs46xCKgSt8vSgpGlDH0C8znhqhtOZQQjFCaQzcseGCVlrbI';
			break;
    	}// end switch
	}catch(Exception $e){
		echo $CustomErrorHandler->getExceptionMessage($e, "Error setting up configuration on page ajax/jwt.php");
	}// end try

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$token = $_SERVER['HTTP_AUTHTOKEN'];
		if(strlen(trim($token)) == 0) {
			echo "<p>Error: Expecting JWT in AuthToken header, but none received.";
		}
		try {
			$decoded = JWT::decode($token, $lKey, $lEnableSignatureValidation);
		}catch(Exception $e){
			echo ($CustomErrorHandler->getExceptionMessage($e, 'Test12'));
			return;
		}

		// get user info from DB
		$userInfo = $SQLQueryHandler->getUserAccountByID($decoded->userid);
		$table  = "<table>";
		$lUserDetailsJSON = "";
		while($row = $userInfo->fetch_object()) {
			$lUserDetailsJSON .= json_encode($row);
			$table .= "<tr><td>CID</td><td>" . $row->cid . "</td></tr>";
			$table .= "<tr><td>User Name</td><td>" . $row->username . "</td></tr>";
			$table .= "<tr><td>Password</td><td style='content: ***'>" . $row->password . "</td></tr>";
			$table .= "<tr><td>Signature</td><td>" . $row->mysignature . "</td></tr>";
			$table .= "<tr><td>Is Admin</td><td>" . $row->is_admin . "</td></tr>";
			$table .= "<tr><td>First Name</td><td>" . $row->firstname . "</td></tr>";
			$table .= "<tr><td>Last Name</td><td>" . $row->lastname . "</td></tr>";
		}
		$table .= "</table>";
		//echo $table;
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		echo $lUserDetailsJSON;
	}
?>
