<?php

    function logMessage($lMessage) {
        try {
            global $LogHandler;
            $LogHandler->writeToLog($lMessage);
        } catch (Exception $e) {
            /* do nothing */
        }
    }

    try {
        $lQueryString = "";
        switch ($_SESSION["security-level"]) {
            default: // Default case: This code is insecure
            case "0": // This code is insecure
            case "1": // This code is insecure
                $lUsername = $_REQUEST["username"];
                $lPassword = $_REQUEST["password"];
                $lProtectCookies = false;
                $lConfidentialityRequired = false;
				$lProtectAgainstRedirectionAttacks = false;
                break;

            case "2":
            case "3":
            case "4":
            case "5": // This code is fairly secure
                $lUsername = $_POST["username"];
                $lPassword = $_POST["password"];
                $lProtectCookies = true;
                $lConfidentialityRequired = true;
				$lProtectAgainstRedirectionAttacks = true;
                break;
        }

        $cUNSURE = -1;
        $cAUTHENTICATION_SUCCESSFUL = 3;

        $lAuthenticationAttemptResult = $cUNSURE;
        $lAuthenticationAttemptResultFound = false;
        $lKeepGoing = true;

        logMessage("User {$lUsername} attempting to authenticate");

        if (!$SQLQueryHandler->accountExists($lUsername)) {
            $lAuthenticationAttemptResult = $lConfidentialityRequired ?
                $cUSERNAME_OR_PASSWORD_INCORRECT :
                $cACCOUNT_DOES_NOT_EXIST;
            $lKeepGoing = false;
            logMessage("Login Failed: Account {$lUsername} does not exist");
        }

        if ($lKeepGoing && !$SQLQueryHandler->authenticateAccount($lUsername, $lPassword)) {
            $lAuthenticationAttemptResult = $lConfidentialityRequired ?
                $cUSERNAME_OR_PASSWORD_INCORRECT :
                $cPASSWORD_INCORRECT;
            $lKeepGoing = false;
            logMessage("Login Failed: Password for {$lUsername} incorrect");
        }

        $lQueryResult = $SQLQueryHandler->getUserAccount($lUsername, $lPassword);

        if (isset($lQueryResult->num_rows) && $lQueryResult->num_rows > 0) {
            $lAuthenticationAttemptResultFound = true;
        }

        if ($lAuthenticationAttemptResultFound) {
            $lRecord = $lQueryResult->fetch_object();
            $_SESSION["user_is_logged_in"] = true;
            $_SESSION["uid"] = $lRecord->cid;
            $_SESSION["logged_in_user"] = $lRecord->username;
            $_SESSION["logged_in_user_signature"] = $lRecord->mysignature;
            $_SESSION["is_admin"] = $lRecord->is_admin;

            if ($lProtectCookies) {
                $lUsernameCookie = $Encoder->encodeForURL($lRecord->username);
                $l_cookie_options = array(
                    'expires' => 0,
                    'path' => '/',
                    'secure' => false,
                    'httponly' => true,
                    'samesite' => 'Strict'
                );
                setcookie("username", $lUsernameCookie, $l_cookie_options);
                setcookie("uid", $lRecord->cid, $l_cookie_options);
            } else {
                $l_cookie_options = array(
                    'expires' => 0,
                    'path' => '/',
                    'secure' => false,
                    'httponly' => false,
                    'samesite' => 'Lax'
                );
                setrawcookie("username", $lRecord->username, $l_cookie_options);
                setrawcookie("uid", $lRecord->cid, $l_cookie_options);
            }

            logMessage("Login Succeeded: Logged in user: {$lRecord->username} ({$lRecord->cid})");
            $lAuthenticationAttemptResult = $cAUTHENTICATION_SUCCESSFUL;

            // Check for 'redirect' query parameter
			$lBaseRedirectUrl = "index.php?popUpNotificationCode=AU1";

			// Check if 'redirectPage' exists and has a non-empty value
			if (isset($_POST['redirectPage']) && !empty($_POST['redirectPage'])) {
				$lRedirectUrl = $lBaseRedirectUrl . "&page=" . $_POST['redirectPage'];
			} else {
				// If 'redirectPage' is not set or empty, use only the base URL
				$lRedirectUrl = $lBaseRedirectUrl;
			}

			// Log the redirect attempt, regardless of whether it is valid or not.
			logMessage("Redirect attempt to: $lRedirectUrl");

			// Validate the redirect page if protection against redirection attacks is enabled.
			if ($lProtectAgainstRedirectionAttacks &&
				!preg_match('/^index\.php\?popUpNotificationCode=AU1(&page=[a-zA-Z0-9_-]+\.php)?$/', $lRedirectUrl)) {
				
				// Log the invalid redirect attempt.
				logMessage("Invalid redirect detected. Redirecting to home page: $lBaseRedirectUrl");

				// Fallback to home page if the redirect URL is invalid.
				$lRedirectUrl = $lBaseRedirectUrl;
			} else {
				// Log redirect.
				logMessage("Redirecting to: $lRedirectUrl");
			}

			// Perform the redirect and terminate the script to prevent further output.
			header("Location: $lRedirectUrl", true, 302);
			exit(0);
		}

    } catch (Exception $e) {
        $lErrorMessage = "Error querying user account";
        echo $CustomErrorHandler->FormatError($e, $lErrorMessage);
        $lAuthenticationAttemptResult = $cAUTHENTICATION_EXCEPTION_OCCURED;
    }

?>
