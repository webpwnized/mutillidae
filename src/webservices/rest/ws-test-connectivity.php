<?php
	/*  --------------------------------
	 *  We use the session on this page
	 *  --------------------------------*/
    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }// end if

	/* ----------------------------------------
	 *	initialize security level to "insecure"
	 * ----------------------------------------*/
    if (!isset($_SESSION["security-level"])){
        $_SESSION["security-level"] = 0;
    }// end if

	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	require_once '../../includes/constants.php';
	require_once '../../includes/minimum-class-definitions.php';

	try{
		$lSuccessMessage = "Connection succeeded...";
        echo $lSuccessMessage;
	} catch (Exception $e) {
		$lErrorMessage = "Unable to process request to web service ws-test-connectivity";
		echo $CustomErrorHandler->FormatErrorJSON($e, $lErrorMessage);
	}// end try
?>
