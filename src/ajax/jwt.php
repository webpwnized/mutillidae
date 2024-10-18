<?php
	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	include_once '../includes/constants.php';

	/* ------------------------------------------------------
	 * INCLUDE CLASS DEFINITION PRIOR TO INITIALIZING SESSION
	 * ------------------------------------------------------ */
	require_once __SITE_ROOT__.'/classes/CustomErrorHandler.php';
	require_once __SITE_ROOT__.'/classes/LogHandler.php';
	require_once __SITE_ROOT__.'/classes/SQLQueryHandler.php';
	require_once __SITE_ROOT__.'/classes/JWT.php';

   /* ------------------------------------------
   * INITIALIZE SESSION
   * ------------------------------------------ */
	if (session_status() == PHP_SESSION_NONE){
	    session_start();
	}// end if

	// user session required to have security-level available
	if(!isset($_SESSION["uid"]) || !is_numeric($_SESSION["uid"])) {
		echo '<p>Not logged in. Please <a href="index.php?page=login.php">login/register</a> first...</p>';
		return;
	}

	/* ------------------------------------------
	 * initialize custom error handler
	 * ------------------------------------------ */
	$CustomErrorHandler = new CustomErrorHandler($_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize log handler
 	* ------------------------------------------ */
	$LogHandler = new LogHandler($_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize SQLQuery handler
 	* ------------------------------------------ */
	$SQLQueryHandler = new SQLQueryHandler($_SESSION["security-level"]);

	try {
    	switch ($_SESSION["security-level"]){
			default:
    		case "0": // This code is insecure.
				$lEnableSignatureValidation = false;
				$lKey = 'snowman';
				$lObfuscatePassword = false;
				break;
    		case "1": // This code is insecure.
				$lEnableSignatureValidation = true;
				$lKey = 'snowman';
				$lObfuscatePassword = false;
				break;
   		case "2":
   		case "3":
   		case "4":
    		case "5": // This code is fairly secure
				$lEnableSignatureValidation = true;
				$lKey = 'MIIBPAIBAAJBANBs46xCKgSt8vSgpGlDH0C8znhqhtOZQQjFCaQzcseGCVlrbI';
				$lObfuscatePassword = true;
			break;
    	}// end switch
	}catch(Exception $e){
		$lErrorMessage = "Error setting up configuration on page ajax/jwt.php";"Error setting up configuration on page ajax/jwt.php";
		echo $CustomErrorHandler->getExceptionMessage($e, $lErrorMessage);
	}// end try

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$token = $_SERVER['HTTP_AUTHTOKEN'];
		
		if(strlen(trim($token)) == 0) {
			echo "<p>Error: Expecting JWT in AuthToken header, but none received.";
		}

		try {
			$decoded = JWT::decode($token, $lKey, $lEnableSignatureValidation);
		}catch(Exception $e){
			echo ($CustomErrorHandler->getExceptionMessage($e, 'Error decoding/validating token on page ajax/jwt.php'));
			return;
		}

		// get user info from DB
		$userInfo = $SQLQueryHandler->getUserAccountByID($decoded->userid);
		$lUserDetailsJSON = "";
		while($row = $userInfo->fetch_object()) {
			if($lObfuscatePassword) { $row->password = "********"; }
			$lUserDetailsJSON .= json_encode($row);
		}
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		echo $lUserDetailsJSON;
	}
?>
