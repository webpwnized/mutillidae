<?php

	function logLoginAttempt($lMessage){
		try {
			global $LogHandler;
			$LogHandler->writeToLog($lMessage);
		} catch (Exception $e) {
			/*do nothing*/
		};
	};//end function logLoginAttempt

    try {
		$lQueryString = "";
    	switch ($_SESSION["security-level"]){
	   		case "0": // This code is insecure
	   		case "1": // This code is insecure
				/*
				 * Grab username and password from parameters.
				 * Notice in insecure mode, we take parameters from "REQUEST" which
				 * could be GET OR POST. This is not correct. The page
				 * intends to receive parameters from POST and should
				 * restrict parameters to POST only.
				 */
				$lUsername = $_REQUEST["username"];
				$lPassword = $_REQUEST["password"];
	   			$lProtectCookies = FALSE;
	   			$lConfidentialityRequired = FALSE;
	   		break;

			case "2":
			case "3":
			case "4":
	   		case "5": // This code is fairly secure
	   			/* Restrict paramters to POST */
				$lUsername = $_POST["username"];
				$lPassword = $_POST["password"];
	   			$lProtectCookies = TRUE;
	   			$lConfidentialityRequired = TRUE;
	   		break;
	   	}// end switch

	   	$cUNSURE = -1;
	   	$cACCOUNT_DOES_NOT_EXIST = 0;
	   	$cPASSWORD_INCORRECT = 1;
	   	$cNO_RESULTS_FOUND = 2;
	   	$cAUTHENTICATION_SUCCESSFUL = 3;
	   	$cAUTHENTICATION_EXCEPTION_OCCURED = 4;
	   	$cUSERNAME_OR_PASSWORD_INCORRECT = 5;

	   	$lAuthenticationAttemptResult = $cUNSURE;
	   	$lAuthenticationAttemptResultFound = FALSE;
	   	$lKeepGoing = TRUE;
	   	$lQueryResult=NULL;

   		logLoginAttempt("User {$lUsername} attempting to authenticate");

   		if (!$SQLQueryHandler->accountExists($lUsername)){
   		    if ($lConfidentialityRequired){
   		        $lAuthenticationAttemptResult = $cUSERNAME_OR_PASSWORD_INCORRECT;
   		    }else{
   		        $lAuthenticationAttemptResult = $cACCOUNT_DOES_NOT_EXIST;
   		    }// end if
   			$lKeepGoing = FALSE;
   			logLoginAttempt("Login Failed: Account {$lUsername} does not exist");
   		}// end if accountExists

		if ($lKeepGoing){
   			if (!$SQLQueryHandler->authenticateAccount($lUsername, $lPassword)){
   			    if ($lConfidentialityRequired){
   			        $lAuthenticationAttemptResult = $cUSERNAME_OR_PASSWORD_INCORRECT;
   			    }else{
   			        $lAuthenticationAttemptResult = $cPASSWORD_INCORRECT;
   			    }// end if
	   			$lKeepGoing = FALSE;
	   			logLoginAttempt("Login Failed: Password for {$lUsername} incorrect");
	   		}//end if authenticateAccount
   		}//end if $lKeepGoing

		$lQueryResult = $SQLQueryHandler->getUserAccount($lUsername, $lPassword);

		if (isset($lQueryResult->num_rows)){
   			if ($lQueryResult->num_rows > 0) {
	   			$lAuthenticationAttemptResultFound = TRUE;
   			}//end if
		}//end if

		if ($lAuthenticationAttemptResultFound){
			$lRecord = $lQueryResult->fetch_object();
			$_SESSION['loggedin'] = 'True';
			$_SESSION['uid'] = $lRecord->cid;
			$_SESSION['logged_in_user'] = $lRecord->username;
			$_SESSION['logged_in_usersignature'] = $lRecord->mysignature;
			$_SESSION['is_admin'] = $lRecord->is_admin;

   				/*
   				 /* Set client-side auth token. if we are in insecure mode, we will
   				* pay attention to client-side authorization tokens. If we are secure,
   				* we dont use client-side authortization tokens and we ignore any
   				* attempts to use them.
   				*
   				* If in secure mode, we want the cookie to be protected
   				* with HTTPOnly flag. There is some irony here. In secure code,
   				* we are to ignore authorization cookies, so we are protecting
   				* a cookie we know we are going to ignore. But the point is to
   				* provide an example to developers of proper coding techniques.
   				*
   				* Note: Ideally this cookie must be protected with SSL also but
   				* again this is just a demo. Once your in SSL mode, maintain SSL
   				* and escalate any requests for HTTP to HTTPS.
   				*/
			if ($lProtectCookies){
				$lUsernameCookie = $Encoder->encodeForURL($lRecord->username);
				$l_cookie_options = array (
				    'expires' => 0,              // 0 means session cookie
				    'path' => '/',               // '/' means entire domain
				    //'domain' => '.example.com', // default is current domain
				    'secure' => FALSE,           // true or false
				    'httponly' => TRUE,         // true or false
				    'samesite' => 'Strict'          // None || Lax  || Strict
				);
				setcookie("username", $lUsernameCookie, $l_cookie_options);
				setcookie("uid", $lRecord->cid, $l_cookie_options);
			}else {
				//setrawcookie() allows for response splitting
				$lUsernameCookie = $lRecord->username;
				$l_cookie_options = array (
				    'expires' => 0,              // 0 means session cookie
				    'path' => '/',               // '/' means entire domain
				    //'domain' => '.example.com', // default is current domain
				    'secure' => FALSE,           // true or false
				    'httponly' => FALSE,         // true or false
				    'samesite' => 'Lax'          // None || Lax  || Strict
				);
				setrawcookie("username", $lUsernameCookie, $l_cookie_options);
				setrawcookie("uid", $lRecord->cid, $l_cookie_options);
			}// end if $lProtectCookies

			logLoginAttempt("Login Succeeded: Logged in user: {$lRecord->username} ({$lRecord->cid})");

			$lAuthenticationAttemptResult = $cAUTHENTICATION_SUCCESSFUL;

			/* Redirect back to the home page and exit to stop adding to HTTP response*/
			header('Location: index.php?popUpNotificationCode=AU1', true, 302);
			exit(0);

		}// end if $lAuthenticationAttemptResultFound

   	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error querying user account");
		$lAuthenticationAttemptResult = $cAUTHENTICATION_EXCEPTION_OCCURED;
	}// end try;

?>