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

	header('Content-Type: application/json');

	try {
		// Handle the GET request to test connectivity
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			// Send a 200 OK status code and JSON response
			http_response_code(200); // OK
			echo json_encode(["message" => "Connection succeeded"]);
		} else {
			// If the request method is not GET, send a 405 Method Not Allowed status code
			http_response_code(405); // Method Not Allowed
			echo json_encode(["error" => "Invalid request method"]);
		}
	} catch (Exception $e) {
		// Handle any exceptions with a 500 Internal Server Error response
		http_response_code(500); // Internal Server Error
		$lErrorMessage = "Unable to process request to web service ws-test-connectivity";
		echo $CustomErrorHandler->FormatErrorJSON($e, $lErrorMessage);
	}
?>
