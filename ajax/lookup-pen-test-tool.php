<?php
	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	include_once('../includes/constants.php');

	/* ------------------------------------------------------
	 * INCLUDE CLASS DEFINITION PRIOR TO INITIALIZING SESSION
	 * ------------------------------------------------------ */
	require_once (__ROOT__ . '/classes/CustomErrorHandler.php');
	require_once (__ROOT__ . '/classes/LogHandler.php');
	require_once (__ROOT__ . '/classes/SQLQueryHandler.php');

    /* ------------------------------------------
     * INITIALIZE SESSION
     * ------------------------------------------ */
	if (session_status() == PHP_SESSION_NONE){
	    session_start();
	}// end if

	/* ------------------------------------------
	 * initialize custom error handler
	 * ------------------------------------------ */
	$CustomErrorHandler = new CustomErrorHandler("../owasp-esapi-php/src/", $_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize log handler
 	* ------------------------------------------ */
	$LogHandler = new LogHandler("../owasp-esapi-php/src/", $_SESSION["security-level"]);

	/* ------------------------------------------
 	* initialize SQLQuery handler
 	* ------------------------------------------ */
	$SQLQueryHandler = new SQLQueryHandler("../owasp-esapi-php/src/", $_SESSION["security-level"]);

	try {
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure.
    			$lUseServerSideValidation = FALSE;
   				$lEncodeOutput = FALSE;
				$lTokenizeAllowedMarkup = FALSE;
				$lProtectAgainstSQLInjection = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
				$lValidateInput = FALSE;
				break;

    		case "1": // This code is insecure.
    			$lUseServerSideValidation = FALSE;
				$lEncodeOutput = FALSE;
				$lTokenizeAllowedMarkup = FALSE;
				$lProtectAgainstSQLInjection = FALSE;
				$lProtectAgainstMethodTampering = FALSE;
				$lValidateInput = FALSE;
			break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lUseServerSideValidation = TRUE;
    			$lProtectAgainstMethodTampering = TRUE;
	  			/*
	  			 * NOTE: Input validation is excellent but not enough. The output must be
	  			 * encoded per context. For example, if output is placed in HTML,
	  			 * then HTML encode it. Blacklisting is a losing proposition. You
	  			 * cannot blacklist everything. The business requirements will usually
	  			 * require allowing dangerous charaters. In the example here, we can
	  			 * validate username but we have to allow special characters in passwords
	  			 * least we force weak passwords. We cannot validate the signature hardly
	  			 * at all. The business requirements for text fields will demand most
	  			 * characters. Output encoding is the answer. Validate what you can, encode it
	  			 * all.
	  			 */
	   			// encode the output following OWASP standards
	   			// this will be HTML encoding because we are outputting data into HTML
				$lEncodeOutput = TRUE;

				/* Business Problem: Sometimes the business requirements define that users
				 * should be allowed to use some HTML  markup. If unneccesary, this is a
				 * bad idea. Output encoding will naturally kill any users attempt to use HTML
				 * in their input, which is exactly why we use output encoding.
				 *
				 * If the business process allows some HTML, then those HTML items are elevated
				 * from "mallicious input" to "direct object refernces" (a resource to be enjoyed).
				 * When we want to restrict a user to using to "direct object refernces" (a
				 * resource to be enjoyed) responsibly, we use mapping. Mapping allows the user
				 * to chose from a "system generated" (that's us programmers) set of tokens
				 * to pick from. We need to assure that the user either chooses one of the tokens
				 * we offer, or our system rejects the request. To put it bluntly, either the user
				 * follows the rules, or their output is encoded. Period.
				 */
				$lTokenizeAllowedMarkup = TRUE;

				/* If we are in secure mode, we need to protect against SQLi */
				$lProtectAgainstSQLInjection = TRUE;

				/* If we are in secure mode, we need to validate input */
				$lValidateInput = TRUE;
			break;
    	}// end switch
	}catch(Exception $e){
		echo $CustomErrorHandler->getExceptionMessage($e, "Error setting up configuration on page pentest-lookup-tool.php");
	}// end try

	//--------------------------------------------------------
	//If the user selected a tool, get the data for that tool
	//--------------------------------------------------------

	if (isset($_POST["ToolID"]) || isset($_REQUEST["ToolID"])){

		try {
			// Initialize an empty JSON response. We will at least return this.
			$lPenTestToolsJSON = '{"query": {"toolIDRequested": "", "penTestTools": []}}';

			if ($lProtectAgainstMethodTampering) {
				$lPostedToolID = $_POST["ToolID"];
			}else{
				$lPostedToolID = $_REQUEST["ToolID"];
			}//end if

			if(empty($lPostedToolID)){
				$lPostedToolID = -1;
			}//end if

			if (!($lPostedToolID == "0923ac83-8b50-4eda-ad81-f1aac6168c5c" || strlen($lPostedToolID) == 0)){

				if ($lPostedToolID != "c84326e4-7487-41d3-91fd-88280828c756"){
					if ($lValidateInput && !is_numeric($lPostedToolID)){
						$lPostedToolID = -1;
					}// end if
				}// end if

				try {
					$qPenTestToolResults = $SQLQueryHandler->getPenTestTool($lPostedToolID);
					$lPenTestToolsDetails = "";
					/* We want to allow single quotes so the user can do SQL injection, but when they return from
					 * the database, we escape the single quotes because they would otherwise break the JSON string. */
					$lPenTestToolsJSON =
					'{"query": {"toolIDRequested": "'.str_replace("'", "\'", $lPostedToolID).'", "penTestTools": [';
						if($qPenTestToolResults->num_rows > 0){
						while($row = $qPenTestToolResults->fetch_object()){
							$lPenTestToolsDetails .= json_encode($row) . ",";
						}// end while
						$lPenTestToolsJSON .= substr($lPenTestToolsDetails, 0, strlen($lPenTestToolsDetails)-1);
					}//end if
					$lPenTestToolsJSON .= ']}}';

				} catch (Exception $e) {
	   				throw (new Exception("Error working with query results"));
				}// end try

			}// end if user didnt pick anything

			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/json');
			echo $lPenTestToolsJSON;
		} catch (Exception $e) {
			echo $CustomErrorHandler->getExceptionMessage($e, $query);
		}// end try

	}// end if isset()

    /* ------------------------------------------
     * LOG USER VISIT TO PAGE
     * ------------------------------------------ */
	include_once ('../includes/log-visit.php');

?>