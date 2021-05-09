<?php

    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }// end if

    if(isset($_SESSION["security-level"])){
        $lSecurityLevel = $_SESSION["security-level"];
    }else{
        $lSecurityLevel = 0;
    }

    /* ------------------------------------------
	 * initialize OWASP ESAPI for PHP
	 * ------------------------------------------ */
    require_once (__ROOT__.'/owasp-esapi-php/src/ESAPI.php');
	if (!isset($ESAPI)){
		$ESAPI = new ESAPI((__ROOT__.'/owasp-esapi-php/src/ESAPI.xml'));
		$Encoder = $ESAPI->getEncoder();
	}// end if

	/* ------------------------------------------
	 * initialize custom error handler
	 * ------------------------------------------ */
    require_once (__ROOT__.'/classes/CustomErrorHandler.php');
	if (!isset($CustomErrorHandler)){
		$CustomErrorHandler =
		new CustomErrorHandler(__ROOT__.'/owasp-esapi-php/src/', $lSecurityLevel);
	}// end if

	/* ------------------------------------------
 	* initialize log error handler
 	* ------------------------------------------ */
    require_once (__ROOT__.'/classes/LogHandler.php');
    $LogHandler = new LogHandler(__ROOT__.'/owasp-esapi-php/src/', $lSecurityLevel);

	/* ------------------------------------------
 	* initialize SQL Query Handler
 	* ------------------------------------------ */
	require_once (__ROOT__.'/classes/SQLQueryHandler.php');
	$SQLQueryHandler = new SQLQueryHandler(__ROOT__."/owasp-esapi-php/src/", $lSecurityLevel);
?>