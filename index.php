<?php

	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	require_once ('./includes/constants.php');

	/* ------------------------------------------------------
	 * INCLUDE CLASS DEFINITION PRIOR TO INITIALIZING SESSION
	 * ------------------------------------------------------ */
	require_once (__ROOT__.'/owasp-esapi-php/src/ESAPI.php');
	require_once (__ROOT__.'/classes/MySQLHandler.php');
	require_once (__ROOT__.'/classes/SQLQueryHandler.php');
	require_once (__ROOT__.'/classes/CustomErrorHandler.php');
	require_once (__ROOT__.'/classes/LogHandler.php');
	require_once (__ROOT__.'/classes/RemoteFileHandler.php');
	require_once (__ROOT__.'/classes/RequiredSoftwareHandler.php');

    /* ------------------------------------------
     * INITIALIZE SESSION
     * ------------------------------------------ */
	if (session_status() == PHP_SESSION_NONE){
	    session_start();
	}// end if

	if (!isset($_SESSION["security-level"])){
	    $_SESSION["security-level"] = 0;
	}// end if

    /* ----------------------------------------------------
     * ENFORCE SSL
     * ----------------------------------------------------
     * If the user would like to enforce the use of SSL,
     * redirect any HTTP requests "up to" HTTPS. Otherwise
     * keep the URL the same.
     * ---------------------------------------------------- */
    if (!isset($_SESSION["EnforceSSL"])){
    	$_SESSION["EnforceSSL"] = "False";
    }// end if

    switch ($_SESSION["security-level"]){
    	case "0": // This code is insecure
    	case "1": // This code is insecure
		    if ($_SESSION["EnforceSSL"] == "True"){
		    	if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!="on"){
		    		$lSecureRedirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		    		header("Location: $lSecureRedirect");
		    		exit();
		    	}//end if
		    }//end if
   		break;

    	case "2":
    	case "3":
    	case "4":
    	case "5": // This code is fairly secure
		    if ($_SESSION["EnforceSSL"] == "True"){
		    	if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!="on"){
		    		require_once('ssl-enforced.php');
		    		exit();
		    	}//end if
		    }//end if
   		break;
    }// end switch

    /* ----------------------------------------------------
     * Initialize logged in status
     * ----------------------------------------------------
     * user is logged out by default
     */
    if (!isset($_SESSION["loggedin"])){
	    $_SESSION['loggedin'] = 'False';
	    $_SESSION['logged_in_user'] = '';
	    $_SESSION['logged_in_usersignature'] = '';
    }// end if

    /* ----------------------------------------------------
     * Check if user wants to disregard any detected
     * database errors
     * ----------------------------------------------------
     * user is logged out by default
     */
    if (!isset($_SESSION["UserOKWithDatabaseFailure"])) {
    	$_SESSION["UserOKWithDatabaseFailure"] = "FALSE";
    }// end if

    /* ----------------------------------------
     * initialize showhints session and cookie
     * ----------------------------------------
	 * This code is here to create a simulated vulnerability. Some
	 * sites put authorication and status tokens in cookies instead
	 * of the session. This is a mistake. The user controls the
	 * cookies entirely.
	*/
	if (isset($_COOKIE["showhints"])){
		$l_showhints = $_COOKIE["showhints"];
	}else{
		$l_showhints = 1;

		/*
		 * If in secure mode, we want the cookie to be protected
		 * with HTTPOnly flag. There is some irony here. In secure code,
		 * we are to ignore authorization cookies, so we are protecting
		 * a cookie we know we are going to ignore. But the point is to
		 * provide an example to developers of proper coding techniques.
		 */
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
	}// end if (isset($_COOKIE["showhints"])){

	if (!isset($_SESSION["showhints"]) || ($_SESSION["showhints"] != $l_showhints)){
		// make session = cookie
		$_SESSION["showhints"] = $l_showhints;
		switch ($l_showhints){
			case 0: $_SESSION["hints-enabled"] = "Disabled"; break;
			case 1: $_SESSION["hints-enabled"] = "Enabled"; break;
		}// end switch
	}//end if

	/* ------------------------------------------
	 * initialize OWASP ESAPI for PHP
	 * ------------------------------------------ */
	$ESAPI = new ESAPI(__ROOT__.'/owasp-esapi-php/src/ESAPI.xml');
	$Encoder = $ESAPI->getEncoder();
	$ESAPIRandomizer = $ESAPI->getRandomizer();

	/* ------------------------------------------
 	* Test for database availability
 	* ------------------------------------------ */

	function handleError($errno, $errstr, $errfile, $errline, array $errcontext){
		/*
		restore_error_handler();
		restore_exception_handler();
		header("Location: database-offline.php", true, 302);
		exit();
		*/
	}// end function

	function handleException($exception){
		//restore_error_handler();
		restore_exception_handler();
		header("Location: database-offline.php", true, 302);
		exit();
	}// end function

	if ($_SESSION["UserOKWithDatabaseFailure"] == "FALSE") {
		//set_error_handler('handleError', E_ALL & ~E_NOTICE);
		set_exception_handler('handleException');
	    	MySQLHandler::databaseAvailable();
		//restore_error_handler();
		restore_exception_handler();
	}//end if

	/* ------------------------------------------
	 * initialize custom error handler
	 * ------------------------------------------ */
	$CustomErrorHandler = new CustomErrorHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize log handler
 	* ------------------------------------------ */
	$LogHandler = new LogHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize MySQL handler
 	* ------------------------------------------ */
	$MySQLHandler = new MySQLHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	$MySQLHandler->connectToDefaultDatabase();

	/* ------------------------------------------
 	* initialize SQL Query handler
 	* ------------------------------------------ */
	$SQLQueryHandler = new SQLQueryHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize remote file handler
 	* ------------------------------------------ */
	$RemoteFileHandler = new RemoteFileHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);

	/* ------------------------------------------
	 * initialize required software handler
	* ------------------------------------------ */
	$RequiredSoftwareHandler = new RequiredSoftwareHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);

	/* ------------------------------------------
	* PROCESS REQUESTS
	* ------------------------------------------ */
	if (isset($_GET["do"])){
		include_once(__ROOT__.'/includes/process-commands.php');
	}// end if

	/* ------------------------------------------
	* PROCESS LOGIN ATTEMPT (IF ANY)
	* ------------------------------------------ */
	if (isset($_POST["login-php-submit-button"])){
		include_once(__ROOT__.'/includes/process-login-attempt.php');
	}// end if

	/* ------------------------------------------
     * REACT TO CLIENT SIDE CHANGES
     * ------------------------------------------ */
	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   		case "1": // This code is insecure
			/* Use the clients authorization token which is stored in
			 * the cookie (in this case). Placing authorization tokens
			 * on the client is fairly ridiculous.
			 *
			 * Known Vulnerabilities: SQL Injection, Authorization Bypass, Session Fixation,
			 * 	Lack of custom error page, Application Exception
			 */
   			if (isset($_COOKIE['uid'])){

				try{
					$lQueryResult = $SQLQueryHandler->getUserAccountByID($_COOKIE['uid']);

				    // Switch to whatever cookie the user sent to simulate sites
				    // that use client-side authorization tokens. Auth information
				    // should never be in cookies.
				    if ($lQueryResult->num_rows > 0) {
					    $row = $lQueryResult->fetch_object();
						$_SESSION['loggedin'] = 'True';
						$_SESSION['uid'] = $row->cid;
						$_SESSION['logged_in_user'] = $row->username;
						$_SESSION['logged_in_usersignature'] = $row->mysignature;
						$_SESSION['is_admin'] = $row->is_admin;
						if (isset($_SESSION['logged_in_user'])){
						    header('Logged-In-User: '.$_SESSION['logged_in_user'], true);
						}// end if
			    	}// end if ($result->num_rows > 0)

				} catch (Exception $e) {
			   		echo $CustomErrorHandler->FormatError($e, $lQueryString);
			   	}// end try
   			}else{
	   			/*
	   			 * Output the user's login name into a custom header
	   			 *
	   			 * Known Vulnerability: Potential HTTP Response Splitting
	   			 * (PHP defends itself against HTTP response splitting by
	   			 * filtering "new line" characters)
	   			 */
   			    if (isset($_SESSION['logged_in_user'])){
   			        header('Logged-In-User: '.$_SESSION['logged_in_user'], true);
   			    }// end if
   			}// end if

   		break;

   		case "2":
   		case "3":
   		case "4":
   		case "5": // This code is fairly secure
  			/* If we are secure, then we do not rely on any client input
  			 * to make authorization decisions. Authorization tokens should
  			 * never be stored on the client. We use the SESSION in secure mode.
  			 *
  			 * Also, when we create an HTTP header, we encode any output to
  			 * prevent response splitting. The critical chars in response splitting
  			 * are CR-LF. Dont fall for filtering. Just encode it all.
  			 */
   		    if (isset($_SESSION['logged_in_user'])){
   		        header('Logged-In-User: '.$Encoder->encodeForHTML($_SESSION['logged_in_user']), TRUE);
   		    }// end if
   		break;
   	}// end switch
	/* ------------------------------------------
     * END REACT TO CLIENT SIDE CHANGES
     * ------------------------------------------ */

   	/* ------------------------------------------
    * Security Headers (Modern Browsers)
    * ------------------------------------------ */
	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   		case "1":
   		    /* Built-in user-agent defenses */
   			header("X-XSS-Protection: 0;", TRUE);

   			/* Disable HSTS */
   			header("Strict-Transport-Security: max-age=0", TRUE);

   			// HTTP/1.1 cache control
   			header("Cache-Control: public", TRUE);

   			/* Referrer Policy */
   			header("Referrer-Policy: unsafe-url", TRUE);

   			header_remove("Pragma");

   			/* Content sniffing */
   			header_remove("X-Content-Type-Options");

   	    break;

   		case "2":
   		case "3":
   		case "4":
   		case "5": // This code is fairly secure
   		    /* Built-in user-agent defenses */
   		    header("X-XSS-Protection: 1; mode=block;", TRUE);

   		    /* Enable HSTS - I would like to enable this but the problem is this header caches so messes
   		     * up labs once the user sets the security level back to level 0*/
   		    //header("Strict-Transport-Security: max-age=31536000; includeSubDomains", TRUE);

   		    // HTTP/1.1 cache control
   		    header('Cache-Control: no-store, no-cache', TRUE);

   		    // HTTP/1.0 cache-control
   		    header("Pragma: no-cache", TRUE);

   		    /* Cross-frame scripting and click-jacking */
   		    header('X-FRAME-OPTIONS: DENY', TRUE);
   		    header("Content-Security-Policy: frame-ancestors 'none';", TRUE);

   			/* Content sniffing */
   			header("X-Content-Type-Options: nosniff", TRUE);

   			/* Referrer Policy */
   			header("Referrer-Policy: no-referrer", TRUE);

   			/* Server version banners */
   			header_remove("X-Powered-By");
   			header_remove("Server");

   		break;
   	}// end switch
	/* ------------------------------------------
    * END Security Headers (Modern Browsers)
    * ------------------------------------------ */

   	/* ------------------------------------------
   	 * Set the HTTP content-type of this page
   	 * ------------------------------------------ */
   	header("Content-Type: text/html;charset=UTF-8", TRUE);

	/* ------------------------------------------
     * DISPLAY PAGE
     * ------------------------------------------ */

   	/* ------------------------------------------
    * "PAGE" VARIABLE INJECTION
    * ------------------------------------------ */
   	global $lPage;
   	$lPage = __ROOT__.'/home.php';
	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   		case "1": // This code is insecure
		    // Get the value of the "page" URL query parameter
		    if (isset($_REQUEST["page"])) {
		    	$lPage = $_REQUEST["page"];
		    }// end if
   		break;

   		case "2":
   		case "3":
   		case "4":
   		case "5": // This code is fairly secure
  			/* To prevent page injection, we start with the basic priciple
  			 * of "DENY ALL". We decide to allow only the characters abosolutely
  			 * neccesary to spell the Mutillidae file names. This requires
  			 * alpha, hyphen, and period.
  			 */
		    // Get the value of the "page" URL query parameter without accepting POST
		    // to prevent method tampering.
		    if (isset($_GET["page"])) {
		    	$lPage = $_GET["page"];
		    }// end if

   			$lPageIsAllowed = (preg_match("/^[a-zA-Z0-9\.\-\/]+[\.php|\.html]$/", $lPage) == 1);
   			if (!$lPageIsAllowed){
		    	$lPage = __ROOT__.'/page-not-found.php';
   			}// end if
   		break;
   	}// end switch
	/* ------------------------------------------
    * END "PAGE" VARIABLE INJECTION
    * ------------------------------------------ */

	/* ------------------------------------------
     * SIMULATE "SECRET" PAGES
     * ------------------------------------------ */
	switch ($lPage){
		case ".htaccess":
		case ".htaccess.php":
		case "secret.php":
   		case "admin.php":
		case "_adm.php":
		case "_admin.php":
		case "root.php":
		case "administrator.php":
		case "auth.php":
		case "hidden.php":
		case "console.php":
		case "conf.php":
		case "_private.php":
		case "private.php":
		case "access.php":
		case "control.php":
		case "control-panel.php":
		case "bash_history":
		case ".history":
		case ".htpasswd":
		case ".htpasswd.php":

   			switch ($_SESSION["security-level"]){
		   		case "0": // This code is insecure
		   		case "1": // This code is insecure
	    			$lPage=__ROOT__.'/phpinfo.php';
		   		break;

		   		case "2":
		   		case "3":
		   		case "4":
		   		case "5": // This code is fairly secure
		  			/* To prevent unauthorized access, we start with the basic priciple
		  			 * of "DENY ALL".
		  			 */
		   			$lUserAuthorized = FALSE;
		   			if(isset($_SESSION['is_admin'])){
		   				if($_SESSION['is_admin'] == 'TRUE'){
		   					$lUserAuthorized = TRUE;
		   				}// end if is_admin
		   			}// end if isseet $_SESSION['is_admin']

		   			if($lUserAuthorized){
		   				$lPage=__ROOT__.'/phpinfo.php';
		   			}else{
		   				$lPage=__ROOT__.'/authorization-required.php';
		   			}// end if $lUserAuthorized

		   		break;//case 5
		   	}// end switch

   		break;
   		default:break;
    }//end switch on page
	/* ------------------------------------------
	* END SIMULATE "SECRET" PAGES
	* ------------------------------------------ */

	/* ------------------------------------------
     * Set Content Security Policy (CSP) if needed
     * ------------------------------------------ */
    if ($lPage == "content-security-policy.php"){
        $lReportToHeader = 'Report-To: {"group": "csp-endpoint", "max_age": 10886400, "endpoints":[{"url": "includes/capture-data.php"}]}';

        $CSPNonce = bin2hex(openssl_random_pseudo_bytes(32));
        $lCSP = "Content-Security-Policy: " .
                "script-src 'self' 'nonce-{$CSPNonce}' mutillidae.local;" .
                "style-src 'unsafe-inline' 'self' mutillidae.local;" .
                "img-src 'self' mutillidae.local www.paypalobjects.com;" .
                "connect-src 'self' mutillidae.local;" .
                "form-action 'self' mutillidae.local;" .
                "font-src 'none';" .
                "frame-src 'self' mutillidae.local;" .
                "media-src 'none';" .
                "object-src 'none';" .
                "default-src 'self';".
                "frame-ancestors 'none';".
                "report-uri includes/capture-data.php;" .
                "report-to csp-endpoint;";

        header($lReportToHeader, TRUE);
        header($lCSP, TRUE);
    }else{
        $CSPNonce = "";
    }// end if

    /* ------------------------------------------
     * END Content Security Policy (CSP)
     * ------------------------------------------ */

	/* ------------------------------------------
	* BEGIN OUTPUT RESPONSE
	* ------------------------------------------ */
	require_once (__ROOT__."/includes/header.php");

	if (strlen($lPage)==0 || !isset($lPage)){
		/* Default Page */
		require_once(__ROOT__."/home.php");
	}else{
		/* All Other Pages */
	    if (file_exists($lPage) || $RemoteFileHandler->remoteSiteIsReachable($lPage)){
			require_once ($lPage);
		}else{
			if(!$RemoteFileHandler->curlIsInstalled()){
				echo $RemoteFileHandler->getNoCurlAdviceBasedOnOperatingSystem();
			}//end if
			require_once (__ROOT__."/page-not-found.php");
		}//end if

	}// end if page variable not set

	require_once (__ROOT__."/includes/information-disclosure-comment.php");
	require_once (__ROOT__."/includes/footer.php");

   	/* ------------------------------------------
   	 * LOG USER VISIT TO PAGE
   	* ------------------------------------------ */
   	include_once (__ROOT__."/includes/log-visit.php");

   	/* ------------------------------------------
   	 * CLOSE DATABASE CONNECTION
   	* ------------------------------------------ */
   	$MySQLHandler->closeDatabaseConnection();

?>
