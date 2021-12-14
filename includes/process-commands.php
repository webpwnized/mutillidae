<?php

    function logMessage($lMessage){
        try {
            global $LogHandler;
            $LogHandler->writeToLog($lMessage);
        } catch (Exception $e) {
            /*do nothing*/
        };
    };//end function logMessage

   	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   		case "1": // This code is insecure
   			$lProtectCookies = FALSE;
   		break;

		case "2":
		case "3":
		case "4":
   		case "5": // This code is fairly secure
   			$lProtectCookies = TRUE;
   		break;
   	}// end switch

    /* Precondition: $_REQUEST["do"] is not NULL */
    switch ($_REQUEST["do"]) {

		case "toggle-enforce-ssl":
    		if ($_SESSION["EnforceSSL"] == "False"){
    			$_SESSION["EnforceSSL"] = "True";
    			$lhintsPopUpNotificationCode = "SSLE1";
    		}else{
    			//default to false
    			$_SESSION["EnforceSSL"] = "False";
       			$lhintsPopUpNotificationCode = "SSLO1";
    		}//end if
		    header("Location: ".$_SERVER['SCRIPT_NAME'].'?popUpNotificationCode='.$lhintsPopUpNotificationCode.'&'.str_ireplace('do=toggle-enforce-ssl&', '', $_SERVER['QUERY_STRING']), true, 302);
			exit();
    	break;//case "toggle-enforce-ssl"

    	case "logout":
    	    logMessage("Logout user: {$_SESSION['logged_in_user']} ({$_SESSION['uid']})");
		    $_SESSION["loggedin"] = "False";
		    $_SESSION['logged_in_user'] = '';
		    $_SESSION['logged_in_usersignature'] = '';
			$_SESSION['uid'] = '';
			$_SESSION['is_admin'] = 'FALSE';
	    	setcookie("uid", "deleted", time()-3600);
	    	setcookie("username", "deleted", time()-3600);
	    	header("Location: index.php?page=login.php&popUpNotificationCode=LOU1", TRUE, 302);
	    	exit(0);
	    break;//case "logout"

		case "toggle-hints":
			/*
			 * Grab current cookie. The cookie might be legitimate or the user
			 * might have changed it
			*/
			$l_showhints = $_COOKIE["showhints"];

			/* Make hints either increase one level or roll over to zero. */
			$l_showhints += 1;
			if ($l_showhints > $C_MAX_HINT_LEVEL){
				$l_showhints = 0;
			}// end if

			/*
			 * If in secure mode, we want the cookie to be protected
			 * with HTTPOnly flag. There is some irony here. In secure code,
			 * we are to ignore authorization cookies, so we are protecting
			 * a cookie we know we are going to ignore. But the point is to
			 * provide an example to developers of proper coding techniques.
			 */
			$l_cookie_options = array (
			    'expires' => 0,              // 0 means session cookie
			    'path' => '/',               // '/' means entire domain
			    //'domain' => '.example.com', // default is current domain
			    'secure' => FALSE,           // true or false
			    'httponly' => FALSE,         // true or false
			    'samesite' => 'Lax'          // None || Lax  || Strict
			);
			if ($lProtectCookies){
			    /* The showhints cookie is unprotected on purpose because
			     * it is used in one of the lab assignments */
			    //$l_cookie_options['httponly'] = TRUE;
			    $l_cookie_options['samesite'] = 'Strict';
			}// end if
			setcookie('showhints', $l_showhints, $l_cookie_options);

			/* Guarantee that the hints cookie has the new hint level */
			//$_COOKIE["showhints"] = $l_showhints;

			/* Create pop up notification message for the jQuery growl notification */
			switch ($l_showhints){
				case 0: $lhintsPopUpNotificationCode = "L1H0";break;
				case 1: $lhintsPopUpNotificationCode = "L1H1";break;
				case 2: $lhintsPopUpNotificationCode = "L1H2";break;
			}//end switch

			/* Redirect the user back to the same page they clicked the "Toggle Hints" button
			 * The "exit()" function makes sure we do not accidentally write any "body" lines.
			 */
		    header("Location: ".$_SERVER['SCRIPT_NAME'].'?popUpNotificationCode='.$lhintsPopUpNotificationCode.'&'.str_ireplace('do=toggle-hints&', '', $_SERVER['QUERY_STRING']), true, 302);
			exit();
		break;//case "toggle-hints"

		case "toggle-security":
			/* Make security level go up a level or roll over*/
			$lSecurityLevel = $_SESSION["security-level"];

			if ($lSecurityLevel == '0') {
				$lSecurityLevel = '1';
			}else if($lSecurityLevel == '1'){
				$lSecurityLevel = '5';
			}else{
				$lSecurityLevel = '0';
		    }// end if

		    /* If we have looped back around to security level 0,
		     * show the hints again */
		    if ($lSecurityLevel == 0){
		    	$_SESSION["showhints"] = 1;
		    	$_SESSION["hints-enabled"] = "Enabled";

		    	$l_cookie_options = array (
		    	    'expires' => 0,              // 0 means session cookie
		    	    'path' => '/',               // '/' means entire domain
		    	    //'domain' => '.example.com', // default is current domain
		    	    'secure' => FALSE,           // true or false
		    	    'httponly' => FALSE,         // true or false
		    	    'samesite' => 'Lax'          // None || Lax  || Strict
		    	);
		    	setcookie('showhints', "1", $l_cookie_options);

		    }// end if

		    /* Disable hints unless we are on security level 0.
		     * There is a way to defeat this */
			if ($lSecurityLevel > 1){
		    	$_SESSION["showhints"] = 0;
				$_SESSION["hints-enabled"] = "Disabled";

				$l_cookie_options = array (
				    'expires' => 0,              // 0 means session cookie
				    'path' => '/',               // '/' means entire domain
				    //'domain' => '.example.com', // default is current domain
				    'secure' => FALSE,           // true or false
				    'httponly' => FALSE,         /* The showhints cookie is unprotected on purpose because
				                                  * it is used in one of the lab assignments */
				    'samesite' => 'Strict'          // None || Lax  || Strict
				);
				setcookie('showhints', "0", $l_cookie_options);
			}// end if

			// change how much information errors barf onto the page.
		    $CustomErrorHandler->setSecurityLevel($lSecurityLevel);
		    $LogHandler->setSecurityLevel($lSecurityLevel);
		   	$MySQLHandler->setSecurityLevel($lSecurityLevel);
		   	$SQLQueryHandler->setSecurityLevel($lSecurityLevel);
		   	$RemoteFileHandler->setSecurityLevel($lSecurityLevel);
		   	$RequiredSoftwareHandler->setSecurityLevel($lSecurityLevel);

		    $_SESSION["security-level"] = $lSecurityLevel;

			switch ($lSecurityLevel){
				case 0: $lhintsPopUpNotificationCode = "SL0";break;
				case 1: $lhintsPopUpNotificationCode = "SL1";break;
				case 5: $lhintsPopUpNotificationCode = "SL5";break;
			}//end switch

		    header("Location: ".$_SERVER['SCRIPT_NAME'].'?popUpNotificationCode='.$lhintsPopUpNotificationCode.'&'.str_ireplace('do=toggle-security&', '', $_SERVER['QUERY_STRING']), true, 302);
		    exit();
		break;//case "toggle-hints"

		default: break;
    }// end switch
?>