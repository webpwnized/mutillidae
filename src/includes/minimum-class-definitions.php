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
	 * initialize encoder
	 * ------------------------------------------ */
    require_once __SITE_ROOT__.'/classes/EncodingHandler.php';
    if (!isset($Encoder)){
        $Encoder = new EncodingHandler();
    }// end if

	/* ------------------------------------------
	 * initialize custom error handler
	 * ------------------------------------------ */
    require_once __SITE_ROOT__.'/classes/CustomErrorHandler.php';
	if (!isset($CustomErrorHandler)){
		$CustomErrorHandler =
		new CustomErrorHandler($lSecurityLevel);
	}// end if

	/* ------------------------------------------
 	* initialize log error handler
 	* ------------------------------------------ */
    require_once __SITE_ROOT__.'/classes/LogHandler.php';
    $LogHandler = new LogHandler($lSecurityLevel);

	/* ------------------------------------------
 	* initialize SQL Query Handler
 	* ------------------------------------------ */
	require_once __SITE_ROOT__.'/classes/SQLQueryHandler.php';
	$SQLQueryHandler = new SQLQueryHandler($lSecurityLevel);
?>
