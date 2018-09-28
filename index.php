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
	require_once (__ROOT__.'/classes/BubbleHintHandler.php');
	require_once (__ROOT__.'/classes/RemoteFileHandler.php');
	require_once (__ROOT__.'/classes/RequiredSoftwareHandler.php');
	
    /* ------------------------------------------
     * INITIALIZE SESSION
     * ------------------------------------------ */
	//initialize session
    if (strlen(session_id()) == 0){
    	session_start();
    }// end if

    // ----------------------------------------
	// initialize security level to "insecure" 
	// ----------------------------------------
    if (!isset($_SESSION['security-level'])){
    	$_SESSION['security-level'] = '0';
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
		
		if ($lProtectCookies){
			setcookie('showhints', $l_showhints.";HTTPOnly");
		}else {
			setcookie('showhints', $l_showhints);
		}// end if $lProtectCookies
	}// end if (isset($_COOKIE["showhints"])){

	if (!isset($_SESSION["showhints"]) || ($_SESSION["showhints"] != $l_showhints)){
		// make session = cookie
		$_SESSION["showhints"] = $l_showhints;
		switch ($l_showhints){
			case 0: $_SESSION["hints-enabled"] = "Disabled (".$l_showhints." - I try harder)"; break;
			case 1: $_SESSION["hints-enabled"] = "Enabled (".$l_showhints." - Try easier)"; break;
		}// end switch
	}//end if
	
	/* ------------------------------------------
	 * initialize OWASP ESAPI for PHP
	 * ------------------------------------------ */
	/*
	if (!is_object($_SESSION["Objects"]["ESAPIHandler"])){
		$_SESSION["Objects"]["ESAPIHandler"] = new ESAPI(__ROOT__.'/owasp-esapi-php/src/ESAPI.xml');
		$_SESSION["Objects"]["ESAPIEncoder"] = $_SESSION["Objects"]["ESAPIHandler"]->getEncoder();
		$_SESSION["Objects"]["ESAPIRandomizer"] = $_SESSION["Objects"]["ESAPIHandler"]->getRandomizer();
	}// end if
	
	// Set up an alias by reference so object can be referenced in memory without copying
	$ESAPI = &$_SESSION["Objects"]["ESAPIHandler"];
	$Encoder = &$_SESSION["Objects"]["ESAPIEncoder"];
	$ESAPIRandomizer = &$_SESSION["Objects"]["ESAPIRandomizer"];
	*/
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
	/*
	if (!is_object($_SESSION["Objects"]["CustomErrorHandler"])){
		$_SESSION["Objects"]["CustomErrorHandler"] = new CustomErrorHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	}// end if
	
	$CustomErrorHandler = &$_SESSION["Objects"]["CustomErrorHandler"];
	*/
	$CustomErrorHandler = new CustomErrorHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	
	/* ------------------------------------------
 	* initialize log handler
 	* ------------------------------------------ */
	/*
	if (!is_object($_SESSION["Objects"]["LogHandler"])){
		$_SESSION["Objects"]["LogHandler"] = new LogHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	}// end if
	
	$LogHandler = &$_SESSION["Objects"]["LogHandler"];
	*/
	$LogHandler = new LogHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);	
		
	/* ------------------------------------------
 	* initialize MySQL handler
 	* ------------------------------------------ */
	/*
	if (!is_object($_SESSION["Objects"]["MySQLHandler"])){
		$_SESSION["Objects"]["MySQLHandler"] = new MySQLHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	}// end if
	
	$MySQLHandler = &$_SESSION["Objects"]["MySQLHandler"];
	*/
	$MySQLHandler = new MySQLHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	$MySQLHandler->connectToDefaultDatabase();

	/* ------------------------------------------
 	* initialize SQL Query handler
 	* ------------------------------------------ */
	/*
	if (!is_object($_SESSION["Objects"]["SQLQueryHandler"])){
		$_SESSION["Objects"]["SQLQueryHandler"] = new SQLQueryHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	}// end if
	
	$SQLQueryHandler = &$_SESSION["Objects"]["SQLQueryHandler"];
	*/
	$SQLQueryHandler = new SQLQueryHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	
	/* ------------------------------------------
 	* initialize balloon-hint handler
 	* ------------------------------------------ */
	/*
   	if (!is_object($_SESSION["Objects"]["BubbleHintHandler"])){
		$_SESSION["Objects"]["BubbleHintHandler"] = new BubbleHintHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	}// end if
	
	// Set up an alias by reference so object can be referenced in memory without copying
	$BubbleHintHandler = &$_SESSION["Objects"]["BubbleHintHandler"];
	*/
	$BubbleHintHandler = new BubbleHintHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
	
	if ($_SESSION["showhints"] != $BubbleHintHandler->getHintLevel()){
		$BubbleHintHandler->setHintLevel($_SESSION["showhints"]);
	}//end if

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
		include_once(__ROOT__.'/process-commands.php');
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
   						header('Logged-In-User: '.$_SESSION['logged_in_user'], true);
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
   				header('Logged-In-User: '.$_SESSION['logged_in_user'], true);
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
   			header('Logged-In-User: '.$Encoder->encodeForHTML($_SESSION['logged_in_user']), TRUE);
   		break;
   	}// end switch
	/* ------------------------------------------
     * END REACT TO CLIENT SIDE CHANGES
     * ------------------------------------------ */

   	/* ------------------------------------------
   	 * PHP Version Detection
   	 * ------------------------------------------ */
   	try{
   	    /*
   	     * This section detects if the header_remove() function should
   	     * be supported. PHP 5.3 first includes this function.
   	     */
   	    $l_header_remove_supported = FALSE;
   	    $l_phpversion = explode(".", phpversion());
   	    $l_phpmajorversion = (int)$l_phpversion[0];
   	    $l_phpminorversion = (int)$l_phpversion[1];
   	    if (($l_phpmajorversion >= 5 && $l_phpminorversion >= 3) || $l_phpmajorversion > 5){
   	        $l_header_remove_supported = TRUE;
   	    }else{
   	        $l_header_remove_supported = FALSE;
   	    }// end if
   	} catch (Exception $e) {
   	    //Bummer: Not sure if we have support
   	    $l_header_remove_supported = FALSE;
   	}// end try
   	
   	/* ------------------------------------------
    * Security Headers (Modern Browsers)
    * ------------------------------------------ */

   	/* If not security level 5, try to get rid of cache-control */
   	if ($_SESSION["security-level"] < 5) {
   	    
   	    try{
   	        /*
   	         * This section is the cache-control. This only works in PHP 5.3
   	         * and higher due to the header_remove function becoming
   	         * available at that time.
   	         */
   	        if ($l_header_remove_supported){
   	            /* Try to get rid of expires, last-modified, Pragma,
   	             * cache control header, HTTP/1.1 and cookie cache control
   	             * that would be created if the user
   	             * enabled security level 5.
   	             */
   	            header_remove("Expires");
   	            header_remove("Last-Modified");
   	            header_remove("Cache-Control");
   	            header_remove("Pragma");
   	        }else{
   	            /* Try to get rid of expires, last-modified, Pragma,
   	             * cache control header, HTTP/1.1 and cookie cache control
   	             * that would be created if the user
   	             * enabled security level 5.
   	             */
   	            /*This line causes severe issues with the toggle security and toggle hints.
   	             DO NOT uncomment until a patch is found.
   	             header("Expires: Mon, 26 Jul 2050 05:00:00 GMT", TRUE);
   	             */
   	            header("Last-Modified: Mon, 26 Jul 2050 05:00:00 GMT", TRUE);
   	            header('Cache-Control: public', TRUE);
   	            header("Pragma: public", TRUE);
   	        }// end if
   	    } catch (Exception $e) {
   	        //Bummer: The cahce-control exercise are not working
   	    }// end try
   	    
    }//end if
   	
	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   			$lIncludeFrameBustingJavaScript = FALSE;
   			
   			/* Built-in user-agent defenses */
   			header("X-XSS-Protection: 0", TRUE);
   			
   			/* Disable HSTS */
   			header("Strict-Transport-Security: max-age=0", TRUE);
   			
   		break;
   		
   		case "1":
   		    /* Cross-frame scripting and click-jacking */
   		    $lIncludeFrameBustingJavaScript = TRUE;
   		    
   		    /* Built-in user-agent defenses */
   		    header("X-XSS-Protection: 0", TRUE);
   		    
			/* Disable HSTS */
			header("Strict-Transport-Security: max-age=0", TRUE);
			
		break;

   		case "2":
   		case "3":
   		case "4":
   		case "5": // This code is fairly secure
   		    
   		    /* Cross-frame scripting and click-jacking */
  			/* To prevent click-jacking and IE6 key-log-via-framing issue
  			 * we can instruct the browser that it should not frame our site. 
  			 * Unfortuneately only the latest browsers support this option.
  			 * There are javascript frame-buster options that work reasonably well
  			 * although the arms race continues. Use the x-frame-options as the
  			 * primary defense and include the javascript frame-buster to help
  			 * with older browsers.
  			 */
   			header('X-FRAME-OPTIONS: DENY', TRUE);
   			$lIncludeFrameBustingJavaScript = TRUE;
   			
   			/* Cache-control */
   			/*
   			 * Forms caching:
   			 * In insecure mode, we do nothing (as is often the case with insecure mode)
   			 * In secure mode, we set the proper caching directives to help prevent client side caching
   			 *
   			 * When the browser caches the information asset just walked out the door. HTML 5 combined
   			 * with naive developers is going to make this problem much worse. HTML 5 includes advanced
   			 * cookies called "offline" storage or "DOM" storage. This is going to be a nightmare
   			 * for enterprises to protect their data from leakage.
   			 */
   			// Expires: past date tells browser that file is out of date
   			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT", TRUE);
   			
   			// Always modified - Tells browser that new content available
   			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT", TRUE);
   			
   			// HTTP/1.1 and cookie cache control
   			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, no-cache="set-cookie"', TRUE);
   			
   			// HTTP/1.0 cache-control
   			header("Pragma: no-cache", TRUE);

   			/* Content sniffing */
   			header("X-Content-Type-Options: nosniff", TRUE);
   			
   			/* Built-in user-agent defenses */
   			header("X-XSS-Protection: 1", TRUE);

   			/* Enable HSTS */
   			//header("Strict-Transport-Security: max-age=31536000; includeSubDomains", TRUE);
   			
   			/* Server version banners */
   			try{
   			    /*
   			     * Remove x-powered-by header and server header for security.
   			     * Server is hard to get rid of without modifying the Apache config because Apache
   			     * adds the header after the PHP has already been rendered and sent to Apache,
   			     * but atleast we discussed it here.
   			     */
   			    if ($l_header_remove_supported){
   			        header_remove("X-Powered-By");
   			        header_remove("Server");
   			    }else{
   			        /* Try to get rid of expires, last-modified, Pragma,
   			         * cache control header, HTTP/1.1 and cookie cache control
   			         * that would be created if the user
   			         * enabled security level 5. Server is often over-ridden
   			         * by Apache no matter what we do. Change Apache config to fix.
   			         */
   			        header("X-Powered-By: Ming Industries Draconian Power Ring", TRUE);
   			        header("Server: Kentucky Wildcat Basketball", TRUE);
   			    }// end if
   			} catch (Exception $e) {
   			    //Bummer: The server blabbermouth defense is not working
   			}// end try
   			
   		break;
   	}// end switch
	/* ------------------------------------------
    * END Security Headers (Modern Browsers)
    * ------------------------------------------ */
	 
   	/* ------------------------------------------
   	 * Set the HTTP content-type of this page
   	 * ------------------------------------------ */
   	header("Content-Type: text/html", TRUE);
   	
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
   		case "admin.php":		case "_adm.php":		case "_admin.php":		case "root.php":		case "administrator.php":
		case "auth.php":		case "hidden.php":		case "console.php":		case "conf.php":		case "_private.php":		case "private.php":		case "access.php":		case "control.php":		case "control-panel.php":		case "bash_history":		case ".history":		case ".htpasswd":
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
	* BEGIN OUTPUT RESPONSE
	* ------------------------------------------ */
	require_once (__ROOT__."/includes/header.php");
	
	if (strlen($lPage)==0 || !isset($lPage)){
		/* Default Page */
		require_once(__ROOT__."/home.php");
	}else{
		/* All Other Pages */

		/* Note: PHP uses lazy evaluation so if file_exists then PHP wont execute remote_file_exists */
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

	/* ------------------------------------------
	* Anti-framing protection (Older Browsers)
	* ------------------------------------------ */
	if ($lIncludeFrameBustingJavaScript){
		include_once (__ROOT__."/includes/anti-framing-protection.inc");	
	}// end if    
    
	/* ------------------------------------------
	* Add javascript includes
	* ------------------------------------------ */
   	include_once (__ROOT__."/includes/create-html-5-web-storage-target.inc");	
   	require_once (__ROOT__."/includes/jquery-init.inc");
   	
   	if (isset($_GET["popUpNotificationCode"])){
   		include_once (__ROOT__."/includes/pop-up-status-notification.inc");
   	}// end if

?>